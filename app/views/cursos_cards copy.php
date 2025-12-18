<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Cursos | DATPROCESADOR</title>
    <link rel="stylesheet" href="/DatProcesador/public/css/dashboard.css">
    <style>
        .grid-cursos { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .card-curso { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 5px solid #007bff; text-align: center; }
        .badge { background: #e7f3ff; color: #007bff; padding: 5px 10px; border-radius: 15px; font-size: 0.8em; font-weight: bold; }
        .btn-config { display: block; margin-top: 15px; background: #007bff; color: white; padding: 10px; border-radius: 4px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="welcome-card" style="max-width: 900px; margin: 40px auto;">
        <h1>📚 Mis Cursos</h1>
        <p>Gestiona las claves y exámenes de cada materia.</p>
        
        <div class="grid-cursos">
            <?php if (!empty($cursos)): ?>
                <?php foreach ($cursos as $curso): ?>
                <div class="card-curso">
                    <span class="badge"><?php echo $curso['cantidad_preguntas']; ?> Preguntas</span>
                    <h3 style="margin: 15px 0;"><?php echo htmlspecialchars($curso['nombre_curso']); ?></h3>
                    <a href="/DatProcesador/claves/subir/<?php echo $curso['id']; ?>" class="btn-config">Configurar Claves</a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1;">Aún no tienes cursos. <a href="/DatProcesador/cursos/crear">¡Crea el primero aquí!</a></p>
            <?php endif; ?>
        </div>
        
        <hr style="margin-top: 30px;">
        <a href="/DatProcesador/" style="display: inline-block; margin-top: 10px; color: #007bff; text-decoration: none; font-weight: bold;">⬅️ Volver al Dashboard Principal</a>
    </div>
</body>
</html>