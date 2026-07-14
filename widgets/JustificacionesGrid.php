<?php
namespace app\widgets;

use app\assets\widgets\JustificacionesGridAsset;
use yii\base\Widget;

class JustificacionesGrid extends Widget {
  public $fechaActual;
  public $data;

  public function init() {
    parent::init();
    JustificacionesGridAsset::register($this->view);
  }

  public function run(): string {
    return '<div class="row g-4" id="cards-container">' . array_reduce($this->data, fn ($carry, $item) => $carry . JustificacionCard::widget(['data' => $item]), '') . '</div>';
  }
}