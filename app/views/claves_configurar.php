<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configurar Claves | DATPROCESADOR</title>
    <style>
        body { background-color: #f1f5f9; font-family: 'Segoe UI', sans-serif; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 6px solid #3182ce; }
        .import-box { background: #fffaf0; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px dashed #ed8936; display: flex; justify-content: space-between; align-items: center; }
        .tabs { display: flex; gap: 8px; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .tab-link { padding: 10px 20px; border-radius: 6px; text-decoration: none; color: #4a5568; background: #edf2f7; font-weight: bold; }
        .tab-link.active { background: #3182ce; color: white; }
        .grid-claves { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 15px; }
        .card-p { background: #fff; border: 1px solid #e2e8f0; padding: 12px; border-radius: 10px; transition: 0.2s; }
        .card-p:hover { border-color: #3182ce; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        select, input { padding: 8px; border: 1px solid #cbd5e0; border-radius: 6px; width: 100%; box-sizing: border-box; font-weight: bold; margin-top: 5px; }
        .btn-save { background: #38a169; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 25px; font-size: 1.1em; }
        .alert { background: #c6f6d5; color: #22543d; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; border: 1px solid #9ae6b4; }
    </style>
</head>
<body>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="margin:0;">🔑 Configurar Claves: <?php echo htmlspecialchars($curso['nombre_curso']); ?></h2>
        <a href="/DatProcesador/" style="text-decoration:none; color:#718096; font-weight:bold;">Volver al Dashboard</a>
    </div>

    <div class="tabs">
        <?php foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $t): ?>
            <a href="/DatProcesador/claves/configurar/<?php echo $curso['id']; ?>/<?php echo $t; ?>" 
               class="tab-link <?php echo ($tipo_actual == $t) ? 'active' : ''; ?>">Tipo <?php echo $t; ?></a>
        <?php endforeach; ?>
    </div>

    <div class="import-box">
        <div>
            <p style="margin:0; font-size:0.9em; color:#9c4221;"><strong>Carga Masiva para Tipo <?php echo $tipo_actual; ?>:</strong></p>
            <small>Formato: <b>Respuesta;Valor</b> (Ej: A;0.66). Se lee desde la fila 1.</small>
        </div>
        <form action="/DatProcesador/claves/importarMasivo" method="POST" enctype="multipart/form-data" style="display:flex; gap:10px;">
            <input type="hidden" name="curso_id" value="<?php echo $curso['id']; ?>">
            <input type="hidden" name="tipo_examen" value="<?php echo $tipo_actual; ?>">
            <input type="file" name="archivo_claves" accept=".csv" required>
            <button type="submit" style="background:#ed8936; color:white; border:none; padding:8px 15px; border-radius:5px; cursor:pointer; font-weight:bold;">Importar CSV</button>
        </form>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <div class="alert">✅ Claves actualizadas correctamente en la base de datos.</div>
    <?php endif; ?>

    <form action="/DatProcesador/claves/guardar" method="POST">
        <input type="hidden" name="curso_id" value="<?php echo $curso['id']; ?>">
        <input type="hidden" name="tipo_examen" value="<?php echo $tipo_actual; ?>">
        
        <div class="grid-claves">
            <?php for($i = 1; $i <= $curso['cantidad_preguntas']; $i++): 
                // Buscamos la respuesta y el valor cargados desde el controlador
                $letra = isset($datos_claves[$i]['letra']) ? $datos_claves[$i]['letra'] : '';
                $valor = isset($datos_claves[$i]['valor']) ? $datos_claves[$i]['valor'] : '1.00';
            ?>
                <div class="card-p">
                    <label style="font-size:0.8em; font-weight:bold; color:#4a5568;">Pregunta <?php echo $i; ?></label>
                    <select name="letras[<?php echo $i; ?>]">
                        <option value="">--</option>
                        <?php foreach(['A','B','C','D','E'] as $op): ?>
                            <option value="<?php echo $op; ?>" <?php echo (trim($letra) === $op) ? 'selected' : ''; ?>>
                                <?php echo $op; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" step="0.0001" name="valores[<?php echo $i; ?>]" value="<?php echo $valor; ?>">
                </div>
            <?php endfor; ?>
        </div>

        <button type="submit" class="btn-save">💾 Guardar Cambios para Tipo <?php echo $tipo_actual; ?></button>
    </form>
</div>

</body>
</html>