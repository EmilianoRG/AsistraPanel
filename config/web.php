<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
  'id' => 'basic',
  'basePath' => dirname(__DIR__),
  'bootstrap' => ['log'],
  'container' => [
    'singletons' => [
      \yii\mail\MailerInterface::class => [
        'class' => \yii\symfonymailer\Mailer::class,
        // send all mails to a file by default.
        'useFileTransport' => true,
        'viewPath' => '@app/mail',
      ],
    ],
  ],
  'aliases' => [
    '@bower' => '@vendor/bower-asset',
    '@npm' => '@vendor/npm-asset',
  ],
  'components' => [
    'request' => [
      // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
      'cookieValidationKey' => '6xm6p_DevZ0bMelyeSTTw4RGpOQl51bn',
      // parse JSON requests automatically
      'parsers' => [
        'application/json' => \yii\web\JsonParser::class,
      ],
    ],
    'cache' => [
      'class' => \yii\caching\FileCache::class,
    ],
    'user' => [
      'identityClass' => \app\models\User::class,
      'enableAutoLogin' => true,
    ],
    'errorHandler' => [
      'errorAction' => 'site/error',
    ],
    'mailer' => \yii\mail\MailerInterface::class,
    'log' => [
      'traceLevel' => YII_DEBUG ? 3 : 0,
      'targets' => [
        [
          'class' => \yii\log\FileTarget::class,
          'levels' => ['error', 'warning'],
        ],
      ],
    ],
    'db' => $db,
//        'response' => [
//            'format' => \yii\web\Response::FORMAT_JSON,
//            'charset' => 'UTF-8',
//        ],
    'urlManager' => [
      'enablePrettyUrl' => true,
      'showScriptName' => false,
      'enableStrictParsing' => false,
      'rules' => [
        // Recommended modern endpoints: versioned auth endpoints
        // /api/v1/auth/<action>  -> ApiController::<action>
        'POST api/v1/auth/<action:\\w+>' => 'api/<action>',
        // back-compat: /api/auth/login
        'POST api/auth/<action:\\w+>' => 'api/<action>',
//                // short form for older clients: /api/<action>
        'POST api/<action:\\w+>' => 'api/<action>',
        // generic controller/action rule
        '<controller:\\w+>/<action:\\w+>' => '<controller>/<action>',
      ],
    ],
    /*
    'urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => [
        ],
    ],
    */
    'assetManager' => [
      'appendTimestamp' => true,
    ],
  ],
  'params' => $params,
];

if (YII_ENV_DEV) {
  // configuration adjustments for 'dev' environment
  $config['bootstrap'][] = 'debug';
  $config['modules']['debug'] = [
    'class' => \yii\debug\Module::class,
    // uncomment the following to add your IP if you are not connecting from localhost.
    //'allowedIPs' => ['127.0.0.1', '::1'],
  ];

  $config['bootstrap'][] = 'gii';
  $config['modules']['gii'] = [
    'class' => \yii\gii\Module::class,
    // uncomment the following to add your IP if you are not connecting from localhost.
    //'allowedIPs' => ['127.0.0.1', '::1'],
  ];
}

return $config;
