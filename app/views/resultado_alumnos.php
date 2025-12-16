<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado de Carga</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/dashboard.css">
    <style>
        .container { 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            background-color: #fff; 
        }
        .summary-box { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 5px; 
        }
        /* Estilo para los duplicados (ahora arriba) */
        .duplicate-list { 
            border-top: 2px solid #d9534f; /* Rojo para alertar */
            padding-top: 15px; 
            margin-top: 20px; 
        }
        .error-list {
            border-top: 2px solid #f0ad4e; /* Advertencia para errores de formato */
            padding-top: 15px; 
            margin-top: 20px; 
        }
        .error-item { 
            color: #f0ad4e; 
            margin-bottom: 5px; 
        }
        .duplicate-item {
            color: #d9534f; 
            margin-bottom: 5px;
            font-weight: bold;
        }
        .dni-container {
            max-height: 300px; 
            overflow-y: auto;  
            border: 1px solid #eee;
            padding: 10px;
            margin-top: 10px;
        }
        .note {
            background-color: #fcf8e3;
            border: 1px solid #faebcc;
            color: #8a6d3b;
            padding: 10px;
            border-radius: 4px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Proceso de Carga Finalizado</h1>

        <div class="summary-box">
            <h2>Resumen de la Carga</h2>
            <div class="note">
                <strong>MODO: CORRECCIÓN HABILITADA.</strong> Todos los registros válidos se han insertado.
            </div>
            <ul>
                <li>Total de Líneas Procesadas: <strong><?php echo $total_procesado; ?></strong></li>
                <li>Líneas Válidas Insertadas: <strong style="color: #5cb85c;"><?php echo $registros_insertados; ?></strong></li>
                <li>Líneas con Errores de Formato: <strong style="color: #f0ad4e;"><?php echo $total_errores; ?></strong></li>
            </ul>
        </div>
        
        <?php 
        // Usamos $duplicados_para_corregir, que contiene el resultado de la consulta GROUP BY
        if ($total_duplicados_tabla > 0 && !empty($duplicados_para_corregir)): ?>
            <div class="duplicate-list">
                <h2>❌ DNI Duplicados a Corregir en la Base de Datos (Filtro)</h2>
                <p>El sistema ha detectado **<?php echo $total_duplicados_tabla; ?>** DNI que aparecen más de una vez en la tabla `alumnos`. **¡REVISAR INMEDIATAMENTE!**</p>
                
                <div class="dni-container">
                    <ul>
                        <?php 
                        // El array $duplicados_para_corregir contiene arrays asociativos: ['dni' => '...', 'total_repeticiones' => N]
                        foreach ($duplicados_para_corregir as $duplicado): 
                            // Hacemos el casting y la verificación de claves robusta
                            $datos = (array) $duplicado;
                            $dni = $datos['dni'] ?? $datos['DNI'] ?? 'DNI_DESCONOCIDO';
                            $repeticiones = $datos['total_repeticiones'] ?? $datos['TOTAL_REPETICIONES'] ?? 'N/A';
                        ?>
                            <li class="duplicate-item">
                                DNI: **<?php echo htmlspecialchars($dni); ?>** (Se repite **<?php echo htmlspecialchars($repeticiones); ?>** veces)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php else: ?>
             <div class="duplicate-list" style="border-top: 2px solid #5cb85c;">
                <h2>✨ No se Detectaron Duplicados en la Tabla</h2>
                <p>Todos los DNI en la tabla de alumnos son únicos en este momento.</p>
            </div>
        <?php endif; ?>

        
        <?php if ($total_errores > 0): ?>
            <div class="error-list">
                <h2>⚠️ Errores de Formato (Líneas Descartadas)</h2>
                <p>Las siguientes líneas no cumplieron con el formato y no fueron insertadas:</p>
                <ul>
                    <?php foreach ($errores_detalle as $error): ?>
                        <li class="error-item"><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <p style="margin-top: 30px;">
            <a href="<?php echo $base_url; ?>alumnos/verTabla">Ver todos los Alumnos cargados</a> | 
            <a href="<?php echo $base_url; ?>">Volver al Inicio</a>
        </p>
    </div>
</body>
</html>