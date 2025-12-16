<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado General de Alumnos</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>././public/css/alumno.css"> 
    <style>
        .table-container { margin-bottom: 40px; }
        .table-container h2 { margin-bottom: 10px; }
        .table-duplicados { border: 2px solid #d9534f; } 
        .table-unicos { border: 2px solid #5cb85c; }   
        .duplicate-row { background-color: #fde0df; }  
        
        /* Estilo específico para el botón Editar */
        .btn-edit {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
        }
        .btn-edit:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Listado General de Alumnos</h1>
        
        <div style="text-align: right; margin-bottom: 20px;">
             <a href="<?php echo $base_url; ?>alumnos/eliminarDatos" class="btn btn-danger" 
                onclick="return confirm('¿Está seguro de que desea ELIMINAR y VACIAR toda la tabla de alumnos?')">
                Eliminar y Reiniciar Tabla
             </a>
        </div>

        <p>Total de Registros en el Sistema: <strong><?php echo $total_alumnos; ?></strong></p>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'success_delete'): ?>
                <div class="alert alert-success">
                    ✅ La tabla de alumnos ha sido vaciada exitosamente.
                </div>
            <?php elseif ($_GET['status'] == 'success_edit'): ?>
                <div class="alert alert-success">
                    ✅ ¡Éxito! El alumno fue actualizado correctamente.
                </div>
            <?php elseif ($_GET['status'] == 'no_change'): ?>
                <div class="alert alert-info" style="background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460;">
                    ℹ️ Los datos se enviaron, pero no se detectaron cambios en el registro.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="table-container">
            <h2 style="color: #d9534f;">❌ Registros con DNI Duplicado (<?php echo $total_duplicados; ?> Filas)</h2>
            <p>Estos registros tienen un DNI que aparece más de una vez. Requieren corrección.</p>
            
            <?php if ($total_duplicados > 0): ?>
            <table class="table table-duplicados">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre Completo</th>
                        <th>Descripción</th>
                        <th>Acciones</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnos_duplicados as $alumno): ?>
                    <tr class="duplicate-row">
                        <td><strong><?php echo htmlspecialchars($alumno['dni']); ?></strong></td>
                        <td><?php echo htmlspecialchars($alumno['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['descripcion']); ?></td>
                        <td>
                            <a href="<?php echo $base_url; ?>alumnos/editar/<?php echo $alumno['id']; ?>" class="btn-edit">
                                Editar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="alert alert-success">No hay DNI duplicados en la base de datos.</div>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <h2 style="color: #5cb85c;">✅ Registros con DNI Único (<?php echo $total_unicos; ?> Filas)</h2>
            <p>Estos registros no tienen problemas de DNI repetidos.</p>

            <?php if ($total_unicos > 0): ?>
            <table class="table table-unicos">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre Completo</th>
                        <th>Descripción</th>
                        <th>Acciones</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnos_unicos as $alumno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($alumno['dni']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['descripcion']); ?></td>
                        <td>
                             <a href="<?php echo $base_url; ?>alumnos/editar/<?php echo $alumno['id']; ?>" class="btn-edit">
                                Editar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="alert alert-info">No hay registros únicos.</div>
            <?php endif; ?>
        </div>
        
        <p style="margin-top: 30px;">
            <a href="<?php echo $base_url; ?>">Volver al Inicio (Cargar CSV)</a>
        </p>
    </div>
</body>
</html>