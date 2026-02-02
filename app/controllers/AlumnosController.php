<?php

class AlumnosController extends DatosController {

    /**
     * Muestra la tabla de alumnos con clasificación de duplicados
     */
    public function verTabla() {
        $model = new AlumnoModel();
        $alumnos = $model->listarTodos(); 

        $alumnos_duplicados = [];
        $alumnos_unicos = [];

        $dnis = array_column($alumnos, 'dni');
        $conteo_dnis = array_count_values($dnis);

        foreach ($alumnos as $alumno) {
            if ($conteo_dnis[$alumno['dni']] > 1) {
                $alumnos_duplicados[] = $alumno;
            } else {
                $alumnos_unicos[] = $alumno;
            }
        }

        $this->render('alumnos_listado', [
            'alumnos_duplicados' => $alumnos_duplicados,
            'alumnos_unicos'     => $alumnos_unicos,
            'total_duplicados'   => count($alumnos_duplicados),
            'total_unicos'       => count($alumnos_unicos),
            'base_url'           => '/DatProcesador/'
        ]);
    }

    /**
     * Procesa la carga de archivos CSV con 4 columnas separadas por ';'
     * Formato esperado: DNI;NOMBRE;FASE;DESCRIPCION
     */
    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['datFile'])) {
            $handle = fopen($_FILES['datFile']['tmp_name'], "r");
            $model = new AlumnoModel();
            $count = 0;

            while (($line = fgets($handle)) !== false) {
                $lineaLimpia = trim($line);
                if (empty($lineaLimpia)) continue;

                // Separador por punto y coma (;)
                $datos = explode(';', $lineaLimpia);
                
                if (count($datos) >= 2) {
                    $dni    = trim($datos[0]); 
                    $nombre = trim($datos[1]); 
                    
                    // Captura Fase y Descripción de las posiciones 2 y 3
                    $fase   = isset($datos[2]) ? trim($datos[2]) : ''; 
                    $desc   = isset($datos[3]) ? trim($datos[3]) : ''; 
                    
                    if ($model->insertar($dni, $nombre, $fase, $desc)) {
                        $count++;
                    }
                }
            }
            fclose($handle);
            
            header("Location: /DatProcesador/alumnos/verTabla?status=success&count=$count");
            exit();
        }
        header("Location: /DatProcesador/?status=error");
    }

    public function editar($id) {
        $model = new AlumnoModel();
        $alumno = $model->obtenerPorId($id);

        if (!$alumno) {
            header("Location: /DatProcesador/alumnos/verTabla");
            exit();
        }

        $this->render('alumno_editar', [
            'alumno'   => $alumno,
            'base_url' => '/DatProcesador/'
        ]);
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = new AlumnoModel();
            
            // Enviamos 5 parámetros al modelo incluyendo Fase
            $exito = $model->actualizar(
                $_POST['id'], 
                $_POST['dni'], 
                $_POST['nombre_completo'], 
                $_POST['fase'], 
                $_POST['descripcion']
            );

            if ($exito) {
                header("Location: /DatProcesador/alumnos/verTabla?status=updated");
                exit();
            }
        }
        header("Location: /DatProcesador/alumnos/verTabla?status=error");
    }

    public function eliminar($id) {
        $model = new AlumnoModel();
        if ($model->eliminarPorId($id)) {
            header("Location: /DatProcesador/alumnos/verTabla?status=deleted");
            exit();
        }
    }

    public function reiniciarTabla() {
        $model = new AlumnoModel();
        if ($model->truncarAlumnos()) {
            header("Location: /DatProcesador/alumnos/verTabla?status=reset_ok");
            exit();
        }
    }
}