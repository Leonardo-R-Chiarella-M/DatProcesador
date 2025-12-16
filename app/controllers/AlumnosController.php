<?php

// AlumnosController hereda de DatosController (o el nombre de tu clase base de controladores)
class AlumnosController extends DatosController {

    /**
     * Método para manejar la subida, validación e inserción del archivo de alumnos.
     * Ruta esperada: /alumnos/procesar
     */
    public function procesar() {
        
        // 1. Verificar si se subió un archivo
        if (!isset($_FILES['datFile']) || $_FILES['datFile']['error'] != UPLOAD_ERR_OK) {
            $this::showError("Error de Subida", "No se ha subido ningún archivo o el archivo es demasiado grande/inválido.");
            return;
        }

        $archivoTemp = $_FILES['datFile']['tmp_name'];
        $model = new AlumnoModel(); 
        $lineaActual = 1;
        $totalValidos = 0;
        
        // 2. Leer y validar el archivo línea por línea
        if (($handle = fopen($archivoTemp, "r")) !== FALSE) {
            while (($data = fgets($handle)) !== FALSE) {
                
                $campos = explode(';', $data); 
                $campos = array_map('trim', $campos);

                if (!empty(implode('', $campos))) {
                    if ($model->validarLinea($campos, $lineaActual)) {
                        $totalValidos++;
                    }
                    $lineaActual++;
                }
            }
            fclose($handle);
        } else {
             $this::showError("Error de lectura", "No se pudo abrir el archivo para la lectura.");
             return;
        }

        // 3. Inserción en Base de Datos (Inserta todos los válidos)
        $totalRegistrosInsertados = 0;
        $datosValidados = $model->getDataValida();

        if (!empty($datosValidados)) {
            try {
                $resultados_db = $model->insertarDatosValidados();
                $totalRegistrosInsertados = $resultados_db['insertados'];
                
            } catch (Exception $e) {
                $this::showError("Error de Base de Datos", "Fallo al ejecutar la inserción: " . $e->getMessage());
                return;
            }
        }
        
        // 4. Consulta de Duplicados en toda la tabla (GRUPO POR DNI)
        $sql_duplicados = "
            SELECT dni, COUNT(dni) as total_repeticiones 
            FROM alumnos 
            GROUP BY dni 
            HAVING COUNT(dni) > 1
        ";
        
        $duplicados_en_db = $this->query($sql_duplicados); 
        
        // 5. Preparar los datos para la vista
        $errores = $model->getErrores();
        $totalLineas = $lineaActual - 1;
        
        $data = [
            'total_procesado' => $totalLineas,
            'total_validos' => $totalValidos,
            'total_errores' => count($errores),
            'registros_insertados' => $totalRegistrosInsertados, 
            
            'total_duplicados_tabla' => count($duplicados_en_db),
            'duplicados_para_corregir' => $duplicados_en_db,         
            
            'errores_detalle' => $errores,
            'base_url' => $this->getBaseUrl()
        ];

        $this->render('resultado_alumnos', $data);
    }
    
    /**
     * Muestra todos los registros de la tabla 'alumnos', separando duplicados.
     * Ruta esperada: /alumnos/verTabla
     */
    public function verTabla() {
        
        // Consulta SQL para identificar DNI que se repiten (COUNT > 1) y clasificar.
        $sql = "
            SELECT 
                a.id, 
                a.dni, 
                a.nombre_completo, 
                a.descripcion, 
                a.fecha_carga,
                CASE WHEN T2.total_repeticiones > 1 THEN 1 ELSE 0 END as es_duplicado
            FROM 
                alumnos a
            INNER JOIN (
                SELECT dni, COUNT(dni) as total_repeticiones 
                FROM alumnos 
                GROUP BY dni
            ) T2 ON a.dni = T2.dni
            ORDER BY a.dni, a.nombre_completo ASC
        ";
        
        $alumnos_raw = $this->query($sql);
        
        $duplicados = [];
        $unicos = [];
        
        // Clasificar los resultados en dos arrays: duplicados y únicos
        foreach ($alumnos_raw as $alumno) {
            $item = (array) $alumno;
            
            if ($item['es_duplicado'] == 1) {
                $duplicados[] = $item;
            } else {
                $unicos[] = $item;
            }
        }

        // Preparar los datos para la vista
        $data = [
            'alumnos_duplicados' => $duplicados,
            'alumnos_unicos' => $unicos,
            'total_duplicados' => count($duplicados),
            'total_unicos' => count($unicos),
            'total_alumnos' => count($alumnos_raw),
            'base_url' => $this->getBaseUrl()
        ];
        
        $this->render('alumnos_listado', $data);
    }

    /**
     * Muestra el formulario de edición para un alumno específico.
     * Ruta esperada: /alumnos/editar/{id}
     */
    public function editar(int $id) {
        $model = new AlumnoModel();
        
        try {
            $alumno = $model->getAlumnoById($id);
            
            if (!$alumno) {
                $this::showError("Error 404", "El alumno con ID {$id} no fue encontrado.");
                return;
            }

            $data = [
                'alumno' => $alumno,
                'base_url' => $this->getBaseUrl()
            ];

            $this->render('alumno_editar', $data);

        } catch (Exception $e) {
            $this::showError("Error de Base de Datos", "No se pudo cargar el alumno: " . $e->getMessage());
        }
    }

    /**
     * Procesa el formulario de edición y actualiza los datos.
     * Ruta esperada: /alumnos/actualizar (POST)
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->getBaseUrl() . 'alumnos/verTabla');
            exit;
        }

        $model = new AlumnoModel();
        
        $datos = [
            'id' => filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT),
            'dni' => filter_input(INPUT_POST, 'dni', FILTER_SANITIZE_STRING),
            'nombre_completo' => filter_input(INPUT_POST, 'nombre_completo', FILTER_SANITIZE_STRING),
            'descripcion' => filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING)
        ];
        
        // Validación básica
        if (empty($datos['id']) || empty($datos['dni']) || empty($datos['nombre_completo'])) {
            $this::showError("Error de Validación", "Todos los campos obligatorios deben ser llenados.");
            return;
        }

        try {
            $actualizado = $model->guardarAlumno($datos);
            
            if ($actualizado) {
                header('Location: ' . $this->getBaseUrl() . 'alumnos/verTabla?status=success_edit');
            } else {
                header('Location: ' . $this->getBaseUrl() . 'alumnos/verTabla?status=no_change');
            }
            exit;

        } catch (Exception $e) {
            $this::showError("Error de Actualización", "Fallo al guardar los datos: " . $e->getMessage());
        }
    }
    
    /**
     * Elimina todos los datos de la tabla alumnos (TRUNCATE).
     */
    public function eliminarDatos() {
        $model = new AlumnoModel();
        
        try {
            $model->eliminarTodosLosAlumnos();
            
            header('Location: ' . $this->getBaseUrl() . 'alumnos/verTabla?status=success_delete');
            exit;
            
        } catch (Exception $e) {
            $this::showError("Error de Eliminación", "No se pudo vaciar la tabla de alumnos. Detalle: " . $e->getMessage());
        }
    }

    /**
     * Método auxiliar para calcular la URL base.
     */
    private function getBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . $host . '/DatProcesador/'; 
    }
}