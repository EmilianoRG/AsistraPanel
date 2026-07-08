<?php
namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\View;
use yii\web\YiiAsset;

class AppAsset extends AssetBundle {
  public $basePath = '@webroot';
  public $baseUrl = '@web';
  public $css = [
    // Font Awesome 6
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    // Bootstrap Icons
    [
      'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css',
      'rel' => 'stylesheet'
    ],
    // Google Fonts (Es importante pasar las opciones correctas para fuentes externas)
    [
      'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap',
      'rel' => 'stylesheet'
    ],
    'css/site.css',
    'css/gemini.css',
  ];
  public $js = [
    'js/color-mode.js',
  ];
  public $jsOptions = [
    'position' => View::POS_HEAD,
  ];
  public $depends = [
    YiiAsset::class,
    BootstrapAsset::class,
  ];
}