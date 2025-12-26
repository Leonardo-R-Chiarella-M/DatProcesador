<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Principal | DATPROCESADOR</title>
    <link rel="stylesheet" href="/DatProcesador/public/css/dashboard.css">
    <style>
        body { background-color: #f8fafc; margin: 0; font-family: 'Segoe UI', sans-serif; color: #2d3748; }
        .container { padding: 30px; max-width: 1400px; margin: auto; }
        
        /* Panel de Herramientas */
        .tools-panel { 
            background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Botón de archivo personalizado */
        .custom-file-upload {
            display: inline-block; padding: 10px 15px; cursor: pointer;
            background: #f1f5f9; color: #475569; border-radius: 6px;
            border: 1px solid #cbd5e0; font-size: 0.85em; font-weight: bold;
        }
        input[type="file"] { display: none; }

        /* GRID DE 4 COLUMNAS */
        .cursos-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); 
            gap: 25px;
        }

        .curso-card {
            background: white; border-radius: 15px; padding: 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07); 
            border-top: 5px solid #38a169;
            display: flex; flex-direction: column; transition: 0.3s ease;
        }
        .curso-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        
        .curso-card h3 { margin: 15px 0; font-size: 1.3em; font-weight: 800; color: #1a202c; }
        
        .q-badge { background: #c6f6d5; color: #22543d; padding: 5px 12px; border-radius: 8px; font-size: 0.75em; font-weight: bold; }

        /* Botones de acción principales */
        .btn-group-main { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; }
        .btn-action { 
            flex: 1; min-width: 80px; padding: 12px; border-radius: 8px; text-decoration: none; 
            font-weight: bold; font-size: 0.85em; text-align: center; color: white;
            display: flex; flex-direction: column; align-items: center; gap: 5px;
            transition: 0.2s;
        }
        .btn-blue { background: #3182ce; }
        .btn-dark { background: #2d3748; }
        .btn-success { background: #38a169; flex-basis: 100%; } /* Verde para resultados, ocupando ancho completo */

        .btn-action:hover { opacity: 0.9; transform: scale(1.02); }

        /* Enlaces de Editar y Borrar */
        .btn-group-edit { 
            display: flex; justify-content: space-around; 
            border-top: 1px solid #edf2f7; padding-top: 15px; 
        }
        .link-action { text-decoration: none; font-size: 0.9em; font-weight: 600; display: flex; align-items: center; gap: 5px; }
        .link-edit { color: #f56565; } 
        .link-delete { color: #a0aec0; }

        .alert-success { background: #c6f6d5; color: #22543d; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; border: 1px solid #9ae6b4; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h1 style="font-size: 2em; font-weight: 800;">📊 Dashboard Principal</h1>
        <a href="/DatProcesador/alumnos/verTabla" class="btn" style="background:#fff; color:#4a5568; border:1px solid #cbd5e0; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display:flex; align-items:center; gap:8px;">
            <span>👥</span> Ver Alumnos
        </a>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'curso_ok'): ?>
            <div class="alert-success">✅ Curso creado exitosamente.</div>
        <?php elseif ($_GET['status'] == 'curso_deleted'): ?>
            <div class="alert-success" style="background:#fff5f5; color:#c53030; border-color:#feb2b2;">🗑️ Curso eliminado correctamente.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="tools-panel">
        <div>
            <h3 style="margin:0; font-weight: 800;">🛠️ Gestión de Contenido</h3>
            <p style="margin:0; color:#718096; font-size: 0.9em;">Cargue archivos o gestione sus cursos.</p>
        </div>
        
        <div style="display: flex; gap: 15px; align-items: center;">
            <form action="/DatProcesador/alumnos/procesar" method="POST" enctype="multipart/form-data" style="display:flex; gap:10px; align-items: center;">
                <label for="datFile" class="custom-file-upload">📂 Seleccionar CSV</label>
                <input type="file" name="datFile" id="datFile" required onchange="document.getElementById('file-name').textContent = this.files[0].name">
                <span id="file-name" style="font-size:0.8em; color:#a0aec0; font-style: italic;">Ningún archivo...</span>
                <button type="submit" class="btn" style="background:#3182ce; color:white; border:none; padding:10px 20px; border-radius:8px; font-weight:bold; cursor:pointer;">Procesar</button>
            </form>
            <div style="margin: 20px 0;">
    <a href="/DatProcesador/procesador/exportarTodoExcel" 
       style="background-color: #3182ce; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
       📁 Exportar Reporte Consolidado (Todos los Cursos)
    </a>
</div>
            <a href="/DatProcesador/cursos/crear" class="btn" style="background:#38a169; color:white; text-decoration:none; padding:10px 20px; border-radius:8px; font-weight:bold;">+ Nuevo Curso</a>
        </div>
    </div>

    <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 5px solid #3182ce;">
        <h2 style="margin-top:0; font-weight: 800; color: #2c5282;">📚 Cursos y Exámenes Registrados</h2>
        
        <div class="cursos-grid">
            <?php if (!empty($cursos)): ?>
                <?php foreach ($cursos as $curso): ?>
                    <div class="curso-card">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span class="q-badge"><?php echo $curso['cantidad_preguntas']; ?> Preguntas</span>
                            <span style="font-size:0.75em; color:#cbd5e0; font-weight:bold;">ID: <?php echo $curso['id']; ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($curso['nombre_curso']); ?></h3>
                        
                        <div class="btn-group-main">
                            <a href="/DatProcesador/claves/configurar/<?php echo $curso['id']; ?>" class="btn-action btn-blue">
                                <span>🔑</span> Claves
                            </a>
                            <a href="/DatProcesador/procesador/subir/<?php echo $curso['id']; ?>" class="btn-action btn-dark">
                                <span>📄</span> .DAT
                            </a>
                            <a href="/DatProcesador/procesador/resultados/<?php echo $curso['id']; ?>" class="btn-action btn-success">
                                <span>🏆</span> Ver Resultados Finales
                            </a>
                        </div>
                        
                        <div class="btn-group-edit">
                            <a href="/DatProcesador/cursos/editar/<?php echo $curso['id']; ?>" class="link-action link-edit">
                                <span>✏️</span> Editar
                            </a>
                            <a href="/DatProcesador/cursos/eliminar/<?php echo $curso['id']; ?>" class="link-action link-delete" onclick="return confirm('¿Eliminar curso?')">
                                <span>🗑️</span> Borrar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; padding: 60px; text-align: center; color: #a0aec0;">
                    <p style="font-size:1.2em; font-weight: 600;">No hay cursos disponibles.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>