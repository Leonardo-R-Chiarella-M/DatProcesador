<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Curso | DATPROCESADOR</title>
    <link rel="stylesheet" href="/DatProcesador/public/css/dashboard.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', sans-serif; color: #2d3748; }
        .container { padding: 50px; max-width: 600px; margin: auto; }
        
        .card-edit {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border-top: 5px solid #3182ce;
        }

        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 8px; color: #4a5568; }
        
        input[type="text"], 
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-sizing: border-box; /* Importante para el ancho total */
            font-size: 1em;
        }

        .btn-save {
            background: #3182ce;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            font-size: 1em;
            transition: 0.2s;
        }
        .btn-save:hover { background: #2b6cb0; }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #a0aec0;
            text-decoration: none;
            font-size: 0.9em;
        }
        .btn-cancel:hover { color: #718096; }
    </style>
</head>
<body>

<div class="container">
    <div class="card-edit">
        <h2 style="margin-top:0; color: #2d3748;">✏️ Editar Curso</h2>
        <p style="color: #718096; font-size: 0.9em; margin-bottom: 25px;">Modifica los detalles del curso seleccionado.</p>

        <form action="/DatProcesador/cursos/actualizar" method="POST">
            <input type="hidden" name="id" value="<?php echo $curso['id']; ?>">

            <div class="form-group">
                <label for="nombre_curso">Nombre del Curso:</label>
                <input type="text" name="nombre_curso" id="nombre_curso" 
                       value="<?php echo htmlspecialchars($curso['nombre_curso']); ?>" required>
            </div>

            <div class="form-group">
                <label for="cantidad_preguntas">Cantidad de Preguntas:</label>
                <input type="number" name="cantidad_preguntas" id="cantidad_preguntas" 
                       value="<?php echo $curso['cantidad_preguntas']; ?>" min="1" max="100" required>
            </div>

            <button type="submit" class="btn-save">Guardar Cambios</button>
            <a href="/DatProcesador/" class="btn-cancel">Cancelar y Volver</a>
        </form>
    </div>
</div>

</body>
</html>