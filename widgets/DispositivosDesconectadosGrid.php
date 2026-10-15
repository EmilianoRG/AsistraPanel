<?php
namespace app\widgets;

use app\assets\widgets\DispositivosDesconectadosGridAsset;
use yii\base\Widget;

class DispositivosDesconectadosGrid extends Widget {
  public $data = [];

  public function init() {
    parent::init();
    DispositivosDesconectadosGridAsset::register($this->view);
  }

  public function run(): string {
    if (!$this->data) {
      return <<<HTML
      <div class="alert alert-success shadow-sm border-0">
        <i class="bi bi-check-circle-fill me-1"></i>
        No hay tecnológicos con dispositivos desconectados en este momento.
      </div>
      HTML;
    }

    $cards = '';
    foreach ($this->data as $item) {
      $cards .= DispositivosDesconectadosCard::widget(['data' => $item]);
    }

    return '<div class="row g-4 mb-5">' . $cards . '</div>';
  }
}
