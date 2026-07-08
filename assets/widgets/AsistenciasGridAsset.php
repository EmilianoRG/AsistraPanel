<?php
namespace app\assets\widgets;

use app\assets\AppAsset;
use yii\web\AssetBundle;

class AsistenciasGridAsset extends AssetBundle {
  public $basePath = '@webroot';
  public $baseUrl = '@web';

  public $css = [
    'css/widgets/asistenciasgrid.css'
  ];

  public $depends = [
    AppAsset::class
  ];
}