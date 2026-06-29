<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

$items = [
  [
    'label' => 'Acciones',
    'url' => ['/site/index'],
  ],
  [
    'label' => 'Login',
    'url' => ['/site/login'],
    'visible' => Yii::$app->user->isGuest,
  ],
  [
    'label' => 'Cerrar Sesión',
    'url' => ['/site/logout'],
    'linkOptions' => [
      'data-method' => 'post',
      'class' => 'nav-link logout',
    ],
    'visible' => !Yii::$app->user->isGuest,
  ],
];

?>
<header id="header">
  <?php NavBar::begin(
    [
      'brandLabel' => 'Panel Asistra',
      'brandUrl' => Yii::$app->homeUrl,
      'options' => [
        'class' => 'navbar-expand-md navbar-dark fixed-top', // bg-dark
        'style' => 'background-color: #0F172A;',
      ]
    ],
  ) ?>
  <?= Nav::widget(
    [
      'options' => ['class' => 'navbar-nav me-auto'],
      'encodeLabels' => false,
      'items' => $items,
    ],
  ) ?>
<!--  --><?php //= Html::button(
//    '&#127769;',
//    [
//      'id' => 'theme-toggle',
//      'class' => 'btn btn-link nav-link fs-5',
//      'aria-label' => 'Switch to dark mode',
//    ],
//  ) ?>
  <?php NavBar::end() ?>
</header>
