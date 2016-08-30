<?php

final class Config extends Models implements OCREND {

    public function __construct() {
        parent::__construct();
    }

    # Método para captar errores
    final private function errors(array $data){
        try{
            Helper::load('strings');
            if (Func::e($data['phone'], $data['email'], $data['direccion'])) {
                throw new Exception("<b>Error:</b> Los campos Teléfono, Email y Dirección son obligatorios.");
            }
            if (!Strings::is_email($data['email'])) {
                throw new Exception("<b>Error:</b> Debe introducir un formato de Email válido.");
            }

            return false;

        }catch(Exception $e){
            return Func::sendResponse(false, $e->getMessage());
        }
    }

    # Método para agregar la configuración a la base de datos
    final public function add_config(array $data) : array {
        $error = $this->errors($data);
        if (false !== $error) {
            return $error;
        }
        # Datos a insertar en la db
        $datos = array(
            'telefono' => $data['phone'],
            'email_contacto' => $data['email'],
            'facebook' => $data['facebook'],
            'twitter' => $data['twitter'],
            'google_plus' => $data['google_plus'],
            'linkedin' => $data['linkedin'],
            'vimeo' => $data['vimeo'],
            'dribble' => $data['dribble'],
            'flickr' => $data['flickr'],
            'direccion' => $data['direccion'],
            'longitud' => $data['longitud'],
            'latitud' => $data['latitud']
        );

        /*
         * La inserción de nuevos datos era un proceso que solo se llevaría a cabo una única vez
         * Por tanto, existiendo ya los datos allí, (no se borrarán), siempre habrá un único dato de id=1 que se debe actualizar
         */
        $this->db->update('config', $datos, 'id=1', 'LIMIT 1');
        return Func::sendResponse(true, "Datos configurados de forma exitosa");
    }

    # Método para obtener los datos
    # El cual SIEMPRE me dará información (así sea vacía) por tanto devuelve un array
    final public function get_config() : array {
        return $this->db->select('*','config',"id='1'",'LIMIT 1');
    }

    public function __destruct() {
        parent::__destruct();
    }
}

?>