<?php
namespace app\assets\widgets;

use app\assets\AppAsset;
use yii\web\AssetBundle;

class RecuperacionesGridAsset extends AssetBundle {
  public $basePath = '@webroot';
  public $baseUrl = '@web';

  public $css = [
    'css/widgets/recuperacionesgrid.css'
  ];

  public $depends = [
    AppAsset::class
  ];
}