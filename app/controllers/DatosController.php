<?php

class DatosController {

    // Método principal que se ejecuta al acceder a la URL base
    public function dashboard() {
        
        // No hay datos de prueba, solo un arreglo vacío.
        $data = []; 

        // Intenta cargar la vista 'dashboard.php'
        $this->render('dashboard', $data); 
    }
    
    // Función estática para mostrar errores (requiere views/error.php)
    public static function showError($message, $trace = null) {
        $data = ['message' => $message, 'trace' => $trace];
        if (!defined('APP_PATH')) {
            define('APP_PATH', dirname(__DIR__)); 
        }

        extract($data); 
        $viewPath = APP_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'error.php';
        
        if (file_exists($viewPath)) {
            http_response_code(500); 
            require_once $viewPath;
            exit;
        } else {
            die("Error Fatal: La vista de error no existe. Mensaje: " . $message);
        }
    }
    
    // Función protegida para cargar vistas
    protected function render($view, $data = []) {
        extract($data); 
        
        $viewPath = APP_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            self::showError("Vista no encontrada", "El archivo de vista '{$view}.php' no se encontró en la ruta esperada.");
        }
    }
}