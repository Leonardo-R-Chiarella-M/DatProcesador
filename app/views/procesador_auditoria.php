<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Auditoría de Lectura | DATPROCESADOR</title>
    <style>
        body { background: #1a202c; color: #e2e8f0; font-family: 'Segoe UI', sans-serif; padding: 20px; }
        .success-banner { background: #2f855a; color: white; padding: 12px; border-radius: 8px; text-align: center; margin-bottom: 20px; font-weight: bold; }
        .edit-card { background: #2d3748; padding: 20px; margin-bottom: 20px; border-radius: 12px; border: 1px solid #4a5568; }
        .label-style { font-size: 0.7em; color: #63b3ed; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; display: block; }
        input, select { background: #1a202c; border: 1px solid #4a5568; color: white; padding: 10px; border-radius: 6px; font-weight: bold; }
        .respuestas-input { 
            font-family: 'Courier New', monospace; letter-spacing: 5px; font-size: 1.25em; color: #4ade80; 
            width: 100%; border: 2px solid #38a169; background: #000; text-transform: uppercase; 
        }
        .btn-update { background: #38a169; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .raw-ref { font-size: 0.8em; color: #718096; margin-top: 15px; border-top: 1px solid #4a5568; padding-top: 10px; font-family: monospace; }
    </style>
</head>
<body>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
    <h2>🔍 Auditoría Técnica: <?php echo htmlspecialchars($curso['nombre_curso']); ?></h2>
    <a href="/DatProcesador/procesador/resultados/<?php echo $curso['id']; ?>" style="color:#63b3ed; font-weight:bold; text-decoration:none; border: 1px solid; padding: 10px 20px; border-radius: 8px;">← Ir a Cuadro de Méritos</a>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="success-banner">✅ Cambios guardados y alumno recalificado correctamente.</div>
<?php endif; ?>

<?php foreach ($auditoria as $r): ?>
    <div class="edit-card">
        <form action="/DatProcesador/procesador/actualizarExamen" method="POST">
            <input type="hidden" name="curso_id" value="<?php echo $r['curso_id']; ?>">
            <input type="hidden" name="dni_original" value="<?php echo $r['alumno_id']; ?>">

            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div>
                    <label class="label-style">DNI Alumno:</label>
                    <input type="text" name="nuevo_dni" value="<?php echo $r['alumno_id']; ?>" maxlength="8" style="width: 110px;">
                </div>
                <div>
                    <label class="label-style">Tipo Examen:</label>
                    <select name="nuevo_tipo" style="width: 100px;">
                        <option value="A" <?php echo ($r['tipo_examen']=='A')?'selected':''; ?>>A</option>
                        <option value="B" <?php echo ($r['tipo_examen']=='B')?'selected':''; ?>>B</option>
                        <option value="C" <?php echo ($r['tipo_examen']=='C')?'selected':''; ?>>C</option>
                        <option value="S/T" <?php echo ($r['tipo_examen']=='S/T' || $r['tipo_examen']=='S')?'selected':''; ?>>S/T</option>
                    </select>
                </div>
                <div style="flex-grow: 1;">
                    <label class="label-style">Nombre del Alumno:</label>
                    <input type="text" value="<?php echo $r['nombre_completo'] ?? 'NO VINCULADO'; ?>" disabled style="width: 100%; opacity: 0.5;">
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label class="label-style">Cadena de Respuestas (Editable):</label>
                <input type="text" name="nuevas_respuestas" 
                       value="<?php 
                            if (empty(trim($r['respuestas_alumno']))) {
                                $prefijo = "514489";
                                $posPrefijo = strpos($r['linea_original'], $prefijo);
                                echo htmlspecialchars(substr($r['linea_original'], $posPrefijo + 15, 30));
                            } else {
                                echo htmlspecialchars($r['respuestas_alumno']);
                            }
                       ?>" 
                       maxlength="30" class="respuestas-input">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: bold; font-size: 0.9em;">
                    Puntaje: <span style="color:#63b3ed; font-size: 1.2em;"><?php echo number_format($r['puntaje_total'], 4); ?></span>
                </div>
                <button type="submit" class="btn-update">💾 Guardar y Recalificar</button>
            </div>

            <div class="raw-ref">
                Referencia Original Lector: <code><?php echo htmlspecialchars($r['linea_original']); ?></code>
            </div>
        </form>
    </div>
<?php endforeach; ?>

</body>
</html>