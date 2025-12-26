<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cuadro de Méritos | DATPROCESADOR</title>
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', sans-serif; padding: 30px; }
        .container { max-width: 1300px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #2d3748; color: white; padding: 12px; text-align: left; font-size: 0.85em; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; }
        .score { font-weight: 800; color: #2b6cb0; font-size: 1.1em; }
        .badge-st { background: #fff5f5; color: #c53030; font-weight: bold; padding: 4px 10px; border-radius: 6px; border: 1px solid #feb2b2; }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h2>🏆 Cuadro de Méritos: <?php echo htmlspecialchars($curso['nombre_curso']); ?></h2>
        <div style="display:flex; gap:10px;">
            <a href="/DatProcesador/procesador/auditoria/<?php echo $curso['id']; ?>" style="background:#4a5568; color:white; padding:8px 15px; border-radius:8px; text-decoration:none; font-weight:bold;">🔍 Auditoría de Lectura</a>
            <a href="/DatProcesador/" style="text-decoration:none; color:#3182ce; font-weight:bold; border: 1px solid; padding: 8px; border-radius: 8px;">Dashboard</a>
        </div>
        <div style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
    <a href="/DatProcesador/procesador/exportarExcel/<?php echo $curso['id']; ?>" 
       style="background-color: #2f855a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: flex; align-items: center; gap: 8px;">
       📊 Descargar Reporte Excel
    </a>
</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>DNI</th>
                <th>Alumno</th>
                <th style="text-align:center;">Tipo</th>
                <th style="text-align:center;">Correctas</th>
                <th style="text-align:center;">Incorrectas</th>
                <th style="text-align:center;">Vacías</th>
                <th>Puntaje Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultados as $r): ?>
                <tr>
                    <td><code><?php echo $r['alumno_id']; ?></code></td>
                    <td style="text-transform:uppercase; font-size:0.9em; font-weight:600;">
                        <?php echo !empty($r['nombre_completo']) ? htmlspecialchars($r['nombre_completo']) : 'DNI NO VINCULADO'; ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if($r['tipo_examen'] == 'S/T'): ?>
                            <span class="badge-st">S/T</span>
                        <?php else: ?>
                            <span style="background:#edf2f7; padding:4px 10px; border-radius:6px; font-weight:bold;"><?php echo $r['tipo_examen']; ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="color:#38a169; font-weight:bold; text-align:center;"><?php echo $r['respuestas_correctas']; ?></td>
                    <td style="color:#e53e3e; font-weight:bold; text-align:center;"><?php echo $r['respuestas_incorrectas']; ?></td>
                    <td style="text-align:center;"><?php echo $r['respuestas_vacias']; ?></td>
                    <td class="score"><?php echo number_format($r['puntaje_total'], 4); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>