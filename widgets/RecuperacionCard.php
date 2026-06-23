<?php
namespace app\widgets;

use yii\base\Widget;

class RecuperacionCard extends Widget {
  public $institucionNombre;
  public $baseDatosNombre;
  public $fecha;
  public $recuperados;
  public $incompletos;
  public $desfazado = false;

  public function init() {
    parent::init();
  }

  public function run(): string {
    return <<<HTML
    <div class="col-lg-4 col-md-6">
      <div class="asistra-card shadow-sm">
        <div class="card-accent-bar accent-success"></div>
          
        <div class="status-indicator status-active">
          <i class="fa-solid fa-circle-check text-success"></i> Sincronizado
        </div>
          
        <div class="p-4 pt-5">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="tec-logo-container">
              <span class="text-success"><i class="fa-solid fa-school"></i></span>
            </div>
            <div>
              <h5 class="fw-bold mb-0">{$this->institucionNombre}</h5>
              <span class="badge bg-light text-secondary border">500 Empleados</span>
            </div>
          </div>
  
          <hr class="text-muted opacity-25">

          <div class="row g-2 mb-4">
            <div class="col-6">
              <div class="sync-metric">
                <div class="sync-metric-val">12,430</div>
                <div class="sync-metric-lbl">Total Sincros</div>
              </div>
              </div>
              <div class="col-6">
                <div class="sync-metric">
                <div class="sync-metric-val text-success">Hoy</div>
                <div class="sync-metric-lbl">Último Envío</div>
              </div>
            </div>
          </div>
  
          <div class="small mb-4">
            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted"><i class="fa-solid fa-microchip me-1"></i> Dispositivos:</span>
              <span class="fw-semibold">3 Suprema (BS3-DB, BSL2)</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted"><i class="fa-solid fa-clock-rotate-left me-1"></i> Última lectura:</span>
              <span class="fw-bold text-success"><span class="fecha-hoy-span"></span> 10:58 AM</span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted"><i class="fa-solid fa-network-wired me-1"></i> IP Local Servidor:</span>
              <span class="font-monospace">192.168.10.22</span>
            </div>
          </div>
        </div>
      </div>
    </div>
HTML;
  }
}