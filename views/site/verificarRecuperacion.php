<?php
use app\components\Util;
use app\widgets\RecuperacionGrid;
use yii\web\View;

/** @var View $this */
/** @var array $data */

$this->title = 'Monitoreo de Recuperación';
$fechaHoy = date('Y-m-d');
$fechaActual = Util::formatDate($fechaHoy);

echo <<<HTML
<!-- ENCABEZADO Y FILTROS -->
<div class="row align-items-center mb-4">
  <div class="col-md-8">
    <h2 class="fw-extrabold mb-1">{$this->title}</h2>
    <p class="text-muted">Estado de recuperación en todos los proyectos de Asistra. Fecha Actual: <strong>{$fechaActual}</strong></p>
  </div>
</div>
HTML;

echo RecuperacionGrid::widget([
  'fechaActual' => $fechaHoy,
  'data' => $data
]);