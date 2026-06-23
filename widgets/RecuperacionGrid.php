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
      $desfazado = strtotime($item['fecha']) < strtotime($this->fechaActual);
      $cards .= RecuperacionCard::widget([
        'institucionNombre' => $item['institucionNombre'],
        'baseDatosNombre' => $item['baseDatosNombre'],
        'fecha' => $item['fecha'],
        'recuperados' => $item['recuperados'],
        'incompletos' => $item['incompletos'],
        'desfazado' => $desfazado
      ]);
    }
    return '<div class="row g-4 mb-5">' . $cards . '</div>';
  }
}