<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procesar .DAT | DATPROCESADOR</title>
    <style>
        body { background-color: #f1f5f9; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 450px; text-align: center; border-top: 6px solid #2d3748; }
        .file-zone { border: 2px dashed #cbd5e0; padding: 40px 20px; border-radius: 12px; margin: 25px 0; background: #f8fafc; cursor: pointer; transition: 0.3s; }
        .file-zone:hover { border-color: #3182ce; background: #ebf8ff; }
        .btn-process { background: #2d3748; color: white; border: none; padding: 15px; border-radius: 10px; font-weight: bold; cursor: pointer; width: 100%; font-size: 1.1em; transition: 0.2s; }
        .btn-process:hover { background: #1a202c; transform: translateY(-2px); }
        .back-link { display: block; margin-top: 20px; color: #718096; text-decoration: none; font-size: 0.9em; font-weight: bold; }
    </style>
</head>
<body>

<div class="card">
    <h2 style="margin:0; color:#2d3748;">📄 Cargar Archivo .DAT</h2>
    <p style="color:#718096; margin-top:10px;">Curso: <strong><?php echo htmlspecialchars($curso['nombre_curso']); ?></strong></p>

    <form action="/DatProcesador/procesador/procesar" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="curso_id" value="<?php echo $curso['id']; ?>">
        
        <div class="file-zone" onclick="document.getElementById('archivo_dat').click()">
            <div style="font-size: 40px; margin-bottom: 10px;">📁</div>
            <p id="file-name" style="margin:0; color:#4a5568;">Selecciona o arrastra el archivo .DAT</p>
            <input type="file" name="archivo_dat" id="archivo_dat" style="display:none;" onchange="document.getElementById('file-name').textContent = this.files[0].name" required>
        </div>

        <button type="submit" class="btn-process">🚀 Iniciar Calificación</button>
    </form>
    
    <a href="/DatProcesador/" class="back-link">← Cancelar y volver</a>
</div>

</body>
</html>