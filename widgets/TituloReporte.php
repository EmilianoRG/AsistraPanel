<?php
namespace app\widgets;

use app\components\Util;
use yii\base\Widget;

class TituloReporte extends Widget {
  public $titulo;
  public $fecha; // fecha actual

  public function init() {
    parent::init();
  }

  public function run(): string {
    $fecha = Util::formatDate($this->fecha);
    return <<<HTML
    <div class="bg-white p-4 rounded-4 shadow-sm border border-light d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <span class="text-muted text-uppercase font-bold tracking-wider fs-7">Reporte</span>
        <h2 class="mb-0 mt-1">{$this->titulo}</h2>
      </div>
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="text-muted me-2"><i class="bi bi-calendar3 me-1"></i> Hoy: <strong id="current-date">{$fecha}</strong></span>
      </div>
    </div>
    HTML;
  }
}