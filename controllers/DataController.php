<?php
namespace app\controllers;

use app\components\AppException;
use app\components\filters\AuthFilter;
use app\components\Util;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\base\DynamicModel;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Response;

class DataController extends ActiveController {
  public $modelClass = 'app\models\User';

  public function behaviors(): array {
    return ArrayHelper::merge(parent::behaviors(), [
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          'generar-token' => ['post'],
          'get-info' => ['get'],
          'get-dispositivos-status' => ['get'],
        ]
      ],
      'auth' => [
        'class' => AuthFilter::class,
        'only' => [
          'get-info',
          'get-dispositivos-status',
        ]
      ]
    ]);
  }

  public function actionGetDispositivosStatus() {
    Yii::$app->response->format = Response::FORMAT_JSON;
    try {
      $model = new DynamicModel(['tecnologicoId', 'onlyDisconnected']);
      $model->addRule(['tecnologicoId'], 'required');
      $model->addRule(['tecnologicoId'], 'string');
      $model->addRule(['onlyDisconnected'], 'safe');
      if (!($model->load(Yii::$app->request->get(), '') && $model->validate())) {
        $appException = new AppException('Parámetros inválidos', 400);
        foreach ($model->getErrors() as $attribute => $errors) {
          foreach ($errors as $error) {
            $appException->errorMessages[] = "{$attribute}: {$error}";
          }
        }
        throw $appException;
      }

      $proyecto = Util::getProyectoById((string)$model->tecnologicoId);
      if (!$proyecto) {
        throw new AppException('No existe un tecnológico con el id proporcionado.', 404);
      }
      if (empty($proyecto['url'])) {
        throw new AppException('El tecnológico no tiene configurada la URL del endpoint.', 500);
      }

      $query = [];
      $onlyDisconnected = Yii::$app->request->get('onlyDisconnected', null);
      if ($onlyDisconnected !== null && $onlyDisconnected !== '') {
        $query['onlyDisconnected'] = $onlyDisconnected;
      }

      $url = 'https://controldeasistenciastec.com/' . $proyecto['url'] . '/web/api/dispositivos/status';
      $client = new Client([
        'timeout' => 20,
        'connect_timeout' => 10,
        'http_errors' => false,
      ]);
      $response = $client->request('GET', $url, ['query' => $query]);

      Yii::$app->response->statusCode = $response->getStatusCode();
      $responseBody = (string)$response->getBody();
      $decoded = json_decode($responseBody, true);
      if (json_last_error() !== JSON_ERROR_NONE) {
        return ['raw' => $responseBody];
      }
      return $decoded;
    } catch (AppException $ex) {
      Yii::$app->response->statusCode = $ex->httpCode;
      return $ex->toArray();
    } catch (GuzzleException $ex) {
      Yii::$app->response->statusCode = 502;
      return ['errorMessage' => $ex->getMessage()];
    } catch (\Exception $ex) {
      Yii::$app->response->statusCode = 500;
      return ['errorMessage' => $ex->getMessage()];
    }
  }

  public function actionGetInfo() {
    Yii::$app->response->format = Response::FORMAT_JSON;
    try {
      $model = new DynamicModel(['tecnologicoId', 'fecha', 'fecha_inicio', 'fecha_fin']);
      $model->addRule(['tecnologicoId'], 'required');
      $model->addRule(['tecnologicoId'], 'string');
      $model->addRule(['fecha', 'fecha_inicio', 'fecha_fin'], 'string');
      $model->addRule(['fecha', 'fecha_inicio', 'fecha_fin'], 'match', [
        'pattern' => '/^\d{4}-\d{2}-\d{2}$/',
        'message' => 'Fecha no válida. El formato debe ser YYYY-MM-DD.'
      ]);
      if (!($model->load(Yii::$app->request->get(), '') && $model->validate())) {
        $appException = new AppException('Parámetros inválidos', 400);
        foreach ($model->getErrors() as $attribute => $errors) {
          foreach ($errors as $error) {
            $appException->errorMessages[] = "{$attribute}: {$error}";
          }
        }
        throw $appException;
      }

      $fecha = trim((string)$model->fecha);
      $fechaInicio = trim((string)$model->fecha_inicio);
      $fechaFin = trim((string)$model->fecha_fin);

      $tieneFecha = $fecha !== '';
      $tieneRango = $fechaInicio !== '' || $fechaFin !== '';

      if ($tieneFecha && $tieneRango) {
        throw new AppException('Parámetros inválidos. Usa solo "fecha" o el par "fecha_inicio" y "fecha_fin".', 400);
      }
      if (!$tieneFecha && !$tieneRango) {
        throw new AppException('Parámetros inválidos. Debes enviar "fecha" o "fecha_inicio" y "fecha_fin".', 400);
      }
      if ($tieneRango && ($fechaInicio === '' || $fechaFin === '')) {
        throw new AppException('Parámetros inválidos. Para rango debes enviar ambos: "fecha_inicio" y "fecha_fin".', 400);
      }
      if ($tieneRango && strtotime($fechaInicio) > strtotime($fechaFin)) {
        throw new AppException('Parámetros inválidos. "fecha_inicio" no puede ser mayor que "fecha_fin".', 400);
      }

      $consultaInicio = $tieneFecha ? $fecha : $fechaInicio;
      $consultaFin = $tieneFecha ? $fecha : $fechaFin;

      $resumen = Util::getResumenTecnologico($model->tecnologicoId, $consultaInicio, $consultaFin);
      if (isset($resumen['errorMessage'])) {
        if ($resumen['errorMessage'] === 'No existe un tecnológico con el id proporcionado.') {
          throw new AppException($resumen['errorMessage'], 404);
        }
        throw new AppException($resumen['errorMessage'], 500);
      }

      return $resumen;
    } catch (AppException $ex) {
      Yii::$app->response->statusCode = $ex->httpCode;
      return $ex->toArray();
    } catch (\Exception $ex) {
      Yii::$app->response->statusCode = 500;
      return ['errorMessage' => $ex->getMessage()];
    }
  }

  public function actionGenerarToken() {
    Yii::$app->response->format = Response::FORMAT_JSON;
    try {
      // <editor-fold defaultstate="collapsed" desc="Variables y Validacion">

      $model = new DynamicModel(['username', 'password']);
      $model->addRule(['username', 'password'], 'required');
      $model->addRule(['username', 'password'], 'string');
      if (!($model->load(Yii::$app->request->post(), '') && $model->validate())) {
        if ($model->hasErrors()) {
          $appException = new AppException('Error de autenticación', 401);
          foreach ($model->getErrors() as $attribute => $errors) {
            foreach ($errors as $error) {
              $appException->errorMessages[] = "{$attribute}: {$error}";
            }
          }
          throw $appException;
        }
      }
      // encontrar el usuario en params['usuarios']
      $usuarios = Yii::$app->params['usuarios'] ?? [];
      $usuario = null;
      foreach ($usuarios as $user) {
        if ($user['username'] === $model->username) {
          $usuario = $user;
          break;
        }
      }
      if (!$usuario) {
        throw new AppException('Usuario o contraseña incorrectos', 401);
      }
      if (!Yii::$app->security->validatePassword($model->password, $usuario['passwordHash'])) {
        throw new AppException('Usuario o contraseña incorrectos', 401);
      }
//      $password = password_hash($model->password, PASSWORD_DEFAULT);

      // </editor-fold>

      $iat = time(); // Fecha de emisión (Issued At)
      $accessToken = JWT::encode([
        'user_id' => $usuario['id'],
        'username' => $usuario['username'],
        'token_type' => 'access',
        'iat' => $iat,
      ], '+ulbU:aG£B/?5+{[\j>@.0:6rmLt9k9(', 'HS256');
      return [
        'access_token' => $accessToken,
        'issued_at' => $iat,
      ];
    } catch (AppException $ex) {
      Yii::$app->response->statusCode = $ex->httpCode;
      return $ex->toArray();
    } catch (\Exception $ex) {
      Yii::$app->response->statusCode = 500;
      return ['errorMessage' => $ex->getMessage()];
    }
  }

  public function actionError($errorMessage, $httpCode, $errorMessages = []): array {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $appException = new AppException($errorMessage, $httpCode);
    if ($errorMessages) {
      $appException->errorMessages = $errorMessages;
    }
    Yii::$app->response->statusCode = $appException->httpCode;
    return $appException->toArray();
  }
}