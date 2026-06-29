<?php
namespace app\components\filters;

use app\components\AppException;
use Yii;
use yii\base\ActionFilter;

class AuthFilter extends ActionFilter {
  public function beforeAction($action): bool {
    try {
      $headers = Yii::$app->request->headers;
      if (!$headers->has('X-Api-Key')) {
        throw new AppException('Autorización Denegada (header)', 401);
      }
      $apikey = (string)$headers->get('X-Api-Key');




    } catch (AppException $ex) {
      $action->controller->redirect([
        'api/error',
        'errorMessage' => $ex->errorMessage,
        'httpCode' => $ex->httpCode,
        'errorMessages' => $ex->errorMessages
      ]);
      return false;
    } catch (\Exception $ex) {
      $action->controller->redirect([
        'api/error',
        'errorMessage' => $ex->getMessage(),
        'httpCode' => 500
      ]);
      return false;
    }
    return true;
  }
}