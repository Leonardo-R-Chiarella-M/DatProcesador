<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Alumno - ID: <?php echo htmlspecialchars($alumno['id']); ?></title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/alumno.css">
    <style>
        /* Estilos específicos para formularios */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #333; }
        .form-group input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1em;
        }
        .form-actions { margin-top: 30px; }
        .btn-success { background-color: #5cb85c; color: white; border: none; cursor: pointer; }
        .btn-success:hover { background-color: #4cae4c; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Editar Alumno: <?php echo htmlspecialchars($alumno['nombre_completo']); ?></h1>
        
        <form action="<?php echo $base_url; ?>alumnos/actualizar" method="POST">
            
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($alumno['id']); ?>">

            <div class="form-group">
                <label for="dni">DNI</label>
                <input type="text" id="dni" name="dni" 
                       value="<?php echo htmlspecialchars($alumno['dni']); ?>" required>
            </div>

            <div class="form-group">
                <label for="nombre_completo">Nombre Completo</label>
                <input type="text" id="nombre_completo" name="nombre_completo" 
                       value="<?php echo htmlspecialchars($alumno['nombre_completo']); ?>" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <input type="text" id="descripcion" name="descripcion" 
                       value="<?php echo htmlspecialchars($alumno['descripcion']); ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                <a href="<?php echo $base_url; ?>alumnos/verTabla" class="btn">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>