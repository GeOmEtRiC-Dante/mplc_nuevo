<?php

final class Slider extends Models implements OCREND {

    # Ruta en donde se guardan
    private $route = '../views/app/images/sliders/';

    public function __construct() {
        parent::__construct();
    }

    # Control de errores
    final private function errors(array $data) {
        try {

            if($_FILES['imagen']['name'] == '') {
                throw new Exception('<b>Error:</b> Debe subir una imagen.');
            }

            Helper::load('files');
            if(!Files::is_image($_FILES['imagen']['name'])) {
                throw new Exception('<b>Error:</b> El formato del archivo debe ser de imagen (JPG,PNG,JPEG,GIF)');
            }

            list($ancho , $alto) = getimagesize ($_FILES['imagen']['tmp_name']) ;

            if($ancho < 1600 or $alto > 300) {
                throw new Exception('<b>Error:</b> La imagen debe tener 1600px de ancho como mínimo y 300px de alto como máximo.');
            }

            if(round ($_FILES[ 'imagen' ][ 'size' ] / 1024 , 1) > 2000) {
                throw new Exception('<b>Error:</b> El peso de la imagen no debe exceder los 2Mb.');
            }

            return false;
        } catch(Exception $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    # Obtiene todas las imagenes
    final public function get_images() : array {
        Helper::load('files');

        return Files::get_files_in_dir($this->route);
    }

    # Elimina una imagen
    final public function delete($name) {
        if(null !== $name and file_exists($this->route . $name)) {
            unlink($this->route . $name);
        }
        Func::redir(URL . 'slider/?success=true');
    }

    # Carga una nueva imagen (API REST)
    final public function upload(array $data) : array {

        # Revisamos errores
        $error = $this->errors($data);
        if(!is_bool($error)) {
            return $error;
        }

        # No hay errores entonces se carga la imagen
        move_uploaded_file($_FILES['imagen']['tmp_name'], '../' . $this->route . $_POST['nombre_imagen'] . '.' . Files::get_file_ext($_FILES['imagen']['name']));

        return array('success' => 1, 'message' => '<b>Realizado: </b> Imágen cargada con éxito.');
    }

    public function __destruct() {
        parent::__destruct();
    }

}

?>