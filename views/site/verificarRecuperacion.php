<?php
use app\components\Util;
use app\widgets\RecuperacionGrid;
use yii\web\View;

/** @var View $this */

$this->title = 'Monitoreo de Recuperación';
$fechaHoy = date('Y-m-d');
$fechaActual = Util::formatDate($fechaHoy);

echo <<<HTML
<!-- ENCABEZADO Y FILTROS -->
<div class="row align-items-center mb-4">
  <div class="col-md-8">
    <h2 class="fw-extrabold mb-1">{$this->title}</h2>
    <p class="text-muted">Estado de recuperación en todos los proyectos de Asistra. Fecha Actual: {$fechaActual}</p>
  </div>
</div>
HTML;


echo RecuperacionGrid::widget([
  'fechaActual' => $fechaHoy,
  'data' => [
    [
      'institucionNombre' => 'Altamira',
      'baseDatosNombre' => 'Base de datos principal',
      'fecha' => '2024-06-01',
      'recuperados' => '150',
      'incompletos' => '5',
    ],
    [
      'institucionNombre' => 'Altamira',
      'baseDatosNombre' => 'Base de datos principal',
      'fecha' => '2024-06-01',
      'recuperados' => '150',
      'incompletos' => '5',
    ],
    [
      'institucionNombre' => 'Altamira',
      'baseDatosNombre' => 'Base de datos principal',
      'fecha' => '2024-06-01',
      'recuperados' => '150',
      'incompletos' => '5',
    ],
    [
      'institucionNombre' => 'Altamira',
      'baseDatosNombre' => 'Base de datos principal',
      'fecha' => '2024-06-01',
      'recuperados' => '150',
      'incompletos' => '5',
    ],
  ]
]);