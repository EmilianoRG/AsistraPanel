<?php
namespace app\widgets;

use app\components\Util;
use yii\base\Widget;

class AsistenciaGrid extends Widget {
  public $data;

  const STATUS_PROCESO_INICIADO = 1;
  const STATUS_PROCESO_COMPLETADO = 2;

  public function init() {
    parent::init();
  }

  public function run(): string {
    $nombreCompleto = $this->data['nombre'] . ' ' . $this->data['apellido_paterno'];
    $iniciales = strtoupper(substr($this->data['nombre'], 0, 1) . substr($this->data['apellido_paterno'], 0, 1));
    if ($this->data['apellido_materno']) {
      $nombreCompleto .= ' ' . $this->data['apellido_materno'];
    }

    $fechaUltimasAsistencias = Util::formatDate($this->data['fecha']);

    $porcentajeAsistencias = $this->data['totalAsistencias'] > 0 ? round(($this->data['checadas'] / $this->data['totalAsistencias']) * 100, 2) : 0;
    $porcentajeClass = 'primary';
    if ($porcentajeAsistencias <= 0) {
      $porcentajeClass = 'danger';
    } else if ($porcentajeAsistencias >= 100) {
      $porcentajeClass = 'success';
    }

    $fecha = '-';
    if ($this->data['fechaUltimaChecada']) {
      if (strtotime($this->data['fechaUltimaChecada']) === strtotime(date('Y-m-d'))) {
        $fecha = 'Hoy';
      } else {
        $fecha = Util::formatDate($this->data['fechaUltimaChecada']);
      }
    }
    $hora = '-';
    switch ($this->data['status_proceso']) {
      case self::STATUS_PROCESO_INICIADO:
        $hora = Util::formatTime($this->data['hora_inicio_registrada']);
        break;
      case self::STATUS_PROCESO_COMPLETADO:
        $hora = Util::formatTime($this->data['hora_fin_registrada']);
        break;
    }

    $checadasClass = $this->data['checadas'] <= 0 ? 'text-danger' : 'text-success';

    $noEsDeHoyDiv = '';
    if (!$this->data['esDeHoy']) {
      $noEsDeHoyDiv = <<<HTML
      <!-- Espacio de Detalle del Check-in o Alerta -->
      <div class="my-2">
        <div class="critical-alert-banner mt-auto">
          <span class="critical-icon-anim"><i class="bi bi-x-circle-fill"></i></span>
          <div>
            <small class="d-block text-uppercase fw-bold text-xs">¡NO EXISTEN ASISTENCIAS PARA HOY!</small>
            <span class="fs-7">Último Horario: {$fechaUltimasAsistencias}</span>
          </div>
        </div>
      </div>
      HTML;
    }

    $asistraCardClass = '';
    $badgeDiv = '';
    if ($this->data['alertaSinChecadas']) {
      $asistraCardClass = 'asistra-card-critical';
      $badgeDiv = '<span class="critical-badge"><i class="bi bi-exclamation-circle-fill"></i> Alerta</span>';
    } else {
      $badgeDiv = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Activo</span>';
    }

    $sinChecadasDiv = '';
    if ($this->data['alertaSinChecadas']) {
      $sinChecadasDiv = <<<HTML
      <!-- Alerta No hay checadas del último horario! -->
      <div class="my-2">
        <div class="critical-alert-banner mt-auto">
          <span class="critical-icon-anim"><i class="bi bi-x-circle-fill"></i></span>
          <div>
            <small class="d-block text-uppercase fw-bold text-xs">¡ALERTA!</small>
            <span class="fs-7">No se registraron asistencias en la fecha del último horario: {$fechaUltimasAsistencias}</span>
          </div>
        </div>
      </div>
      HTML;
    }

    return <<<HTML
    <div class="col-xl-4 col-md-6">
      <div class="asistra-card {$asistraCardClass} p-4 d-flex flex-column justify-content-between">
        <!-- Cabecera de la tarjeta -->
        <div>
          <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
            <div>
              <h5 class="card-title mt-1 mb-0 fs-5 lh-sm text-dark-emphasis">{$this->data['institucionNombre']}</h5>
            </div>
            {$badgeDiv}
          </div>

          <!-- Detalles numéricos del Tecnológico -->
          <div class="row g-2 py-2 text-center bg-light rounded-3 my-2 border border-light">
            <div class="col-4">
              <span class="text-muted d-block text-uppercase text-xs font-bold" style="font-size: 0.65rem;">Empleados</span>
              <strong class="text-dark fs-6">{$this->data['personalTotal']}</strong>
            </div>
            <div class="col-4 border-start border-end">
              <span class="text-muted d-block text-uppercase text-xs font-bold" style="font-size: 0.65rem;">Checadas</span>
              <strong class={$checadasClass} fs-6">{$this->data['checadas']}</strong>
            </div>
            <div class="col-4">
              <span class="text-muted d-block text-uppercase text-xs font-bold" style="font-size: 0.65rem;">Esperados</span>
              <strong class="text-dark fs-6">{$this->data['totalAsistencias']}</strong>
            </div>
          </div>

          <!-- Progreso Visual -->
          <div class="asistra-progress-wrapper">
            <div class="d-flex justify-content-between align-items-center mb-1 text-xs">
              <span class="text-muted font-semibold">Registro Diario</span>
              <span class="text-{$porcentajeClass} fw-bold">{$this->data['checadas']} de {$this->data['totalAsistencias']} ({$porcentajeAsistencias}%)</span>
            </div>
            <div class="progress">
              <div class="progress-bar progress-bar-animated-custom rounded-pill bg-{$porcentajeClass}" role="progressbar" style="width: {$porcentajeAsistencias}%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
        </div>
        
        {$sinChecadasDiv}
        
        {$noEsDeHoyDiv}
        
        <!-- Espacio de Detalle del Check-in o Alerta -->
        <div class="my-2">
          <div class="checkin-detail-box mt-auto">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fw-bold text-xs text-muted text-uppercase">Última Asistencia</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <div class="checkin-avatar">{$iniciales}</div>
              <div style="flex: 1; min-width: 0;">
                <h6 class="mb-0 text-xs text-truncate">{$nombreCompleto}</h6>
              </div>
            </div>
            <div class="border-top mt-2 pt-2 d-flex justify-content-between text-muted text-xs">
              <span><i class="bi bi-calendar-event me-1"></i>{$fecha}</span>
              <span><i class="bi bi-clock me-1"></i>{$hora}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    HTML;
  }
}