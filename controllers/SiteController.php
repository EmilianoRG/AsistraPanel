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
            'actions' => ['index', 'logout', 'verificar-recuperacion'],
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

  public function actionLogin(): Response|string {
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

  public function actionVerificarRecuperacion(): string {
    return $this->render('verificarRecuperacion');
//    return json_encode(Util::getRecuperaciones('2026-04-14'), JSON_PRETTY_PRINT); exit;
  }
}

/*
[
{
"Institucion": "2sis Background",
"BD": "checatec_2sis_background",
"Fecha": "2026-04-14",
"Cantidad_Personal": 13,
"Cantidad_Personal_Recuperacion_Incompleta": 0
}
]
*/