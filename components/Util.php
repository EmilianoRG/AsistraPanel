<?php
namespace app\components;

use Yii;

class Util {
  public static function getProyectos(): array {
    return [
      [
        'nombre' => '2sis Evoluciona',
        'schema' => 'proyectos2sis_2sis',
        'url' => '2sis',
      ],
      [
        'nombre' => 'IT Altamira',
        'schema' => 'proyectos2sis_tec_altamira',
        'url' => 'altamira',
      ],
      [
        'nombre' => 'IT Campeche',
        'schema' => 'proyectos2sis_tec_campeche',
        'url' => 'campeche',
      ],
      [
        'nombre' => 'IT Ciudad Victoria',
        'schema' => 'proyectos2sis_tec_cdvictoria',
        'url' => 'cdvictoria',
      ],
      [
        'nombre' => 'IT Chiná',
        'schema' => 'proyectos2sis_tec_china',
        'url' => 'china',
      ],
      [
        'nombre' => 'IT Comitancillo',
        'schema' => 'proyectos2sis_tec_comitancillo',
        'url' => 'comitancillo',
      ],
      [
        'nombre' => 'Entorno de Pruebas (Sandbox)',
        'schema' => 'proyectos2sis_tec_sandbox',
        'url' => 'sandbox',
      ],
      [
        'nombre' => 'IT San Marcos',
        'schema' => 'proyectos2sis_tec_sanmarcos',
        'url' => 'sanmarcos',
      ],
      [
        'nombre' => 'IT Zacatepec',
        'schema' => 'proyectos2sis_tec_zacatepec',
        'url' => 'zacatepec',
      ],
//      [
//        'nombre' => 'Soporte 2sis',
//        'schema' => 'proyectos2sis_soporte',
//        'url' => 'soporte',
//      ],


//      [
//        'nombre' => 'Asistra 2sis',
//        'schema' => 'asistra_2sis',
//        'url' => 'asistra',
//      ],
    ];
  }

  public static function getRecuperacionesBackup($fecha = null) {
    if (!$fecha) {
      $fecha = date('Y-m-d');
    }
    // validar que la fecha tenga el formato correcto
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
      return ['errorMessage' => 'Fecha no válida. El formato debe ser YYYY-MM-DD.'];
    }
    try {
      $db = Yii::$app->db; // O Yii::$app->otraDb si es una conexión externa

      // 1. Definir variables y configurar la sesión
      $db->createCommand("SET @fecha_recuperacion = '{$fecha}';")->execute();
      $db->createCommand("SET SESSION group_concat_max_len = 1000000;")->execute();

      $proyectos = self::getProyectos();

      // 2. Construir el SQL dinámico en la variable @sql_completo
      $sqlConstruccion = "
      SELECT GROUP_CONCAT(
          CONCAT('SELECT
              ''', 
              CASE schema_name  
      ";

      foreach ($proyectos as $proyecto) {
        $sqlConstruccion .= "WHEN '{$proyecto['schema']}' THEN '{$proyecto['nombre']}'";
      }

      $sqlConstruccion .= "
                ELSE schema_name 
              END, 
              ''' AS Institucion,
              ''', schema_name, ''' AS BD,
              bp.fecha AS Fecha,
              COUNT(bp.id) AS Cantidad_Personal,
              COALESCE(b3.incompletos, 0) AS Cantidad_Personal_Recuperacion_Incompleta
          FROM ', schema_name, '.bitacora_procesamiento AS bp
          LEFT JOIN (
              SELECT fecha, COUNT(*) AS incompletos
              FROM ', schema_name, '.bitacora_procesamiento
              WHERE todos_registros_asistencia_obtenidos = 0 
                AND status = 1 
                AND fecha = ''', @fecha_recuperacion, '''
              GROUP BY fecha
          ) AS b3 ON b3.fecha = bp.fecha
          WHERE bp.fecha = ''', @fecha_recuperacion, ''' AND bp.status = 1
          GROUP BY bp.fecha')
          SEPARATOR ' UNION ALL '
      ) INTO @sql_completo
      FROM information_schema.schemata
      WHERE schema_name IN (
      ";

      $temp = [];
      foreach ($proyectos as $proyecto) {
        $temp[]= "'{$proyecto['schema']}'";
      }
      $sqlConstruccion .= implode(', ', $temp);

      $sqlConstruccion .= ");";

      $db->createCommand($sqlConstruccion)->execute();

      // 3. Preparar, ejecutar y recuperar los resultados del UNION ALL
      $db->createCommand("PREPARE stmt FROM @sql_completo;")->execute();

      // Aquí es donde realmente obtenemos el array de datos final
      $resultados = $db->createCommand("EXECUTE stmt;")->queryAll();

      // Limpieza
      $db->createCommand("DEALLOCATE PREPARE stmt;")->execute();

      // devolver un array con el resultado
      return $resultados;
    } catch (\Exception $ex) {
      return ['errorMessage' => $ex->getMessage()];
    }
  }

  public static function getRecuperaciones($fecha = null): array {
    if (!$fecha) {
      $fecha = date('Y-m-d');
    }
    // validar que la fecha tenga el formato correcto
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
      return ['errorMessage' => 'Fecha no válida. El formato debe ser YYYY-MM-DD.'];
    }
    $select = [
      'inicio_ejecucion',
      'fin_ejecucion',
      'tiempo_transcurrido',
      'fecha_inicio_a_procesar',
      'fecha_fin_a_procesar',
      'total_personal',
      'observaciones',
      'numero_errores',
    ];
    $select = implode(', ', $select);
    try {
      $db = Yii::$app->db;
      $resultados = [];
      foreach (self::getProyectos() as $proyecto) {
        $resultado = [
          'institucionNombre' => $proyecto['nombre'],
          'baseDatosNombre' => $proyecto['schema'],
          'fecha' => null,
          'totalEmpleados' => 0,
          'analizados' => 0,
          'recuperados' => 0,
          'incompletos' => 0,
          'inicioEjecucion' => null,
          'finEjecucion' => null,
          'tiempoTranscurrido' => null,
          'fechaInicioAProcesar' => null,
          'fechaFinAProcesar' => null,
          'numeroErrores' => 0,
          'encontrado' => false,
        ];
        $data = $db->createCommand("SELECT {$select} FROM {$proyecto['schema']}.procesos_background WHERE tipo_proceso = :tipo_proceso AND DATE(inicio_ejecucion) = :fecha LIMIT 1")
          ->bindValues([
            ':tipo_proceso' => 1,
            ':fecha' => $fecha,
          ])
          ->queryOne();
        if (!$data) {
          // intentar buscar el último registro de recuperación antes de la fecha dada
          $data = $db->createCommand("SELECT {$select} FROM {$proyecto['schema']}.procesos_background WHERE tipo_proceso = :tipo_proceso AND DATE(inicio_ejecucion) < :fecha ORDER BY fecha_inicio_a_procesar DESC LIMIT 1")
            ->bindValues([
              ':tipo_proceso' => 1,
              ':fecha' => $fecha,
            ])
            ->queryOne();
        }
        if ($data) {
          $analisis = json_decode($data['observaciones'], true);
          $resultado['fecha'] = date('Y-m-d', strtotime($data['inicio_ejecucion']));
          $resultado['totalEmpleados'] = $data['total_personal'];
          $resultado['analizados'] = $analisis['asistenciasTotalesAnalizadas'] ?? 0;
          $resultado['recuperados'] = $analisis['asistenciasProcesadas'] ?? 0;
          $resultado['incompletos'] = $analisis['asistenciasTotalesAnalizadas'] - $analisis['asistenciasProcesadas'] ?? 0;
          $resultado['inicioEjecucion'] = $data['inicio_ejecucion'];
          $resultado['finEjecucion'] = $data['fin_ejecucion'];
          $resultado['tiempoTranscurrido'] = $data['tiempo_transcurrido'];
          $resultado['fechaInicioAProcesar'] = $data['fecha_inicio_a_procesar'];
          $resultado['fechaFinAProcesar'] = $data['fecha_fin_a_procesar'];
          $resultado['numeroErrores'] = $data['numero_errores'];
          $resultado['encontrado'] = true;
        }
        $resultados[] = $resultado;
      }
      return $resultados;
    } catch (\Exception $ex) {
      return ['errorMessage' => $ex->getMessage()];
    }
  }

  public static array $months = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
  ];

  public static function formatDate($date, array $params = []): ?string {
    // <editor-fold defaultstate="collapsed" desc="Variables y Validacion">

    $separator = $params['separator'] ?? '/';
    $case = $params['case'] ?? 'mixed';
    $type = $params['type'] ?? 'short';
    if (!in_array($separator, ['/', '-', 'full'])) {
      $separator = '/';
    }
    if (!in_array($case, ['mixed', 'upper', 'lower'])) {
      $case = 'mixed';
    }
    if (!in_array($type, ['short', 'long'])) {
      $type = 'short';
    }
    if (!$date) {
      return null;
    }
    if (!is_string($date) && !is_int($date)) {
      return null;
    }
    if (is_string($date)) {
      if (!strtotime($date)) {
        return null;
      }
      $date = strtotime($date);
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Date Breakdown">

    $day = date('d', $date);
    $year = date('Y', $date);
    $month = self::$months[date('n', $date) - 1];
    if ($type === 'short') {
      $month = mb_substr($month, 0, 3);
    }
    if ($case === 'upper') {
      $month = mb_strtoupper($month);
    } else if ($case === 'lower') {
      $month = mb_strtolower($month);
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Formato Final">

    switch ($separator) {
      case '/':
        return "{$day}/{$month}/{$year}";
      case '-':
        return "{$day}-{$month}-{$year}";
      case 'full':
        return "{$day} de {$month} del {$year}";
      default:
        return '';
    }

    // </editor-fold>
  }

  public static function formatTime($time, array $params = []): ?string {
    // <editor-fold defaultstate="collapsed" desc="Variables y Validacion">

    $hourFormat = $params['hourFormat'] ?? 12;
    $addSeconds = $params['addSeconds'] ?? true;
    $useMeridien = $params['useMeridien'] ?? true;
    if (!in_array($hourFormat, [12, 24])) {
      $hourFormat = 12;
    }
    $addSeconds = is_bool($addSeconds) ? $addSeconds : true;
    $useMeridien = is_bool($useMeridien) ? $useMeridien : true;
    if (!$time) {
      return null;
    }
    if (!is_string($time) && !is_int($time)) {
      return null;
    }
    if (is_string($time)) {
      if (!strtotime($time)) {
        return null;
      }
      $time = strtotime($time);
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Time Breakdown">

    $hours = date($hourFormat === 12 ? 'h' : 'H', $time);
    $minutes = date('i', $time);
    $seconds = date('s', $time);
    $meridiem = date('a', $time);

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Formato Final">

    return "{$hours}:{$minutes}" . ($addSeconds ? ":{$seconds}" : '') . ($useMeridien ? " {$meridiem}" : '');

    // </editor-fold>
  }

  public static function obtenerTiempoTranscurrido($inicio, $fin): string {
    $fechaInicio = new \DateTime($inicio);
    $fechaFin    = new \DateTime($fin);

    // 1. Calculamos los segundos totales de diferencia
    $segundosTranscurridos = $fechaFin->getTimestamp() - $fechaInicio->getTimestamp();

    if ($segundosTranscurridos <= 0) {
      return '0s';

//      return [
//        'formato_corto' => '0s',
//        'segundos' => 0
//      ];
    }

    // 2. Obtenemos el desglose del intervalo
    $diferencia = $fechaInicio->diff($fechaFin);
    $resultadoTexto = [];

    // Sumamos los días multiplicados por 24 a las horas por si pasa de un día a otro
    $horasTotales = ($diferencia->days * 24) + $diferencia->h;

    // Agregar horas si existen
    if ($horasTotales > 0) {
      $resultadoTexto[] = $horasTotales . 'h';
    }

    // Agregar minutos si existen
    if ($diferencia->i > 0) {
      $resultadoTexto[] = $diferencia->i . 'm';
    }

    // Agregar segundos si existen
    if ($diferencia->s > 0) {
      $resultadoTexto[] = $diferencia->s . 's';
    }

    return implode(' ', $resultadoTexto);

//    return [
//      'formato_corto' => implode(' ', $resultadoTexto),
//      'segundos'      => $segundosTranscurridos
//    ];
  }
}