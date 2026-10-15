<?php
use app\widgets\DispositivosDesconectadosGrid;
use app\widgets\TituloReporte;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var string $fechaHoy */
/** @var array $data */
/** @var array $errores */

$this->title = 'Dispositivos desconectados';

echo TituloReporte::widget([
  'titulo' => $this->title,
  'fecha' => $fechaHoy
]);

if ($errores) {
  $items = '';
  foreach ($errores as $error) {
    $nombre = Html::encode((string)($error['institucionNombre'] ?? 'N/D'));
    $id = Html::encode((string)($error['tecnologicoId'] ?? 'N/D'));
    $mensaje = Html::encode((string)($error['errorMessage'] ?? 'Error desconocido'));
    $items .= "<li><strong>{$nombre}</strong> ({$id}): {$mensaje}</li>";
  }
  echo <<<HTML
  <div class="alert alert-warning shadow-sm border-0 mb-3">
    <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i>Hubo tecnológicos que no se pudieron consultar:</div>
    <ul class="mb-0 ps-3">{$items}</ul>
  </div>
  HTML;
}

echo DispositivosDesconectadosGrid::widget([
  'data' => $data,
]);
