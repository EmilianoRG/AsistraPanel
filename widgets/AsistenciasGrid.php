<?php
namespace app\widgets;

use app\assets\widgets\AsistenciasGridAsset;
use yii\base\Widget;

class AsistenciasGrid extends Widget {
  public $fechaActual;
  public $data;

  public function init() {
    parent::init();
    AsistenciasGridAsset::register($this->view);
  }

  public function run(): string {
    return '<div class="row g-4" id="cards-container">' . array_reduce($this->data, fn ($carry, $item) => $carry . AsistenciaGrid::widget(['data' => $item]), '') . '</div>';
  }
}