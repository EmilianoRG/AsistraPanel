<?php
namespace app\widgets;

use app\components\Util;
use yii\base\Widget;

class RecuperacionCard extends Widget {
  public $institucionNombre;
  public $baseDatosNombre;
  public $fecha;
  public $totalEmpleados;
  public $totalARecuperar;
  public $recuperados;
  public $incompletos;
  public $asistenciasProcesadas;
  public $asistenciasTotalesAnalizadas;
  public $inicioEjecucion;
  public $finEjecucion;
  public $tiempoTranscurrido;
  public $fechaHoraActualizacion;
  public $numeroErrores;
  public $desfasado = false;

  public function init() {
    parent::init();
  }

  public function run(): string {
    $fechaUltimoEnvio = 'Hoy';
    $fechaEjecucion = /*Util::formatDate($this->inicioEjecucion) . ' ' . */ Util::formatTime($this->inicioEjecucion);
    $tiempoTranscurrido = Util::obtenerTiempoTranscurrido($this->inicioEjecucion, $this->finEjecucion);

    $porcentajeAsistenciasProcesadas = $this->asistenciasTotalesAnalizadas > 0 ? round(($this->asistenciasProcesadas / $this->asistenciasTotalesAnalizadas) * 100, 2) : 0;
    $fechaHoraActualizacion = Util::formatTime($this->fechaHoraActualizacion);
    // ajustar la clase dependiendo del porcentaje de asistencias procesadas, < 33 = verde, 33-66 = amarillo, > 66 = rojo
    $metricClass = $porcentajeAsistenciasProcesadas < 33 ? 'metric-state-green' : ($porcentajeAsistenciasProcesadas < 66 ? 'metric-state-yellow' : 'metric-state-danger');

    $class = '';
    $borderClass = '';
    $statusText = '';
    $statusClass = '';
    $statusTextClass = '';
    $statusIcon = '';
    $tecClass = '';
    $tecIcon = '';
    $tecEmpleadosClass = '';
    $tecFechaClass = '';
    $alerta = '';
    $erroresDiv = '';

    if ($this->desfasado) {
      $fechaUltimoEnvio = Util::formatDate($this->fecha);

      $class = 'danger';
      $borderClass = 'border-danger border-opacity-50';
      $statusText = 'Desfasado';
      $statusClass = 'blink-danger';
      $statusTextClass = 'white';
      $statusIcon = 'fa-circle-exclamation';
      $tecClass = 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-20';
      $tecIcon = 'fa-solid fa-triangle-exclamation';
      $tecEmpleadosClass = 'badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
      $tecFechaClass = 'fw-extrabold text-decoration-underline';
      $alerta = <<<HTML
      <div class="d-flex justify-content-between mb-1 bg-danger bg-opacity-10 p-2 rounded">
        <span class="text-danger fw-semibold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Desfase Crítico:</span>
        <span class="fw-bold text-danger">No es de hoy</span>
      </div>
      HTML;
    } else {
      $class = 'success';
      $statusText = 'Recuperado';
      $statusClass = 'status-active';
      $statusTextClass = 'success';
      $statusIcon = 'fa-circle-check';
      $tecIcon = 'fa-solid fa-school';
      $tecEmpleadosClass = 'badge bg-light text-secondary border';
    }
    if ($this->numeroErrores > 0) {
      $erroresDiv = <<<HTML
      <div class="d-flex justify-content-between mb-1 bg-danger bg-opacity-10 p-2 rounded">
        <span class="text-danger fw-semibold"><i class="fa-solid fa-triangle-exclamation me-1"></i>Número de Errores:</span>
        <span class="fw-bold text-danger">{$this->numeroErrores}</span>
      </div>
      HTML;
    }

    return <<<HTML
    <div class="col-lg-4 col-md-6">
      <div class="asistra-card shadow-sm {$borderClass}">
        <div class="card-accent-bar accent-{$class}"></div>
          
        <div class="status-indicator {$statusClass}">
          <i class="fa-solid {$statusIcon} text-{$statusTextClass}"></i> {$statusText}
        </div>
          
        <div class="p-4 pt-5">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="tec-logo-container {$tecClass}">
              <span class="text-{$class}"><i class="{$tecIcon}"></i></span>
            </div>
            <div>
              <h5 class="fw-bold mb-0">{$this->institucionNombre}</h5>
              <span class="{$tecEmpleadosClass}">{$this->totalEmpleados} Empleados</span>
            </div>
          </div>
  
          <hr class="text-muted opacity-25">

          <div class="row g-2 mb-2">
            <div class="col-6">
              <div class="sync-metric">
                <div class="sync-metric-val">{$this->recuperados}/{$this->totalARecuperar}</div>
                <div class="sync-metric-lbl">Recuperados</div>
              </div>
            </div>
            <div class="col-6">
              <div class="sync-metric">
                <div class="sync-metric-val text-{$class} {$tecFechaClass}">{$fechaUltimoEnvio}</div>
                <div class="sync-metric-lbl">Último Envío</div>
              </div>
            </div>
          </div>
          
          <div class="row g-2 mb-4">
            <div class="col-6">
              <div class="sync-metric {$metricClass}">
                <div class="sync-metric-val">{$this->asistenciasProcesadas} ({$porcentajeAsistenciasProcesadas}%)</div>
                <div class="sync-metric-lbl">Asistencias Procesadas</div>
              </div>
            </div>
            <div class="col-6">
              <div class="sync-metric">
                <div class="sync-metric-val">{$fechaHoraActualizacion}</div>
                <div class="sync-metric-lbl">Última Actualización</div>
              </div>
            </div>
          </div>
  
          <div class="small mb-4">
            {$alerta}
            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted"><i class="fa-solid fa-clock-rotate-left me-1"></i> Hora de Ejecución:</span>
              <span class="fw-bold text-{$class}"><span class="fecha-hoy-span"></span> {$fechaEjecucion}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted"><i class="fa-solid fa-clock-rotate-left me-1"></i> Tiempo Transcurrido:</span>
              <span class="fw-bold text-{$class}"><span class="transcurrido-span"></span> {$tiempoTranscurrido}</span>
            </div>
            {$erroresDiv}
          </div>
        </div>
      </div>
    </div>
    HTML;
  }
}