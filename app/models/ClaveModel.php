<?php

class ClaveModel extends Database {

    /**
     * Obtiene las claves de un curso específico y un tipo de examen (A, B, C...)
     */
    public function obtenerClavesPorTipo($curso_id, $tipo_examen) {
        try {
            $dbh = $this->getDbh();
            $stmt = $dbh->prepare("SELECT num_pregunta, respuesta, valor 
                                   FROM claves 
                                   WHERE curso_id = :id AND tipo_examen = :tipo 
                                   ORDER BY num_pregunta ASC");
            $stmt->execute([':id' => $curso_id, ':tipo' => $tipo_examen]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Borra las claves existentes e inserta las nuevas de forma masiva
     */
    public function guardarClavesMasivo($curso_id, $tipo_examen, $datos) {
        try {
            $dbh = $this->getDbh();
            $dbh->beginTransaction();

            // 1. Limpiar claves previas de este tipo
            $stmtDel = $dbh->prepare("DELETE FROM claves WHERE curso_id = :id AND tipo_examen = :tipo");
            $stmtDel->execute([':id' => $curso_id, ':tipo' => $tipo_examen]);

            // 2. Insertar las nuevas
            $stmtIns = $dbh->prepare("INSERT INTO claves (curso_id, tipo_examen, num_pregunta, respuesta, valor) 
                                     VALUES (?, ?, ?, ?, ?)");
            
            foreach ($datos as $item) {
                $stmtIns->execute([
                    $curso_id, 
                    $tipo_examen, 
                    $item['num'], 
                    strtoupper($item['letra']), 
                    $item['valor']
                ]);
            }

            $dbh->commit();
            return true;
        } catch (PDOException $e) {
            $dbh->rollBack();
            return false;
        }
    }

    /**
     * Elimina absolutamente todas las claves de un curso (al borrar el curso)
     */
    public function eliminarTodasLasClaves($curso_id) {
        try {
            $dbh = $this->getDbh();
            $stmt = $dbh->prepare("DELETE FROM claves WHERE curso_id = :id");
            return $stmt->execute([':id' => $curso_id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}