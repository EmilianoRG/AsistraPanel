<?php
namespace app\components\filters;

use app\components\AppException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Yii;
use yii\base\ActionFilter;

class AuthFilter extends ActionFilter {
  public function beforeAction($action): bool {
    try {
      $headers = Yii::$app->request->headers;
//      if (!$headers->has('Authorization')) {
//        throw new AppException('Autorización denegada (header)', 401);
//      }
      $authorization = explode(' ', $headers->get('Authorization'));
      $token = array_pop($authorization);
      $decoded = JWT::decode($token, new Key('+ulbU:aG£B/?5+{[\j>@.0:6rmLt9k9(', 'HS256'));
      if (!$decoded->token_type) {
        throw new AppException('Autorización denegada (token)', 401);
      }
      if ($decoded->token_type !== 'access') {
        throw new AppException('Autorización denegada (no access)', 401);
      }
      // encontrar el usuario (que es un array) desde los params de yii
      $usuarios = Yii::$app->params['usuarios'] ?? [];
      $usuario = null;
      foreach ($usuarios as $u) {
        if ($u['id'] === $decoded->user_id) {
//          $usuario = (object)$u;
          $usuario = $u;
          break;
        }
      }
      if (!$usuario) {
        throw new AppException('Usuario no encontrado', 401);
      }
    } catch (AppException $ex) {
      $action->controller->redirect([
        'data/error',
        'errorMessage' => $ex->errorMessage,
        'httpCode' => $ex->httpCode,
        'errorMessages' => $ex->errorMessages
      ]);
      return false;
    } catch (ExpiredException $ex) {
      $action->controller->redirect([
        'data/error',
        'errorMessage' => 'El token ha expirado',
        'httpCode' => 401
      ]);
      return false;
    } catch (SignatureInvalidException $ex) {
      $action->controller->redirect([
        'data/error',
        'errorMessage' => 'La firma del token es inválida',
        'httpCode' => 401
      ]);
      return false;
    } catch (\Exception $ex) {
      $action->controller->redirect([
        'data/error',
        'errorMessage' => $ex->getMessage(),
        'httpCode' => 500
      ]);
      return false;
    }
    return true;
  }

  public function beforeActionBackup($action): bool {
    try {
      $headers = Yii::$app->request->headers;
      if (!$headers->has('X-Api-Key')) {
        throw new AppException('Autorización Denegada (header)', 401);
      }
      $apikey = (string)$headers->get('X-Api-Key');

    } catch (AppException $ex) {
      $action->controller->redirect([
        'data/error',
        'errorMessage' => $ex->errorMessage,
        'httpCode' => $ex->httpCode,
        'errorMessages' => $ex->errorMessages
      ]);
      return false;
    } catch (\Exception $ex) {
      $action->controller->redirect([
        'data/error',
        'errorMessage' => $ex->getMessage(),
        'httpCode' => 500
      ]);
      return false;
    }
    return true;
  }
}