<?php
final class Suscriptores extends Models implements OCREND {

    # La colocamos como propiedad para que se pueda reservar en memoria
    private $listmail;

    public function __construct() {
        parent::__construct();
    }

    /* Método para verificar campos vacíos */
    final private function Errors(array $data){
        try{
            Helper::load('strings');

            # Confirmamos que esté vacío el campo
            /**
             * Con Func::e() podemos pasar infinitos parámetros en vez de usar Func::emp() para cada uno
             * Func::e() devuelve TRUE si ALMENOS uno está vacío
             */
            if (Func::e($data['asunto'],$data['contenido'])) {
                throw new Exception('<b>Error: </b> Ningún campo puede estar vacío.');
            }

            /**
             * Simplemente hay que sacarlos
             *
             */
            $suscriptores = $this->get_suscriptores(true);
            if(false !== $suscriptores) {
                foreach($suscriptores as $sus) {
                    $this->listmail[$sus['email']] = 'Suscriptor ' . $sus['id'];
                }
            } else {
                throw new Exception('<b>Error:</b> No hay nadie en la lista de suscriptores.');
            }


            return false;
        }catch(Exception $e){
            return Func::sendResponse(false, $e->getMessage());
        }
    }
    /* Función para envío de correos */
    final public function Send(array $data) : array {
        Helper::load('emails');

        # Ahora cuando llamamos a Errors, $this->listmail se genera como un arreglo
        $error = $this->Errors($data);
        # Si no da falso entonces retornamos el error
        if (false !== $error) {
            # Si fue distinto de false, es porque devolvió un arreglo, por tanto eso devolvemos
            return $error;
            # Cómo se hizo un return, pues se corta la ejecución del código y hasta aquí llega
        }

        $email = Emails::send_mail($this->listmail,Emails::plantilla($data['contenido']),$data['asunto']);

        if(true === $email) {
            # El correo se envió
            $success = 1;
            $message = '<b>Correcto:</b> Los emails se han enviado';

        } else {
            # No se envió, entonces $email contiene un string con información del motivo por el cual no se envió
            $success = 0;
            $message = DEBUG ? $email : '<b>Error</b> No se pudo enviar correo';
            # Cuando el DEBUG esté activo mostrará error de phpmailer, cuando no esté activo mostrará el mensaje

        }

        return array('success' => $success, 'message' => $message);
    }

    /* Función para eliminar al suscriptor según su id */
    final public function Del() {

        $this->db->delete('subscribers',"id='$this->id'");

        Func::redir(URL . 'suscriptores/'); # Faltaba redireccionar
    }

    /*Función que devuelve los suscriptores de la base de datos */
    final public function get_suscriptores(bool $multiple) {
        if($multiple) {
            return $this->db->select('*','subscribers');
        }
        $result = $this->db->select('*','subscribers',"id='$this->id'");
        if(false === $result) {
            Func::redir(URL . 'suscriptores/');
        }
        return $result;
    }
    public function __destruct() {
        parent::__destruct();
    }
}
?>