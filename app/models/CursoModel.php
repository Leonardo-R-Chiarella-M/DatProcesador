<?php

/**
 * Modelo para la gestión de cursos y exámenes.
 * Hereda de la clase Database para obtener la conexión PDO.
 */
class CursoModel extends Database {

    /**
     * Obtiene la lista completa de cursos para el Dashboard.
     * @return array Arreglo asociativo con los datos de los cursos.
     */
    public function listarTodos() {
        try {
            $dbh = $this->getDbh();
            // Seleccionamos los campos necesarios ordenados por el más reciente
            $stmt = $dbh->query("SELECT id, nombre_curso, cantidad_preguntas FROM cursos ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En caso de error, devolvemos un array vacío para no romper la vista
            return [];
        }
    }

    /**
     * Inserta un nuevo curso de forma manual.
     * @param string $nombre Nombre descriptivo del curso.
     * @param int $preguntas Cantidad de preguntas del examen.
     * @return bool True si la inserción fue exitosa.
     */
    public function insertarCursoManual($nombre, $preguntas) {
        try {
            $dbh = $this->getDbh();
            $sql = "INSERT INTO cursos (nombre_curso, cantidad_preguntas) VALUES (:nombre, :preguntas)";
            $stmt = $dbh->prepare($sql);
            return $stmt->execute([
                ':nombre' => $nombre, 
                ':preguntas' => $preguntas
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Obtiene los datos de un curso específico por su ID.
     * Útil para cargar el formulario de edición.
     * @param int $id ID del curso.
     * @return array|null Datos del curso o null si no existe.
     */
    public function obtenerPorId($id) {
        try {
            $dbh = $this->getDbh();
            $stmt = $dbh->prepare("SELECT * FROM cursos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Actualiza la información de un curso existente.
     * @param int $id ID del curso a modificar.
     * @param string $nombre Nuevo nombre.
     * @param int $preguntas Nueva cantidad de preguntas.
     * @return bool True si se actualizó correctamente.
     */
    public function actualizarCurso($id, $nombre, $preguntas) {
        try {
            $dbh = $this->getDbh();
            $sql = "UPDATE cursos SET nombre_curso = :nombre, cantidad_preguntas = :preguntas WHERE id = :id";
            $stmt = $dbh->prepare($sql);
            return $stmt->execute([
                ':nombre' => $nombre,
                ':preguntas' => $preguntas,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Elimina un curso permanentemente de la base de datos.
     * @param int $id ID del curso a borrar.
     * @return bool True si la eliminación fue exitosa.
     */
    public function eliminarCurso($id) {
        try {
            $dbh = $this->getDbh();
            // Nota: Asegúrate de que en la BD las tablas relacionadas tengan ON DELETE CASCADE
            $stmt = $dbh->prepare("DELETE FROM cursos WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}