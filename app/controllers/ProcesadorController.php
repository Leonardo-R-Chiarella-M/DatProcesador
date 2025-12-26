<?php

class ProcesadorController extends DatosController {

    public function subir($curso_id) {
        $cursoModel = new CursoModel();
        $curso = $cursoModel->obtenerPorId($curso_id);
        if (!$curso) { header("Location: /DatProcesador/"); exit(); }
        $this->render('procesador_subir', ['curso' => $curso]);
    }

    /**
     * Procesa el archivo .DAT inicial
     */
    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_dat'])) {
            $curso_id = $_POST['curso_id'];
            $file = $_FILES['archivo_dat']['tmp_name'];
            $db = new Database(); $dbh = $db->getDbh();

            // Cargar claves maestras
            $stmt = $dbh->prepare("SELECT * FROM claves WHERE curso_id = :id");
            $stmt->execute([':id' => $curso_id]);
            $clavesMaster = [];
            while ($c = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $clavesMaster[$c['tipo_examen']][$c['num_pregunta']] = ['resp' => $c['respuesta'], 'val'  => $c['valor']];
            }

            $lineas = file($file);
            $dbh->beginTransaction();
            try {
                $stmtDel = $dbh->prepare("DELETE FROM resultados WHERE curso_id = :id");
                $stmtDel->execute([':id' => $curso_id]);

                foreach ($lineas as $linea) {
                    $lineaRaw = rtrim($linea); 
                    if (empty(trim($lineaRaw))) continue;

                    $prefijo = "514489";
                    $posPrefijo = strpos($lineaRaw, $prefijo);
                    
                    if ($posPrefijo !== false) {
                        $dni = substr($lineaRaw, $posPrefijo + 6, 8);
                        $caracterTipo = substr($lineaRaw, $posPrefijo + 14, 1);
                        
                        // EXTRACCIÓN GARANTIZADA: Saltamos prefijo(6)+DNI(8)+Tipo(1) = 15
                        $respuestas_alumno = substr($lineaRaw, $posPrefijo + 15, 30);

                        if ($caracterTipo === ' ' || empty(trim($caracterTipo)) || !preg_match('/[A-F]/', $caracterTipo)) {
                            $tipo_examen = "S/T";
                        } else {
                            $tipo_examen = strtoupper($caracterTipo);
                        }
                    } else { continue; }

                    $nota = 0; $correctas = 0; $incorrectas = 0; $vacias = 0;
                    $clavesConfig = $clavesMaster[$tipo_examen] ?? [];
                    
                    for ($i = 1; $i <= 30; $i++) {
                        $marcada = $respuestas_alumno[$i-1] ?? ' ';
                        if (trim($marcada) === '' || $marcada === ' ') { $vacias++; }
                        elseif (isset($clavesConfig[$i]) && $marcada === $clavesConfig[$i]['resp']) {
                            $nota += $clavesConfig[$i]['val']; $correctas++;
                        } else { $incorrectas++; }
                    }

                    // Guardar en la nueva columna respuestas_alumno
                    $stmtIns = $dbh->prepare("INSERT INTO resultados 
                        (curso_id, alumno_id, tipo_examen, puntaje_total, respuestas_correctas, respuestas_incorrectas, respuestas_vacias, respuestas_alumno, linea_original) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmtIns->execute([$curso_id, $dni, $tipo_examen, $nota, $correctas, $incorrectas, $vacias, $respuestas_alumno, $lineaRaw]);
                }
                $dbh->commit();
                header("Location: /DatProcesador/procesador/resultados/{$curso_id}");
            } catch (Exception $e) { $dbh->rollBack(); die("Error: " . $e->getMessage()); }
            exit();
        }
    }

    /**
     * Acción del botón Recalificar/Guardar Cambios
     */
    public function actualizarExamen() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $curso_id = $_POST['curso_id'];
            $dni_original = $_POST['dni_original']; 
            $nuevo_dni = trim($_POST['nuevo_dni']);
            $nuevo_tipo = strtoupper($_POST['nuevo_tipo']);
            $nuevas_respuestas = str_pad(strtoupper($_POST['nuevas_respuestas']), 30, " ");
            
            $db = new Database(); $dbh = $db->getDbh();
            $stmtClaves = $dbh->prepare("SELECT * FROM claves WHERE curso_id = ? AND tipo_examen = ?");
            $stmtClaves->execute([$curso_id, $nuevo_tipo]);
            $claves = [];
            while ($c = $stmtClaves->fetch(PDO::FETCH_ASSOC)) { $claves[$c['num_pregunta']] = ['resp' => $c['respuesta'], 'val' => $c['valor']]; }

            $nota = 0; $corr = 0; $inc = 0; $vac = 0;
            for ($i = 1; $i <= 30; $i++) {
                $marcada = $nuevas_respuestas[$i-1];
                if (trim($marcada) === '' || $marcada === ' ') { $vac++; }
                elseif (isset($claves[$i])) {
                    if ($marcada === $claves[$i]['resp']) { $nota += $claves[$i]['val']; $corr++; }
                    else { $inc++; }
                }
            }
            
            $stmtUpd = $dbh->prepare("UPDATE resultados SET 
                alumno_id = ?, tipo_examen = ?, puntaje_total = ?, 
                respuestas_correctas = ?, respuestas_incorrectas = ?, respuestas_vacias = ?,
                respuestas_alumno = ? 
                WHERE curso_id = ? AND alumno_id = ?");
            
            $stmtUpd->execute([$nuevo_dni, $nuevo_tipo, $nota, $corr, $inc, $vac, $nuevas_respuestas, $curso_id, $dni_original]);
            
            header("Location: /DatProcesador/procesador/auditoria/{$curso_id}?success=1");
            exit();
        }
    }

    public function resultados($curso_id) {
        $db = new Database(); $dbh = $db->getDbh();
        $sql = "SELECT r.*, a.nombre_completo FROM resultados r LEFT JOIN alumnos a ON r.alumno_id = a.dni WHERE r.curso_id = :id ORDER BY r.puntaje_total DESC";
        $stmt = $dbh->prepare($sql); $stmt->execute([':id' => $curso_id]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cursoModel = new CursoModel(); $curso = $cursoModel->obtenerPorId($curso_id);
        $this->render('procesador_resultados', ['resultados' => $resultados, 'curso' => $curso]);
    }

    public function auditoria($curso_id) {
        $db = new Database(); $dbh = $db->getDbh();
        $sql = "SELECT r.*, a.nombre_completo FROM resultados r LEFT JOIN alumnos a ON r.alumno_id = a.dni WHERE r.curso_id = :id";
        $stmt = $dbh->prepare($sql); $stmt->execute([':id' => $curso_id]);
        $auditoria = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cursoModel = new CursoModel(); $curso = $cursoModel->obtenerPorId($curso_id);
        $this->render('procesador_auditoria', ['auditoria' => $auditoria, 'curso' => $curso]);
    }
}