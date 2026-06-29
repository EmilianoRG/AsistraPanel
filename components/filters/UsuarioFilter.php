<?php
namespace app\components\filters;

use app\controllers\AppController;
use app\models\usuarios\Usuario;
use Yii;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class UsuarioFilter extends ActionFilter {
  public function beforeAction($action): bool {
    if (Yii::$app->user->isGuest) {
      throw new ForbiddenHttpException('No ha iniciado sesión');
    }
    /** @var Usuario $usuario */
    $usuario = Usuario::find()->where(['id' => Yii::$app->user->id, 'status' => true])->one();
    if (!$usuario) {
      throw new ForbiddenHttpException('No ha iniciado sesión');
    }
    /** @var AppController $controller */
    $controller = $action->controller;
    $controller->usuario = $usuario;
    return true;
  }
}