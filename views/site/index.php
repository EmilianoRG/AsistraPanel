<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Panel de reportes';
$this->params['meta_description'] = 'Dashboard para acceder a los reportes y módulos del sistema.';
?>
<div class="site-index">
    <div class="py-4 mb-3">
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="text-muted">Seleccione el módulo o reporte que desea consultar.</p>
    </div>

    <div class="row g-3">
        <div class="col-sm-6 col-md-4">
            <div class="card shadow-sm rounded-3 h-100">
                <div class="card-body text-center">
                    <div class="display-6 mb-2">🔄</div>
                    <h5 class="card-title">Verificar recuperación</h5>
                    <p class="card-text small text-muted">Verifica si la recuperación se realizó correctamente en cada tecnológico / proyecto.</p>
                    <?= Html::a('Ir al módulo', ['site/verificar-recuperacion'], ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card shadow-sm rounded-3 h-100">
                <div class="card-body text-center">
                    <div class="display-6 mb-2">🕓</div>
                    <h5 class="card-title">Checadas de hoy</h5>
                    <p class="card-text small text-muted">Revisa las checadas del día actual por proyecto.</p>
                    <?= Html::a('Ir al módulo', ['site/asistencias'], ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card shadow-sm rounded-3 h-100">
                <div class="card-body text-center">
                    <div class="display-6 mb-2">📋</div>
                    <h5 class="card-title">Justificaciones</h5>
                    <p class="card-text small text-muted">Gestiona y revisa las justificaciones de asistencia.</p>
                    <?= Html::a('Ir al módulo', ['site/justificaciones'], ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card shadow-sm rounded-3 h-100">
                <div class="card-body text-center">
                    <div class="display-6 mb-2">🔐</div>
                    <h5 class="card-title">API (simple)</h5>
                    <p class="card-text small text-muted">Acceda a los endpoints públicos mediante la API con API key fija.</p>
                    <?= Html::a('Ver endpoints', ['API/index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                </div>
            </div>
        </div>
    </div>

</div>
