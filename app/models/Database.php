<?php

class Database {
    private $host = 'localhost';
    private $user = 'root';   // <-- ¡VERIFICA TUS CREDENCIALES!
    private $pass = '';       // <-- ¡VERIFICA TUS CREDENCIALES!
    private $dbname = 'DatProcesador';
    private $dbh;             // Database Handler
    private $error;

    public function __construct() {
        // DSN (Data Source Name)
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        
        // Opciones de configuración de PDO
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // CRÍTICO para consultas IN (?) y execute([array])
            PDO::ATTR_EMULATE_PREPARES => false, 
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (\PDOException $e) {
            $this->error = $e->getMessage();
            // Muestra un error visible si la conexión falla
            die("Error de conexión a la Base de Datos: " . $this->error);
        }
    }

    // Método para exponer la conexión PDO a los modelos
    public function getDbh() {
        return $this->dbh;
    }
}