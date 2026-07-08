<?php
declare(strict_types=1);

namespace app\controllers;

use app\components\Util;
use app\models\LoginForm;
use Yii;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller {

  public function behaviors(): array {
    return [
      'access' => [
        'class' => AccessControl::class,
//        'only' => ['logout'],
        'rules' => [
          [
            'allow' => true,
            'actions' => ['error'],
            'roles' => ['?', '@'],
          ],
          [
            'allow' => true,
            'actions' => ['login'],
            'roles' => ['?'],
          ],
          [
            'allow' => true,
            'actions' => ['index', 'logout', 'verificar-recuperacion', 'asistencias'],
            'roles' => ['@'],
          ]
        ],
      ],
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          'logout' => ['post'],
        ],
      ],
    ];
  }

  public function actions(): array {
    return [
      'error' => [
        'class' => ErrorAction::class,
      ],
      'captcha' => [
        'class' => CaptchaAction::class,
        'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
        'transparent' => true,
      ],
    ];
  }

  public function actionIndex(): string {
    return $this->render('index');
  }

  public function actionLogin() {
    if (!Yii::$app->user->isGuest) {
      return $this->goHome();
    }
    $model = new LoginForm(Yii::$app->security);
    if ($model->load($this->request->post()) && $model->login()) {
      return $this->goBack();
    }
    $model->password = '';
    return $this->render('login', ['model' => $model]);
  }

  public function actionLogout(): Response {
    Yii::$app->user->logout();
    return $this->goHome();
  }

  public function actionVerificarRecuperacion($fecha = null): string {
//    echo json_encode(Util::getRecuperaciones($fecha), JSON_PRETTY_PRINT); exit;
    if (!$fecha) {
      $fecha = date('Y-m-d');
    }
    return $this->render('verificarRecuperacion', [
      'fechaHoy' => $fecha,
      'data' => Util::getRecuperaciones($fecha)
    ]);
  }

  public function actionAsistencias($fecha = null): string {
    if (!$fecha) {
      $fecha = date('Y-m-d');
    }
    return $this->render('asistencias', [
      'fechaHoy' => $fecha,
      'data' => Util::getAsistencias($fecha)
    ]);
  }
}