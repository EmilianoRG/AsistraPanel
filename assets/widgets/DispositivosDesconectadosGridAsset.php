<?php
namespace app\assets\widgets;

use app\assets\AppAsset;
use yii\web\AssetBundle;

class DispositivosDesconectadosGridAsset extends AssetBundle {
  public $basePath = '@webroot';
  public $baseUrl = '@web';

  public $css = [
    'css/widgets/dispositivosdesconectadosgrid.css'
  ];

  public $depends = [
    AppAsset::class
  ];
}
