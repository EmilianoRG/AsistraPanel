<?php
namespace app\assets\widgets;

use app\assets\AppAsset;
use yii\web\AssetBundle;

class JustificacionesGridAsset extends AssetBundle {
  public $basePath = '@webroot';
  public $baseUrl = '@web';

  public $css = [
    'css/widgets/justificacionesgrid.css'
  ];

  public $depends = [
    AppAsset::class
  ];
}