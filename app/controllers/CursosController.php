<?php

class CursosController extends DatosController {

    /**
     * Muestra el formulario para crear un nuevo curso
     */
    public function crear() {
        $this->render('curso_crear');
    }

    /**
     * ✅ NUEVO MÉTODO: Recibe los datos del formulario y los guarda
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre_curso'] ?? '');
            $preguntas = (int)($_POST['cantidad_preguntas'] ?? 0);

            if (!empty($nombre) && $preguntas > 0) {
                $model = new CursoModel();
                // Llamamos al método del modelo que ya definimos antes
                if ($model->insertarCursoManual($nombre, $preguntas)) {
                    header('Location: /DatProcesador/?status=curso_ok');
                    exit;
                }
            }
        }
        // Si algo falla, regresa al dashboard con error
        header('Location: /DatProcesador/?status=error');
        exit;
    }

    /**
     * Procesa la eliminación de un curso
     */
    public function eliminar($id) {
        $model = new CursoModel();
        if ($model->eliminarCurso($id)) {
            header("Location: /DatProcesador/?status=curso_deleted");
            exit();
        }
        header("Location: /DatProcesador/?status=error");
    }

    /**
     * Carga la vista para editar un curso
     */
    public function editar($id) {
        $model = new CursoModel();
        $curso = $model->obtenerPorId($id);
        if ($curso) {
            $this->render('curso_editar', ['curso' => $curso]);
        } else {
            header("Location: /DatProcesador/");
        }
    }

    /**
     * Procesa la actualización de los datos del curso
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $nombre = trim($_POST['nombre_curso']);
            $preguntas = (int)$_POST['cantidad_preguntas'];

            $model = new CursoModel();
            if ($model->actualizarCurso($id, $nombre, $preguntas)) {
                header("Location: /DatProcesador/?status=curso_updated");
                exit();
            }
        }
        header("Location: /DatProcesador/?status=error");
    }
}