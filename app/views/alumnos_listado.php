<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Alumnos | DATPROCESADOR</title>
    <link rel="stylesheet" href="/DatProcesador/public/css/dashboard.css">
    <style>
        .container { padding: 30px; max-width: 1200px; margin: auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-main { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        
        /* Herramientas de tabla */
        .tools-panel { 
            background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; 
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .table-section { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #edf2f7; }
        th { background: #f8fafc; color: #4a5568; font-weight: 600; text-transform: uppercase; font-size: 0.85em; }
        
        .row-error { background-color: #fff5f5; }
        .row-success { background-color: #f0fff4; }

        .btn { padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.9em; transition: 0.2s; cursor: pointer; border: none; }
        .btn-danger { background: #e53e3e; color: white; }
        .btn-danger:hover { background: #c53030; }
        .btn-edit { color: #3182ce; margin-right: 10px; font-weight: bold; text-decoration: none; }
        .btn-delete { color: #e53e3e; font-weight: bold; text-decoration: none; }

        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.8em; font-weight: bold; }
        .badge-red { background: #fed7d7; color: #9b2c2c; }
        .badge-green { background: #c6f6d5; color: #22543d; }

        .alert-box { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; border: 1px solid; }
        .alert-success { background: #c6f6d5; color: #22543d; border-color: #9ae6b4; }
        .alert-info { background: #bee3f8; color: #2c5282; border-color: #90cdf4; }
    </style>
</head>
<body>

<div class="container">
    
    <div class="header-main">
        <h1>👥 Base de Datos de Alumnos</h1>
        <a href="/DatProcesador/" class="btn" style="background: #edf2f7; color: #4a5568;">⬅️ Volver al Inicio</a>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success'): ?>
            <div class="alert-box alert-success">✅ Carga Exitosa: Se han procesado <?php echo htmlspecialchars($_GET['count'] ?? '0'); ?> alumnos nuevos.</div>
        <?php elseif ($_GET['status'] == 'reset_ok'): ?>
            <div class="alert-box alert-success">✨ Tabla Reiniciada: Todos los registros fueron borrados y el contador de ID volvió a 1.</div>
        <?php elseif ($_GET['status'] == 'updated'): ?>
            <div class="alert-box alert-info">🔹 Datos del alumno actualizados correctamente.</div>
        <?php elseif ($_GET['status'] == 'deleted'): ?>
            <div class="alert-box" style="background:#feebcb; color:#744210; border-color:#fbd38d;">🗑️ Registro eliminado satisfactoriamente.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="tools-panel">
        <div>
            <h3 style="margin:0; color:#2d3748;">🛠️ Gestión Masiva</h3>
            <p style="margin:0; color:#718096; font-size: 0.9em;">Use el botón rojo para limpiar la tabla de alumnos por completo.</p>
        </div>
        <a href="/DatProcesador/alumnos/reiniciarTabla" 
           class="btn btn-danger" 
           onclick="return confirm('⚠️ ATENCIÓN: Esta acción eliminará permanentemente a TODOS los alumnos y reiniciará el contador ID a 1.\n\n¿Deseas continuar?')">
           🗑️ Eliminar Todo y Reiniciar Contador
        </a>
    </div>

    <div class="table-section" style="border-top: 4px solid #e53e3e;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="color: #c53030; margin: 0;">❌ Alumnos Duplicados (Mismo DNI)</h2>
            <span class="badge badge-red"><?php echo $total_duplicados; ?> Registros</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>DNI</th>
                    <th>Nombre Completo</th>
                    <th>Descripción</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($alumnos_duplicados)): ?>
                    <?php foreach ($alumnos_duplicados as $alumno): ?>
                        <tr class="row-error">
                            <td><small>#<?php echo $alumno['id']; ?></small></td>
                            <td><strong><?php echo htmlspecialchars($alumno['dni']); ?></strong></td>
                            <td><?php echo htmlspecialchars($alumno['nombre_completo']); ?></td>
                            <td><small><?php echo htmlspecialchars($alumno['descripcion'] ?: 'Sin descripción'); ?></small></td>
                            <td style="text-align: center;">
                                <a href="/DatProcesador/alumnos/editar/<?php echo $alumno['id']; ?>" class="btn-edit">Editar</a>
                                <a href="/DatProcesador/alumnos/eliminar/<?php echo $alumno['id']; ?>" class="btn-delete" onclick="return confirm('¿Borrar registro?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; color: #a0aec0; padding: 20px;">No hay registros duplicados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="table-section" style="border-top: 4px solid #38a169;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="color: #2f855a; margin: 0;">✅ Alumnos con Registro Único</h2>
            <span class="badge badge-green"><?php echo $total_unicos; ?> Alumnos</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>DNI</th>
                    <th>Nombre Completo</th>
                    <th>Descripción</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($alumnos_unicos)): ?>
                    <?php foreach ($alumnos_unicos as $alumno): ?>
                        <tr class="row-success">
                            <td><small>#<?php echo $alumno['id']; ?></small></td>
                            <td><?php echo htmlspecialchars($alumno['dni']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['nombre_completo']); ?></td>
                            <td><small><?php echo htmlspecialchars($alumno['descripcion'] ?: 'Sin descripción'); ?></small></td>
                            <td style="text-align: center;">
                                <a href="/DatProcesador/alumnos/editar/<?php echo $alumno['id']; ?>" class="btn-edit">Editar</a>
                                <a href="/DatProcesador/alumnos/eliminar/<?php echo $alumno['id']; ?>" class="btn-delete" onclick="return confirm('¿Eliminar alumno?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; color: #a0aec0; padding: 20px;">No hay alumnos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>