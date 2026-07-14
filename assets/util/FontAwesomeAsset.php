<?php
namespace app\assets\util;

use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle {
  public $basePath = '@webroot';
  public $baseUrl = '@web';

  public $css = [
    'css/fontawesomepro-5.15.3/css/all.min.css'
  ];
}