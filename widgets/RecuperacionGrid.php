<?php
namespace app\widgets;

use yii\base\Widget;

class RecuperacionGrid extends Widget {
  public $data;
  public $fechaActual;

  public function init() {
    parent::init();
  }

  public function run(): string {
    $cards = '';
    foreach ($this->data as $item) {
      $desfasado = strtotime($item['fecha']) < strtotime($this->fechaActual);
      $desfaseDias = $desfasado ? (strtotime($this->fechaActual) - strtotime($item['fecha'])) / (60 * 60 * 24) : 0;
      $cards .= RecuperacionCard::widget([
        'institucionNombre' => $item['institucionNombre'],
        'baseDatosNombre' => $item['baseDatosNombre'],
        'fecha' => $item['fecha'],
        'totalEmpleados' => $item['totalEmpleados'],
        'analizados' => $item['analizados'],
        'recuperados' => $item['recuperados'],
        'incompletos' => $item['incompletos'],
        'inicioEjecucion' => $item['inicioEjecucion'],
        'finEjecucion' => $item['finEjecucion'],
        'tiempoTranscurrido' => $item['tiempoTranscurrido'],
        'numeroErrores' => $item['numeroErrores'],
        'desfasado' => $desfasado
      ]);
    }
    return '<div class="row g-4 mb-5">' . $cards . '</div>';
  }
}