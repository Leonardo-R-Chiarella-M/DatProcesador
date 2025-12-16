<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error del Sistema | DATPROCESADOR</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f8f8; padding: 20px; }
        .error-container { 
            max-width: 800px; 
            margin: 50px auto; 
            background-color: #fff; 
            border: 1px solid #ffdddd; 
            border-left: 5px solid #f44336; 
            padding: 20px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.05); 
        }
        .error-container h1 { color: #f44336; margin-top: 0; }
        .error-container h2 { color: #555; border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-top: 20px; }
        .error-container pre { background-color: #eee; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .error-container p { font-size: 1.1em; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>🚨 Error Crítico del Sistema</h1>
        
        <p><strong>Mensaje:</strong> <?php echo htmlspecialchars($message ?? 'Error desconocido'); ?></p>
        
        <?php if (isset($trace)): ?>
            <h2>Detalles Técnicos (Stack Trace)</h2>
            <pre><?php echo htmlspecialchars($trace); ?></pre>
        <?php endif; ?>
        
        <p>Por favor, contacte al administrador o revise los logs del servidor.</p>
        <p><a href="/">Volver al Dashboard</a></p>
    </div>
</body>
</html>