<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido | DATPROCESADOR</title>
    
    <link rel="stylesheet" href="././public/css/dashboard.css">
    
</head>
<body>
    <div class="welcome-card">
        <h1>DATPROCESADOR</h1>
        <p>¡Bienvenido a la Pantalla Principal!</p>
        
        <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
            <h2>Cargar Archivo de Alumnos</h2>
            <form action="/DatProcesador/alumnos/procesar" method="POST" enctype="multipart/form-data">
                <label for="datFile">Seleccionar archivo (CSV/DAT):</label><br>
                <input type="file" name="datFile" id="datFile" accept=".dat, .csv" required style="margin-top: 10px;"><br>
                <button type="submit" style="margin-top: 15px; background-color: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
                    Procesar Alumnos
                </button>
            </form>
            <p style="font-size: 0.8em; color: #777; margin-top: 10px;">Formato esperado: DNI; Nombre Completo; Descripción (separado por punto y coma)</p>
        </div>
        
        <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #ddd;">
            <h2>Consulta de Datos</h2>
            <p>
                <a href="/DatProcesador/alumnos/verTabla" style="color: #007bff; text-decoration: none; font-weight: bold;">
                    ➡️ Ver todos los alumnos cargados
                </a>
            </p>
        </div>

        <div class="status-badge">
            Estructura MVC y DB Lista
        </div>

    </div>
</body>
</html>