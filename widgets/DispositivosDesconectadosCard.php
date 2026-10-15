<?php
namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class DispositivosDesconectadosCard extends Widget {
  public $data = [];

  public function run(): string {
    $institucionNombre = Html::encode((string)($this->data['institucionNombre'] ?? 'N/D'));
    $tecnologicoId = Html::encode((string)($this->data['tecnologicoId'] ?? 'N/D'));
    $baseDatosNombre = Html::encode((string)($this->data['baseDatosNombre'] ?? 'N/D'));
    $totalDesconectados = (int)($this->data['totalDesconectados'] ?? 0);

    $dispositivosHtml = '';
    foreach (($this->data['dispositivosDesconectados'] ?? []) as $dispositivo) {
      $nombre = Html::encode((string)($dispositivo['nombre'] ?? 'Dispositivo sin nombre'));
      $detalles = '';
      foreach (($dispositivo['info'] ?? []) as $key => $value) {
        $keyEsc = Html::encode((string)$key);
        if (is_array($value)) {
          $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $valueEsc = Html::encode((string)$value);
        $detalles .= "<div class=\"item-row\"><span class=\"item-key\">{$keyEsc}</span><span class=\"item-value\">{$valueEsc}</span></div>";
      }
      $dispositivosHtml .= <<<HTML
      <div class="dispositivo-item p-3 mb-3">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
          <h6 class="m-0 fw-bold">{$nombre}</h6>
          <span class="badge rounded-pill text-bg-danger">Desconectado</span>
        </div>
        <div class="item-grid">
          {$detalles}
        </div>
      </div>
      HTML;
    }

    return <<<HTML
    <div class="col-xl-6 col-md-12">
      <div class="dispositivos-card h-100 p-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h4 class="m-0 fw-bold">{$institucionNombre}</h4>
            <div class="text-muted small mt-1">ID: {$tecnologicoId}</div>
          </div>
          <div class="text-end">
            <span class="badge rounded-pill text-bg-danger fs-6">{$totalDesconectados}</span>
            <div class="small text-muted mt-1">Desconectados</div>
          </div>
        </div>
        <div class="small text-muted mb-3">
          <i class="bi bi-database me-1"></i>{$baseDatosNombre}
        </div>
        {$dispositivosHtml}
      </div>
    </div>
    HTML;
  }
}
