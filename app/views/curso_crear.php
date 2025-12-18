<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Curso | DATPROCESADOR</title>
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; border-top: 5px solid #38a169; }
        h2 { margin-top: 0; color: #2d3748; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #4a5568; }
        input { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
        .btn-save { background: #38a169; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1em; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #a0aec0; text-decoration: none; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="card">
    <h2>➕ Nuevo Curso</h2>
    <form action="/DatProcesador/cursos/guardar" method="POST">
        <div class="form-group">
            <label>Nombre del Curso:</label>
            <input type="text" name="nombre_curso" placeholder="Ej: Matemática" required>
        </div>
        <div class="form-group">
            <label>Cantidad de Preguntas:</label>
            <input type="number" name="cantidad_preguntas" min="1" max="100" value="50" required>
        </div>
        <button type="submit" class="btn-save">Crear Curso</button>
        <a href="/DatProcesador/" class="btn-cancel">Cancelar</a>
    </form>
</div>

</body>
</html>