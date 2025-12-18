<?php

// Asegúrate de que el modelo esté disponible (si no usas autoloader, descomenta esto)
// require_once '../app/models/CursoModel.php';

class DatosController {

    // ✅ MÉTODO CORREGIDO: Ahora sí carga los cursos
    public function dashboard() {
        
        // 1. Instanciamos el modelo
        $model = new CursoModel();

        // 2. Obtenemos los datos de la base de datos
        // Asegúrate de que CursoModel.php tenga el método listarTodos()
        $cursos = $model->listarTodos(); 

        // 3. Enviamos los datos a la vista con la clave 'cursos'
        $this->render('dashboard', [
            'cursos' => $cursos
        ]); 
    }
    
    /**
     * Función estática para mostrar errores (requiere views/error.php).
     * @param string $message Mensaje principal de error.
     * @param string|null $trace Detalle técnico o stack trace.
     */
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
    
    /**
     * Función protegida para cargar vistas.
     * @param string $view Nombre de la vista (sin extensión .php).
     * @param array $data Datos a pasar a la vista (se convierten en variables).
     */
    protected function render($view, $data = []) {
        // Definimos APP_PATH si no existe para evitar errores de ruta
        if (!defined('APP_PATH')) {
             define('APP_PATH', dirname(__DIR__)); 
        }

        extract($data); 
        
        $viewPath = APP_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            self::showError("Vista no encontrada", "El archivo de vista '{$view}.php' no se encontró en la ruta esperada: " . $viewPath);
        }
    }

    /**
     * Consulta datos usando una sentencia SQL (Requiere Database.php).
     * Este método se usa en AlumnosController::verTabla().
     * @param string $sql La sentencia SQL a ejecutar.
     * @param array $params Parámetros para la sentencia preparada (opcional).
     * @return array Resultado de la consulta.
     */
    protected function query($sql, $params = []) {
        try {
            // Instanciamos el modelo base para obtener la conexión PDO (dbh)
            $db = new Database(); 
            $dbh = $db->getDbh();
            
            $stmt = $dbh->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            self::showError("Error de Consulta de Datos", "Fallo al ejecutar la consulta: " . $e->getMessage());
            return [];
        }
    }
}