<?php
namespace app\controllers;

use app\components\filters\AuthFilter;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class DataController extends ActiveController {
  public $modelClass = 'app\models\User';

  public function behaviors(): array {
    return ArrayHelper::merge(parent::behaviors(), [
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          'login' => ['post'],
        ]
      ],
      'auth' => [
        'class' => AuthFilter::class,
        'only' => [
          'login'
        ]
      ]
    ]);
  }
}