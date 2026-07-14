<?php
namespace app\widgets;

use app\components\Util;
use yii\base\Widget;

class JustificacionCard extends Widget {
  public $data;

  const TIPO_POR_SUBPERIODO = 1;
  const TIPO_GLOBAL = 2;

  const JUSTIFICA_RETARDO = 1;
  const JUSTIFICA_OMITIO_ENTRADA = 2;
  const JUSTIFICA_OMITIO_SALIDA = 3;
  const JUSTIFICA_FALTA = 4;
  const JUSTIFICA_FALTARON_HORAS_DE_TRABAJO = 5;
  const JUSTIFICA_PERMISO_POR_HORAS = 6;
  const JUSTIFICA_JUSTIFICACION_BAJO_CRITERIO = 7;
  const JUSTIFICA_FUERA_DE_HORARIO_ENTRADA = 8;
  const JUSTIFICA_FUERA_DE_HORARIO_SALIDA = 9;
  const JUSTIFICA_FUERA_DE_HORARIO = 10;

  public static array $justificacionArray = [
    self::JUSTIFICA_RETARDO => ['texto' => 'RETARDO', 'color' => 'badge badge-warning'],
    self::JUSTIFICA_OMITIO_ENTRADA => ['texto' => 'OMITIÓ ENTRADA', 'color' => 'badge badge-warning'],
    self::JUSTIFICA_OMITIO_SALIDA => ['texto' => 'OMITIÓ SALIDA', 'color' => 'badge badge-warning'],
    self::JUSTIFICA_FALTA => ['texto' => 'FALTA', 'color' => 'badge badge-warning'],
    self::JUSTIFICA_FALTARON_HORAS_DE_TRABAJO => ['texto' => 'FALTARON HORAS DE TRABAJO', 'color' => 'badge badge-warning'],
    self::JUSTIFICA_PERMISO_POR_HORAS => ['texto' => 'PERMISO POR HORAS', 'color' => 'badge badge-warning'],
    self::JUSTIFICA_JUSTIFICACION_BAJO_CRITERIO => ['texto' => 'JUSTIFICACIÓN BAJO CRITERIO', 'color' => 'badge badge-warning'],
    self::JUSTIFICA_FUERA_DE_HORARIO_ENTRADA => ['texto' => 'FUERA DE HORARIO ENTRADA', 'color' => 'badge badge-warning'],
    self::JUSTIFICA_FUERA_DE_HORARIO_SALIDA => ['texto' => 'FUERA DE HORARIO SALIDA', 'color' => 'badge badge-warning'],
    self::JUSTIFICA_FUERA_DE_HORARIO => ['texto' => 'FUERA DE HORARIO', 'color' => 'badge badge-warning'],
  ];

  const TIPO_JUSTIFICACION = 0;
  const TIPO_PERMISO = 1;
  const TIPO_VACACIONES = 2;

  public function init() {
    parent::init();
  }

  public function run(): string {
    $statusClass = 'status-active';
    if ($this->data['sinJustificaciones']) {
      $statusClass = 'status-empty';
    } else if (!$this->data['esDeHoy']) {
      $statusClass = 'status-historic';
    }

    $hoyBadge = '';
    if ($this->data['sinJustificaciones']) {
      $hoyBadge = '<span class="badge badge-asistra-empty mb-2 d-inline-flex align-items-center gap-1"><i class="fas fa-history"></i> Sin registros</span>';
    } else {
      if ($this->data['esDeHoy']) {
        $hoyBadge = '<span class="badge badge-asistra-primary mb-2 d-inline-flex align-items-center gap-1"><i class="fas fa-calendar-check"></i> Hoy</span>';
      } else {
        $hoyBadge = '<span class="badge badge-asistra-warning mb-2 d-inline-flex align-items-center gap-1"><i class="fas fa-history"></i> Histórico</span>';
      }
    }

    $alertDiv = '';
    if ($this->data['sinJustificaciones']) {
      $alertDiv = <<<HTML
      <div class="empty-alert p-3 mb-3 d-flex align-items-start gap-2 text-muted">
        <i class="fas fa-info-circle mt-1 text-secondary"></i>
        <div>
          <strong>Sin actividad registrada.</strong> Este Tecnológico no cuenta con antecedentes ni movimientos en el sistema.
        </div>
      </div>
      HTML;
    } else if (!$this->data['esDeHoy']) {
      $ultimaFecha = Util::formatDate($this->data['fecha']);
      $alertDiv = <<<HTML
      <div class="historical-alert p-3 mb-3 d-flex align-items-start gap-2">
        <i class="fas fa-exclamation-triangle mt-1"></i>
        <div>
          <strong>Mostrando datos del último día activo ({$ultimaFecha}).</strong> 
          Hoy no se registran incidencias en este plantel.
        </div>
      </div>
      HTML;
    }

    $ultimaJustificacionDiv = '';
    if ($this->data['sinJustificaciones']) {
      $ultimaJustificacionDiv = <<<HTML
      <div class="detail-card p-4 text-center border-dashed">
        <i class="fas fa-folder-open text-muted fs-3 mb-2"></i>
        <p class="m-0 text-muted small">No existe última justificación o evento registrado para este tecnológico en el sistema</p>
      </div>
      HTML;
    } else {
      $tipoJustificacion = self::TIPO_JUSTIFICACION;
      if ($this->data['vacacion_id'] !== null) {
        $tipoJustificacion = self::TIPO_VACACIONES;
      } else if ($this->data['justifica'] == self::JUSTIFICA_PERMISO_POR_HORAS) {
        $tipoJustificacion = self::TIPO_PERMISO;
      }
      $tipoBadge = '';
      switch ($tipoJustificacion) {
        case self::TIPO_JUSTIFICACION:
          $tipoBadge = '<span class="badge-type-justificacion">Justificación</span>';
          break;
        case self::TIPO_PERMISO:
          $tipoBadge = '<span class="badge-type-permiso">Permiso</span>';
          break;
        case self::TIPO_VACACIONES:
          $tipoBadge = '<span class="badge-type-vacaciones">Vacaciones</span>';
          break;
      }
      $tipoSpan = '';
      if ($this->data['tipo'] == self::TIPO_POR_SUBPERIODO) {
        $tipoSpan = '<span class="badge-scope-individual"><i class="fas fa-user"></i> Individual</span>';
      } else if ($this->data['tipo'] == self::TIPO_GLOBAL) {
        $tipoSpan = '<span class="badge-scope-global"><i class="fas fa-globe"></i> Global</span>';
      }
      $creado = Util::formatDate($this->data['fecha_creacion']) . ' ' . Util::formatTime($this->data['hora_creacion']);
      $desde = Util::formatDate($this->data['fecha_inicio']) . ' ' . Util::formatTime($this->data['hora_inicio']);
      $hasta = Util::formatDate($this->data['fecha_fin']) . ' '. Util::formatTime($this->data['hora_fin']);

      $ultimaJustificacionDiv = <<<HTML
      <div class="detail-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
          {$tipoBadge}
          {$tipoSpan}
        </div>

        <div class="small text-muted mb-3 d-flex align-items-center gap-1">
          <i class="far fa-clock"></i>
          <span>Creado: <strong>{$creado}</strong></span>
        </div>

        <div class="mb-2">
          <span class="small text-muted d-block">Motivo:</span>
          <span class="fw-bold text-dark fs-6">{$this->data['motivo']}</span>
        </div>

        <div class="row g-2 mb-3">
          <div class="col-sm-6">
            <span class="small text-muted d-block"><i class="fas fa-play-circle text-success me-1"></i>Desde:</span>
            <span class="text-dark fw-semibold small">{$desde}</span>
          </div>
          <div class="col-sm-6">
            <span class="small text-muted d-block"><i class="fas fa-stop-circle text-danger me-1"></i>Hasta:</span>
            <span class="text-dark fw-semibold small">{$hasta}</span>
          </div>
        </div>

        <!-- Observations Block with custom padded class -->
        <div class="observation-box">
          <span class="small text-muted d-block fw-bold mb-1">Observaciones:</span>
          <p class="m-0 text-dark small fst-italic">
            "{$this->data['observaciones']}"
          </p>
        </div>
      </div>
      HTML;
    }

    // col-xl-4
    return <<<HTML
    <div class="col-md-6 col-12">
      <div class="tec-card h-100 p-4 {$statusClass}">
        <div>
          <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
            <div>
              {$hoyBadge}
              <h3 class="fs-5 fw-bold text-dark m-0 card-title-hover">{$this->data['institucionNombre']}</h3>
            </div>
            <div class="text-end shrink-0">
              <span class="small text-muted d-block">Empleados</span>
              <span class="fw-bold fs-5 text-dark"><i class="fas fa-users me-1"></i>{$this->data['personalTotal']}</span>
            </div>
          </div>
          
          <!-- Status specific alerts -->
          {$alertDiv}

          <!-- Stats Metrics Split for this Tec -->
          <div class="row g-2 mb-4">
            <div class="col-6">
              <div class="stat-box d-flex align-items-center justify-content-between">
                <div>
                  <span class="small text-muted d-block">Justificaciones</span>
                  <span class="fs-4 fw-extrabold text-dark">{$this->data['totalJustificaciones']}</span>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                  <i class="fas fa-file-alt fs-5"></i>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="stat-box d-flex align-items-center justify-content-between">
                <div>
                  <span class="small text-muted d-block">Permisos</span>
                  <span class="fs-4 fw-extrabold text-dark">{$this->data['totalPermisos']}</span>
                </div>
                <div class="bg-info bg-opacity-10 text-info p-2 rounded-3">
                  <i class="fas fa-file-signature fs-5"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Last transaction details section -->
        {$ultimaJustificacionDiv}
      </div>
    </div>
    HTML;
  }
}