<?php

/**
 * Controlador para la gestión de claves de respuestas.
 * Permite configurar respuestas y valores por tipo de examen.
 */
class ClavesController extends DatosController {

    /**
     * Muestra la interfaz de configuración de claves.
     */
    public function configurar($id, $tipo = 'A') {
        $cursoModel = new CursoModel();
        $claveModel = new ClaveModel();
        
        $curso = $cursoModel->obtenerPorId($id);
        if (!$curso) {
            header("Location: /DatProcesador/");
            exit();
        }

        // Obtener claves desde el modelo filtradas por tipo
        $clavesExistentes = $claveModel->obtenerClavesPorTipo($id, $tipo);

        $datos_claves = [];
        foreach ($clavesExistentes as $c) {
            $datos_claves[$c['num_pregunta']] = [
                'letra' => $c['respuesta'],
                'valor' => $c['valor']
            ];
        }

        $this->render('claves_configurar', [
            'curso'        => $curso,
            'datos_claves' => $datos_claves,
            'tipo_actual'  => $tipo
        ]);
    }

    /**
     * Guarda los datos ingresados manualmente en el grid.
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $curso_id    = $_POST['curso_id'];
            $tipo_examen = $_POST['tipo_examen'];
            $letras      = $_POST['letras']; 
            $valores     = $_POST['valores'];

            $claveModel = new ClaveModel();
            $datosParaGuardar = [];
            
            foreach ($letras as $num => $letra) {
                if (!empty($letra)) {
                    $datosParaGuardar[] = [
                        'num'   => $num,
                        'letra' => $letra,
                        'valor' => (!empty($valores[$num])) ? $valores[$num] : 1.00
                    ];
                }
            }

            if ($claveModel->guardarClavesMasivo($curso_id, $tipo_examen, $datosParaGuardar)) {
                header("Location: /DatProcesador/claves/configurar/{$curso_id}/{$tipo_examen}?status=claves_ok");
            } else {
                header("Location: /DatProcesador/claves/configurar/{$curso_id}/{$tipo_examen}?status=error");
            }
            exit();
        }
    }

    /**
     * Importación masiva desde CSV: Respuesta;Valor (Desde fila 1).
     */
    public function importarMasivo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_claves'])) {
            $curso_id = $_POST['curso_id'];
            $tipo_actual = $_POST['tipo_examen'];
            $file = $_FILES['archivo_claves']['tmp_name'];

            if (($handle = fopen($file, "r")) !== FALSE) {
                $claveModel = new ClaveModel();
                $separador = ";"; 

                $datosParaGuardar = [];
                $contadorPregunta = 1;

                while (($data = fgetcsv($handle, 1000, $separador)) !== FALSE) {
                    // Limpieza de caracteres invisibles al inicio del archivo (BOM)
                    $resRaw = trim($data[0]);
                    $resRaw = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $resRaw); 
                    
                    if ($resRaw !== '') { 
                        $datosParaGuardar[] = [
                            'num'   => $contadorPregunta,
                            'letra' => strtoupper(substr($resRaw, 0, 1)), 
                            'valor' => isset($data[1]) ? str_replace(',', '.', trim($data[1])) : 1.00
                        ];
                        $contadorPregunta++;
                    }
                }
                fclose($handle);

                if ($claveModel->guardarClavesMasivo($curso_id, $tipo_actual, $datosParaGuardar)) {
                    header("Location: /DatProcesador/claves/configurar/{$curso_id}/{$tipo_actual}?status=import_ok");
                } else {
                    header("Location: /DatProcesador/claves/configurar/{$curso_id}/{$tipo_actual}?status=error_import");
                }
                exit();
            }
        }
    }
}