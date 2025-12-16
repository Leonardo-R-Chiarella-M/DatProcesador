<?php

// ---------------------------------------------------------
// 1. Configuración Inicial y Rutas
// ---------------------------------------------------------

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión (Necesario si se usaran variables de sesión más adelante)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define la ruta raíz del proyecto (DATPROCESADOR)
define('ROOT_PATH', dirname(__DIR__)); 
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app'); 


// ---------------------------------------------------------
// 2. Autocarga de Clases (Simplificado)
// ---------------------------------------------------------

spl_autoload_register(function ($className) {
    
    $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $className); 

    // Rutas para buscar Clases (Controladores, Modelos, Core)
    $paths_to_check = [
        APP_PATH . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $class_path . '.php', 
        // Importante: Aquí se buscan AlumnoModel.php y Database.php
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

// Verificación de la clase base (DatosController)
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