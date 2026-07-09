<?php
use app\components\Util;
use app\widgets\RecuperacionesGrid;
use app\widgets\TituloReporte;
use yii\web\View;

/** @var View $this */
/** @var string $fechaHoy */
/** @var array $data */

$this->title = 'Monitoreo de Recuperación';

echo TituloReporte::widget([
  'titulo' => $this->title,
  'fecha' => $fechaHoy
]);

/*$fechaActual = Util::formatDate($fechaHoy);
echo <<<HTML
<!-- ENCABEZADO Y FILTROS -->
<div class="row align-items-center mb-4">
  <div class="col-md-8">
    <h2 class="fw-extrabold mb-1">{$this->title}</h2>
    <p class="text-muted">Estado de recuperación en todos los proyectos de Asistra. Fecha Actual: <strong>{$fechaActual}</strong></p>
  </div>
</div>
HTML;*/

echo RecuperacionesGrid::widget([
  'fechaActual' => $fechaHoy,
  'data' => $data
]);