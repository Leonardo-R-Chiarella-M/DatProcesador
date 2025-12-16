<?php

// AlumnoModel DEBE HEREDAR de Database
class AlumnoModel extends Database {
    private $errores = [];
    private $dataValida = [];

    // Constructor
    public function __construct() {
        parent::__construct(); 
    }

    /**
     * Procesa y valida una línea de datos de alumno. Incluye limpieza de BOM.
     */
    public function validarLinea(array $linea, int $numeroLinea): bool {
        $lineaValida = true;
        
        $primerCampo = trim($linea[0] ?? '');
        $nombre = trim($linea[1] ?? '');
        $descripcion = trim($linea[2] ?? '');
        
        // LIMPIEZA DE BOM
        if ($numeroLinea === 1 && substr($primerCampo, 0, 3) == "\xEF\xBB\xBF") {
            $primerCampo = substr($primerCampo, 3);
        }

        $alumno = [
            'dni' => $primerCampo, 
            'nombre_completo' => $nombre,
            'descripcion' => $descripcion,
        ];

        // 1. Verificar el número de campos (mínimo 3)
        if (count($linea) < 3) {
            $this->errores[] = "Línea {$numeroLinea}: Faltan campos. Se esperaban 3 (DNI;Nombre;Desc).";
            return false;
        }

        // 2. Validación DNI (numérico y 8 dígitos)
        if (empty($alumno['dni'])) {
            $this->errores[] = "Línea {$numeroLinea}: El campo DNI no puede estar vacío.";
            $lineaValida = false;
        } elseif (!ctype_digit($alumno['dni']) || strlen($alumno['dni']) != 8) {
            $this->errores[] = "Línea {$numeroLinea}: El DNI '{$alumno['dni']}' debe ser numérico y tener 8 dígitos.";
            $lineaValida = false;
        }

        // 3. Validación Nombre Completo
        if (empty($alumno['nombre_completo'])) {
            $this->errores[] = "Línea {$numeroLinea}: El campo Nombre Completo no puede estar vacío.";
            $lineaValida = false;
        }

        if ($lineaValida) {
            $this->dataValida[] = $alumno;
            return true;
        }

        return false;
    }

    /**
     * Inserta TODOS los datos validados, ya que el DNI ya NO es clave única.
     * @return array ['insertados', 'duplicados_db', 'duplicados_archivo', 'duplicados_dni_archivo']
     */
    public function insertarDatosValidados(): array {
        $insertados = 0;
        $dbh = $this->getDbh(); 
        
        $sql_insert = "INSERT INTO alumnos (dni, nombre_completo, descripcion) VALUES (:dni, :nombre, :desc)";
        $stmt_insert = $dbh->prepare($sql_insert); 
        
        foreach ($this->dataValida as $alumno) {
            $dni = $alumno['dni'];
            
            $stmt_insert->bindValue(':dni', $dni);
            $stmt_insert->bindValue(':nombre', $alumno['nombre_completo']);
            $stmt_insert->bindValue(':desc', $alumno['descripcion']);
            
            $stmt_insert->execute();

            if ($stmt_insert->rowCount() > 0) {
                $insertados++;
            }
        }
        
        // Retornamos el resultado (duplicados = 0, ya que se insertó todo)
        return [
            'insertados' => $insertados,
            'duplicados_db' => [], 
            'duplicados_archivo' => 0, 
            'duplicados_dni_archivo' => [] 
        ];
    }
    
    /**
     * Obtiene un alumno específico por su ID.
     */
    public function getAlumnoById(int $id) {
        $dbh = $this->getDbh();
        try {
            $sql = "SELECT id, dni, nombre_completo, descripcion FROM alumnos WHERE id = :id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new Exception("Error al obtener alumno por ID: " . $e->getMessage());
        }
    }

    /**
     * Actualiza los datos de un alumno existente.
     */
    public function guardarAlumno(array $datos_alumno): bool {
        $dbh = $this->getDbh();
        try {
            $sql = "UPDATE alumnos SET dni = :dni, nombre_completo = :nombre, descripcion = :desc WHERE id = :id";
            $stmt = $dbh->prepare($sql);
            
            $stmt->bindValue(':dni', $datos_alumno['dni']);
            $stmt->bindValue(':nombre', $datos_alumno['nombre_completo']);
            $stmt->bindValue(':desc', $datos_alumno['descripcion']);
            $stmt->bindValue(':id', $datos_alumno['id'], PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
            
        } catch (\PDOException $e) {
            throw new Exception("Error al actualizar alumno: " . $e->getMessage());
        }
    }

    /**
     * VACÍA la tabla 'alumnos' completamente y reinicia el auto-incremento (TRUNCATE).
     */
    public function eliminarTodosLosAlumnos(): bool {
        $dbh = $this->getDbh();
        try {
            $sql = "TRUNCATE TABLE alumnos";
            $dbh->exec($sql);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Error al truncar la tabla: " . $e->getMessage());
        }
    }

    // Métodos Getters
    public function getErrores(): array {
        return $this->errores;
    }

    public function getDataValida(): array {
        return $this->dataValida;
    }
}