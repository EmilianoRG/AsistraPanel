<?php
use app\widgets\JustificacionesGrid;
use app\widgets\TituloReporte;
use yii\web\View;

/** @var View $this */
/** @var string $fechaHoy */
/** @var array $data */

$this->title = 'Justificaciones de Hoy';

echo TituloReporte::widget([
  'titulo' => $this->title,
  'fecha' => $fechaHoy
]);

echo JustificacionesGrid::widget([
  'fechaActual' => $fechaHoy,
  'data' => $data
]);