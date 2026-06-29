<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use yii\filters\VerbFilter;
use yii\filters\Cors;
use app\components\JwtHelper;
use app\components\InstanceConnector;
use app\models\ApiUser;
use app\models\ExternalInstance;

class ApiController extends Controller {
  public $enableCsrfValidation = false; // API recibe JSON

  public function behaviors(): array
  {
    return array_merge(parent::behaviors(), [
      // handle CORS preflight and headers; runs before VerbFilter
      'cors' => [
        'class' => Cors::class,
        'cors' => [
          'Origin' => ['*'],
          'Access-Control-Request-Method' => ['GET', 'POST', 'OPTIONS'],
          'Access-Control-Allow-Credentials' => true,
          'Access-Control-Max-Age' => 86400,
        ],
      ],
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          // only POST allowed for login (also accept OPTIONS for preflight)
          'login' => ['POST', 'OPTIONS'],
          // only GET allowed for fetch (also accept OPTIONS for preflight)
          'fetch' => ['GET', 'OPTIONS'],
        ],
      ],
    ]);
  }

  public function beforeAction($action): bool {
    Yii::$app->response->format = Response::FORMAT_JSON;
    return parent::beforeAction($action);
  }

  // POST /index.php?r=api/login
  public function actionLogin(): array {
    $body = Yii::$app->request->getBodyParams();
    if (empty($body['username']) || empty($body['password'])) {
      throw new BadRequestHttpException('username and password required');
    }

    $username = $body['username'];
    $password = $body['password'];

    $user = ApiUser::find()->where(['username' => $username])->one();

    if (!$user || !password_verify($password, $user->password_hash)) {
      throw new UnauthorizedHttpException('invalid credentials');
    }

    $token = JwtHelper::generateToken((int)$user->id, $user->username);

    return [
      'status' => 'ok',
      'token' => $token,
      'expires_in' => JwtHelper::getTtlSeconds(),
    ];
  }

  // GET /index.php?r=api/fetch&instance_id=1
  public function actionFetch() {
    $this->authenticateRequest();

    $instanceId = Yii::$app->request->get('instance_id');
    if (!$instanceId) {
      throw new BadRequestHttpException('instance_id is required');
    }

    $instance = ExternalInstance::findOne((int)$instanceId);
    if (!$instance) {
      throw new BadRequestHttpException('instance not found');
    }

    $connector = new InstanceConnector();
    // InstanceConnector can accept the instance id; it will also read DB if needed.
    $result = $connector->fetchData((int)$instance->id);

    return [
      'status' => 'ok',
      'data' => $result,
    ];
  }

  protected function authenticateRequest() {
    $headers = Yii::$app->request->getHeaders();
    $auth = $headers->get('Authorization');
    if (!$auth) {
      throw new UnauthorizedHttpException('Missing Authorization header');
    }

    if (stripos($auth, 'Bearer ') !== 0) {
      throw new UnauthorizedHttpException('Invalid Authorization header');
    }

    $token = trim(substr($auth, 7));
    $payload = JwtHelper::validateToken($token);
    if (!$payload) {
      throw new UnauthorizedHttpException('Invalid or expired token');
    }

    $user = ApiUser::findOne((int)$payload->sub);
    if ($user) {
      // avoid setting Yii::$app->user identity if ApiUser does not implement IdentityInterface
      Yii::$app->params['apiUser'] = $user;
    }

    return $payload;
  }
}

// New simple API controller that accepts a fixed API key instead of JWT/OAuth.
// Note: Windows filesystem is case-insensitive; we place this class in the same
// file to avoid filename conflicts while providing a distinct controller name
// "APIController" as requested.
class APIControllerxx extends Controller {
    public $enableCsrfValidation = false; // API receives JSON / query params

    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'verify-recovery' => ['GET', 'OPTIONS'],
                    'checkins-today' => ['GET', 'OPTIONS'],
                    'projects' => ['GET', 'OPTIONS'],
                    'index' => ['GET', 'OPTIONS'],
                ],
            ],
        ]);
    }

    public function beforeAction($action): bool
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    protected function authenticateByApiKey(): void
    {
        $headers = \Yii::$app->request->getHeaders();
        $provided = null;

        // accept X-Api-Key header or apikey query parameter
        if ($headers->has('X-Api-Key')) {
            $provided = $headers->get('X-Api-Key');
        } elseif (\Yii::$app->request->get('apikey')) {
            $provided = (string)\Yii::$app->request->get('apikey');
        }

        $expected = \Yii::$app->params['fixedApiKey'] ?? null;
        if (!$expected || !$provided || !hash_equals((string)$expected, (string)$provided)) {
            throw new \yii\web\UnauthorizedHttpException('Invalid or missing API key');
        }
    }

    // GET /index.php?r=API/index
    public function actionIndex(): array
    {
        // simple listing of available endpoints for documentation
        return [
            'status' => 'ok',
            'endpoints' => [
                'GET index' => '/index.php?r=API/index',
                'GET projects' => '/index.php?r=API/projects',
                'GET verify-recovery' => '/index.php?r=API/verify-recovery&project_id=ID',
                'GET checkins-today' => '/index.php?r=API/checkins-today&project_id=ID',
            ],
            'auth' => 'Provide X-Api-Key header or ?apikey=... with the fixed API key',
        ];
    }

    // GET /index.php?r=API/projects
    public function actionProjects(): array
    {
        $this->authenticateByApiKey();
        // TODO: implement listing of projects (technologicos)
        return [
            'status' => 'not_implemented',
            'message' => 'projects endpoint not implemented yet',
        ];
    }

    // GET /index.php?r=API/verify-recovery&project_id=1
    public function actionVerifyRecovery(): array
    {
        $this->authenticateByApiKey();
        $projectId = \Yii::$app->request->get('project_id');
        if (!$projectId) {
            return ['status' => 'error', 'message' => 'project_id required'];
        }

        // TODO: implement verification logic for each project
        return [
            'status' => 'not_implemented',
            'project_id' => $projectId,
            'message' => 'verify-recovery not implemented yet',
        ];
    }

    // GET /index.php?r=API/checkins-today&project_id=1
    public function actionCheckinsToday(): array
    {
        $this->authenticateByApiKey();
        $projectId = \Yii::$app->request->get('project_id');
        if (!$projectId) {
            return ['status' => 'error', 'message' => 'project_id required'];
        }

        // TODO: implement checkins fetching logic
        return [
            'status' => 'not_implemented',
            'project_id' => $projectId,
            'message' => 'checkins-today not implemented yet',
        ];
    }
}

