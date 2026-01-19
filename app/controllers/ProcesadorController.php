<?php

class ProcesadorController extends DatosController {

    /**
     * Vista para subir el archivo .DAT
     */
    public function subir($curso_id) {
        $cursoModel = new CursoModel();
        $curso = $cursoModel->obtenerPorId($curso_id);
        if (!$curso) { 
            header("Location: /DatProcesador/"); 
            exit(); 
        }
        $this->render('procesador_subir', ['curso' => $curso]);
    }

    /**
     * Procesa la carga inicial del archivo de la lectora óptica
     */
    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_dat'])) {
            $curso_id = $_POST['curso_id'];
            $file = $_FILES['archivo_dat']['tmp_name'];
            $db = new Database(); 
            $dbh = $db->getDbh();

            // 1. Cargar claves maestras y determinar el número total de preguntas
            $stmt = $dbh->prepare("SELECT * FROM claves WHERE curso_id = :id");
            $stmt->execute([':id' => $curso_id]);
            $clavesMaster = [];
            $maxPreguntasCurso = 0;
            while ($c = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $clavesMaster[$c['tipo_examen']][$c['num_pregunta']] = [
                    'resp' => $c['respuesta'], 
                    'val'  => $c['valor']
                ];
                if ($c['num_pregunta'] > $maxPreguntasCurso) $maxPreguntasCurso = $c['num_pregunta'];
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
                        
                        // Extraemos la cadena completa (usamos 100 por seguridad o el máximo del curso)
                        $respuestas_alumno = substr($lineaRaw, $posPrefijo + 15, $maxPreguntasCurso);

                        if ($caracterTipo === ' ' || empty(trim($caracterTipo)) || !preg_match('/[A-F]/', $caracterTipo)) {
                            $tipo_examen = "S/T";
                        } else {
                            $tipo_examen = strtoupper($caracterTipo);
                        }
                    } else { continue; }

                    $nota = 0; $correctas = 0; $incorrectas = 0; $vacias = 0;
                    $clavesConfig = $clavesMaster[$tipo_examen] ?? [];
                    
                    for ($i = 1; $i <= $maxPreguntasCurso; $i++) {
                        $marcada = $respuestas_alumno[$i-1] ?? ' ';
                        if (trim($marcada) === '' || $marcada === ' ') { 
                            $vacias++; 
                        } elseif (isset($clavesConfig[$i]) && $marcada === $clavesConfig[$i]['resp']) {
                            $nota += $clavesConfig[$i]['val']; 
                            $correctas++;
                        } else { 
                            $incorrectas++; 
                        }
                    }

                    $stmtIns = $dbh->prepare("INSERT INTO resultados 
                        (curso_id, alumno_id, tipo_examen, puntaje_total, respuestas_correctas, respuestas_incorrectas, respuestas_vacias, respuestas_alumno, linea_original) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmtIns->execute([$curso_id, $dni, $tipo_examen, $nota, $correctas, $incorrectas, $vacias, $respuestas_alumno, $lineaRaw]);
                }
                $dbh->commit();
                header("Location: /DatProcesador/procesador/resultados/{$curso_id}");
            } catch (Exception $e) { 
                $dbh->rollBack(); 
                die("Error: " . $e->getMessage()); 
            }
            exit();
        }
    }

    /**
     * Actualización manual desde Auditoría
     */
    public function actualizarExamen() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $curso_id = $_POST['curso_id'];
            $dni_original = $_POST['dni_original']; 
            $nuevo_dni = trim($_POST['nuevo_dni']);
            $nuevo_tipo = strtoupper($_POST['nuevo_tipo']);
            $nuevas_respuestas = strtoupper($_POST['nuevas_respuestas']);
            
            $db = new Database(); $dbh = $db->getDbh();

            // Determinar total de preguntas para recalificar
            $stmtCant = $dbh->prepare("SELECT MAX(num_pregunta) as total FROM claves WHERE curso_id = ?");
            $stmtCant->execute([$curso_id]);
            $numPreguntasTotal = $stmtCant->fetch(PDO::FETCH_ASSOC)['total'] ?? 30;

            $stmtClaves = $dbh->prepare("SELECT * FROM claves WHERE curso_id = ? AND tipo_examen = ?");
            $stmtClaves->execute([$curso_id, $nuevo_tipo]);
            $claves = [];
            while ($c = $stmtClaves->fetch(PDO::FETCH_ASSOC)) { 
                $claves[$c['num_pregunta']] = ['resp' => $c['respuesta'], 'val' => $c['valor']]; 
            }

            $nota = 0; $corr = 0; $inc = 0; $vac = 0;
            for ($i = 1; $i <= $numPreguntasTotal; $i++) {
                $marcada = $nuevas_respuestas[$i-1] ?? ' ';
                if (trim($marcada) === '' || $marcada === ' ') { 
                    $vac++; 
                } elseif (isset($claves[$i])) {
                    if ($marcada === $claves[$i]['resp']) { 
                        $nota += $claves[$i]['val']; $corr++; 
                    } else { $inc++; }
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

    /**
     * Exportación Individual con columnas dinámicas (soporta 40 preguntas)
     */
    public function exportarExcel($curso_id) {
        $db = new Database(); $dbh = $db->getDbh();
        $cursoModel = new CursoModel();
        $curso = $cursoModel->obtenerPorId($curso_id);
        
        $stmtCant = $dbh->prepare("SELECT MAX(num_pregunta) as total FROM claves WHERE curso_id = ?");
        $stmtCant->execute([$curso_id]);
        $numPreguntasTotal = $stmtCant->fetch(PDO::FETCH_ASSOC)['total'] ?? 30;

        $sql = "SELECT r.*, a.nombre_completo, a.descripcion FROM resultados r 
                LEFT JOIN alumnos a ON r.alumno_id = a.dni 
                WHERE r.curso_id = :id ORDER BY r.puntaje_total DESC";
        $stmt = $dbh->prepare($sql); $stmt->execute([':id' => $curso_id]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmtClaves = $dbh->prepare("SELECT * FROM claves WHERE curso_id = ?");
        $stmtClaves->execute([$curso_id]);
        $clavesMaster = [];
        while ($c = $stmtClaves->fetch(PDO::FETCH_ASSOC)) {
            $clavesMaster[$c['tipo_examen']][$c['num_pregunta']] = $c['respuesta'];
        }

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="Reporte_'.$curso['nombre_curso'].'.xls"');

        $this->generarEstructuraExcel($resultados, $clavesMaster, "CURSO: " . $curso['nombre_curso'], false, $numPreguntasTotal);
        exit();
    }

    /**
     * Exportación General Dinámica
     */
    public function exportarTodoExcel() {
        $db = new Database(); $dbh = $db->getDbh();
        $stmtMax = $dbh->query("SELECT MAX(num_pregunta) as max_p FROM claves");
        $maxPreguntas = $stmtMax->fetch(PDO::FETCH_ASSOC)['max_p'] ?? 30;

        $sql = "SELECT r.*, a.nombre_completo, a.descripcion, c.nombre_curso FROM resultados r 
                LEFT JOIN alumnos a ON r.alumno_id = a.dni 
                LEFT JOIN cursos c ON r.curso_id = c.id ORDER BY c.nombre_curso ASC, r.puntaje_total DESC";
        $resultados = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $clavesMaster = [];
        $resClaves = $dbh->query("SELECT * FROM claves")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($resClaves as $c) { $clavesMaster[$c['curso_id']][$c['tipo_examen']][$c['num_pregunta']] = $c['respuesta']; }

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="Reporte_General.xls"');

        $this->generarEstructuraExcel($resultados, $clavesMaster, "REPORTE GENERAL DE TODOS LOS CURSOS", true, $maxPreguntas);
        exit();
    }

    private function generarEstructuraExcel($resultados, $claves, $titulo, $esGeneral, $numP) {
        $colspan = $numP + ($esGeneral ? 6 : 5);
        echo '<table border="1">';
        echo '<tr><th colspan="'.$colspan.'" style="background:#1a202c; color:white;">'.$titulo.'</th></tr>';
        echo '<tr style="background:#edf2f7;">';
        if($esGeneral) echo '<th>CURSO</th>';
        echo '<th>DNI</th><th>DESCRIPCION</th><th>NOMBRE COMPLETO</th><th>TIPO</th><th>NOTA</th>';
        for ($i = 1; $i <= $numP; $i++) { echo '<th>P'.$i.'</th>'; }
        echo '</tr>';

        foreach ($resultados as $r) {
            $tipo = $r['tipo_examen']; $c_id = $r['curso_id'];
            $resps = str_pad($r['respuestas_alumno'], $numP, " ");
            echo '<tr>';
            if($esGeneral) echo '<td rowspan="2">'.$r['nombre_curso'].'</td>';
            echo '<td rowspan="2">'.$r['alumno_id'].'</td><td rowspan="2">'.($r['descripcion'] ?? '---').'</td><td rowspan="2">'.($r['nombre_completo'] ?? 'N/V').'</td><td rowspan="2">'.$tipo.'</td><td rowspan="2">'.number_format($r['puntaje_total'], 4).'</td>';
            for ($i = 0; $i < $numP; $i++) { echo '<td align="center" style="background:#fff9db;">'.$resps[$i].'</td>'; }
            echo '</tr><tr>';
            for ($i = 1; $i <= $numP; $i++) {
                $marcada = $resps[$i-1];
                $correcta = $esGeneral ? ($claves[$c_id][$tipo][$i] ?? null) : ($claves[$tipo][$i] ?? null);
                $simbolo = (trim($marcada) === '') ? '-' : (($marcada === $correcta) ? 'V' : 'F');
                $color = ($simbolo === 'V') ? 'color:#2f855a;' : (($simbolo === 'F') ? 'color:#c53030;' : '');
                echo '<td align="center" style="font-weight:bold; '.$color.'">'.$simbolo.'</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }

    public function resultados($curso_id) {
        $db = new Database(); $dbh = $db->getDbh();
        $sql = "SELECT r.*, a.nombre_completo, a.descripcion FROM resultados r LEFT JOIN alumnos a ON r.alumno_id = a.dni WHERE r.curso_id = :id ORDER BY r.puntaje_total DESC";
        $stmt = $dbh->prepare($sql); $stmt->execute([':id' => $curso_id]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cursoModel = new CursoModel(); $curso = $cursoModel->obtenerPorId($curso_id);
        $this->render('procesador_resultados', ['resultados' => $resultados, 'curso' => $curso]);
    }

    public function auditoria($curso_id) {
        $db = new Database(); $dbh = $db->getDbh();
        $sql = "SELECT r.*, a.nombre_completo, a.descripcion FROM resultados r LEFT JOIN alumnos a ON r.alumno_id = a.dni WHERE r.curso_id = :id";
        $stmt = $dbh->prepare($sql); $stmt->execute([':id' => $curso_id]);
        $auditoria = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cursoModel = new CursoModel(); $curso = $cursoModel->obtenerPorId($curso_id);
        $this->render('procesador_auditoria', ['auditoria' => $auditoria, 'curso' => $curso]);
    }
}