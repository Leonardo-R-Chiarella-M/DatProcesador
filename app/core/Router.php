<?php

// Clase sin namespace
class Router { 
    
    public static function run() {
        
        $url = self::parseUrl();
        
        $controller = 'DatosController'; 
        $method = 'dashboard';
        $params = [];

        if (!empty($url[0])) {
            $controller = ucfirst($url[0]) . 'Controller';
        }
        if (!empty($url[1])) {
            $method = $url[1];
        }
        $params = array_slice($url, 2);

        try {
            // Buscamos la clase en el ámbito global (sin namespace)
            if (!class_exists($controller)) {
                $controller = 'DatosController';
                $method = 'dashboard';
                $params = $url;
                
                if (!class_exists($controller)) {
                    throw new \Exception("Controlador base 'DatosController' no encontrado (Fallo secundario).");
                }
            }

            $controllerInstance = new $controller();

            if (method_exists($controllerInstance, $method)) {
                call_user_func_array([$controllerInstance, $method], $params);
            } else {
                header("HTTP/1.0 404 Not Found");
                // DatosController funciona sin namespace
                DatosController::showError("404 No Encontrado", "El método '{$method}' no existe en el controlador '{$controller}'.");
            }

        } catch (\Throwable $e) {
            DatosController::showError("Error Interno (500)", $e->getMessage() . "\nStack Trace:\n" . $e->getTraceAsString());
        }
    }

    private static function parseUrl() {
        if (isset($_GET['url'])) {
            $url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return []; 
    }
}