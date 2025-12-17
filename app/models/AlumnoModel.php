<?php

class AlumnoModel extends Database {

    public function listarTodos() {
        try {
            $dbh = $this->getDbh();
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

    public function insertar($dni, $nombre, $descripcion) {
        $dbh = $this->getDbh();
        $sql = "INSERT INTO alumnos (dni, nombre_completo, descripcion) VALUES (:dni, :nombre, :desc)";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([':dni' => $dni, ':nombre' => $nombre, ':desc' => $descripcion]);
    }

    public function actualizar($id, $dni, $nombre, $descripcion) {
        $dbh = $this->getDbh();
        $sql = "UPDATE alumnos SET dni = :dni, nombre_completo = :nombre, descripcion = :desc WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([':dni' => $dni, ':nombre' => $nombre, ':desc' => $descripcion, ':id' => $id]);
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