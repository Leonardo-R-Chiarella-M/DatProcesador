<?php

class Router { 
    
    public static function run() {
        $url = self::parseUrl();
        
        // Valores por defecto para la raíz
        if (empty($url[0])) {
            require_once '../app/controllers/DatosController.php';
            $controllerInstance = new DatosController();
            $controllerInstance->dashboard();
            return;
        }

        $controllerName = ucfirst($url[0]) . 'Controller';
        $method = $url[1] ?? 'index'; // Si no hay método, busca 'index'
        $params = array_slice($url, 2);

        $controllerFile = '../app/controllers/' . $controllerName . '.php';

        // 1. Validar si el ARCHIVO existe
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            
            // 2. Validar si la CLASE existe dentro del archivo
            if (class_exists($controllerName)) {
                $controllerInstance = new $controllerName();

                // 3. Validar si el MÉTODO existe en la clase
                if (method_exists($controllerInstance, $method)) {
                    call_user_func_array([$controllerInstance, $method], $params);
                } else {
                    self::lanzarError("El método '{$method}' no existe.");
                }
            } else {
                self::lanzarError("La clase '{$controllerName}' no está definida.");
            }
        } else {
            // AQUÍ ESTÁ LA LÓGICA QUE BUSCABAS: Si no encuentra el archivo, ERROR.
            self::lanzarError("El controlador '{$controllerName}' no existe.");
        }
    }

    private static function lanzarError($mensaje) {
        header("HTTP/1.0 404 Not Found");
        // Asegúrate de que DatosController esté disponible para mostrar el error
        if (!class_exists('DatosController')) {
            require_once '../app/controllers/DatosController.php';
        }
        DatosController::showError("404 - No Encontrado", $mensaje);
    }

    private static function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return []; 
    }
}