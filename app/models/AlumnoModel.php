<?php

class AlumnoModel extends Database {

    public function listarTodos() {
        try {
            $dbh = $this->getDbh();
            // Selecciona todos los campos, incluyendo fase y descripcion
            $stmt = $dbh->query("SELECT * FROM alumnos ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function obtenerPorId($id) {
        $dbh = $this->getDbh();
        $stmt = $dbh->prepare("SELECT * FROM alumnos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Inserta un alumno o actualiza sus datos si el DNI ya existe
     */
    public function insertar($dni, $nombre, $fase, $descripcion) {
        $dbh = $this->getDbh();
        // Se añade la columna 'fase' y la lógica de actualización por duplicado
        $sql = "INSERT INTO alumnos (dni, nombre_completo, fase, descripcion) 
                VALUES (:dni, :nombre, :fase, :desc)
                ON DUPLICATE KEY UPDATE 
                nombre_completo = VALUES(nombre_completo), 
                fase = VALUES(fase), 
                descripcion = VALUES(descripcion)";
        
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([
            ':dni'    => $dni, 
            ':nombre' => $nombre, 
            ':fase'   => $fase, 
            ':desc'   => $descripcion
        ]);
    }

    /**
     * Actualiza un registro específico desde el formulario de edición
     */
    public function actualizar($id, $dni, $nombre, $fase, $descripcion) {
        $dbh = $this->getDbh();
        // Se incluye 'fase' en la sentencia UPDATE
        $sql = "UPDATE alumnos SET 
                dni = :dni, 
                nombre_completo = :nombre, 
                fase = :fase, 
                descripcion = :desc 
                WHERE id = :id";
        
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([
            ':dni'    => $dni, 
            ':nombre' => $nombre, 
            ':fase'   => $fase, 
            ':desc'   => $descripcion, 
            ':id'     => $id
        ]);
    }

    public function eliminarPorId($id) {
        $dbh = $this->getDbh();
        $stmt = $dbh->prepare("DELETE FROM alumnos WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Reinicia la tabla y el contador AUTO_INCREMENT
     */
    public function truncarAlumnos() {
        try {
            $dbh = $this->getDbh();
            return $dbh->exec("TRUNCATE TABLE alumnos") !== false;
        } catch (PDOException $e) { return false; }
    }
}