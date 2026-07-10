<?php
namespace app\widgets;

use app\assets\widgets\RecuperacionesGridAsset;
use yii\base\Widget;

class RecuperacionesGrid extends Widget {
  public $fechaActual;
  public $data;

  public function init() {
    parent::init();
    RecuperacionesGridAsset::register($this->view);
  }

  public function run(): string {
    $cards = '';
    foreach ($this->data as $item) {
      $desfasado = strtotime($item['fecha']) < strtotime($this->fechaActual);
      $desfaseDias = $desfasado ? (strtotime($this->fechaActual) - strtotime($item['fecha'])) / (60 * 60 * 24) : 0; // AÚN NO SE USA!
      $cards .= RecuperacionCard::widget([
        'institucionNombre' => $item['institucionNombre'],
        'baseDatosNombre' => $item['baseDatosNombre'],
        'fecha' => $item['fecha'],
        'totalEmpleados' => $item['totalEmpleados'],
        'totalARecuperar' => $item['totalARecuperar'],
        'recuperados' => $item['recuperados'],
        'incompletos' => $item['incompletos'],
        'asistenciasProcesadas' => $item['asistenciasProcesadas'],
        'asistenciasTotalesAnalizadas' => $item['asistenciasTotalesAnalizadas'],
        'inicioEjecucion' => $item['inicioEjecucion'],
        'finEjecucion' => $item['finEjecucion'],
        'tiempoTranscurrido' => $item['tiempoTranscurrido'],
        'fechaHoraActualizacion' => $item['fechaHoraActualizacion'],
        'numeroErrores' => $item['numeroErrores'],
        'sinConexion' => $item['sinConexion'],
        'desfasado' => $desfasado
      ]);
    }
    return '<div class="row g-4 mb-5">' . $cards . '</div>';
  }
}