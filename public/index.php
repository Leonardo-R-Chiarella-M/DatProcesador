<?php

// ---------------------------------------------------------
// 1. Configuración Inicial y Rutas
// ---------------------------------------------------------

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Define la ruta raíz del proyecto (DATPROCESADOR)
// Subimos solo un nivel (de /public/ a /DATPROCESADOR/)
define('ROOT_PATH', dirname(__DIR__)); 
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app'); 


// ---------------------------------------------------------
// 2. Autocarga de Clases (Simplificado, sin Namespaces)
// ---------------------------------------------------------

spl_autoload_register(function ($class) {
    
    $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $class); 

    // Busca clases en las carpetas principales (controllers, models, core)
    $paths_to_check = [
        APP_PATH . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $class_path . '.php', 
        APP_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $class_path . '.php',       
        APP_PATH . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . $class_path . '.php', 
    ];

    foreach ($paths_to_check as $file) {
        if (file_exists($file)) {
            require_once $file;
            return; 
        }
    }
});


// ---------------------------------------------------------
// 3. Inicio del Enrutamiento
// ---------------------------------------------------------

// Verificación de la clase base
if (!class_exists('DatosController')) {
    die("Error crítico (500): DatosController no cargado.");
}

// Verificación de existencia del Router (Clase global)
if (!class_exists('Router')) {
    DatosController::showError(
        "Error crítico de carga: La clase Router no se pudo cargar.",
        "Asegúrese de que el archivo Router.php está en 'app/core/' y NO tiene un namespace." 
    );
}

// Inicia el proceso de enrutamiento
Router::run();