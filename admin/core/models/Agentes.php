<?php

final class Agentes extends Models implements OCREND {

    public function __construct() {
        parent::__construct();
    }

    # Método para los posibles errores que pueden ocurrir
    final private function Errors(array $data, bool $add = false){
        try {
            Helper::load('strings');
            # Si el parámetro add es verdadero entonces estamos en agregar nuevo usuario por lo tanto especificamos cuales campos son obligatorios
            if ($add) {
                if (Func::e($data['email'], $data['pass'], $data['name'], $data['phone'], $data['cel'], $data['direc'], $data['rif'], $data['gerente'], $data['cel_ger'], $data['phone_ger'], $data['email_ger'])) {
                    throw new Exception('<b>Error: </b>Los campos con * son obligatorios.');
                }
                $where = '';
            }else{
                # Si es falso add entonces especificamos los campos obligatorios menos la contraseña ya que está encriptada
                if (Func::e($data['email'], $data['name'], $data['phone'], $data['cel'], $data['direc'], $data['rif'], $data['gerente'], $data['cel_ger'], $data['phone_ger'], $data['email_ger'])) {
                    throw new Exception('<b>Error: </b>Los campos Email, Nombre y Teléfono no pueden estar vacíos.');
                }
                # Como estamos en edit debemos capturar el id que enviamos desde el formulario
                $id = (int) $data['id'];
                $where = "AND id <> '$id'";
            }
            # Realizamos una consulta para comprobar que el email no esté todavía en la base de datos
            $email = $this->db->scape($data['email']);
            if (is_array($this->db->select('email', 'users', "email = '$email' $where AND admin != '1' AND agencia != '1'"))) {
                throw new Exception('<b>Error:</b> El email utilizado ya existe.');

            }
            # Comprobamos que el email tenga un formato válido
            if (!Strings::is_email($data['email']) || !Strings::is_email($data['email_ger'])) {
                throw new Exception('<b>Error: </b>El email debe tener un formato válido.');
            }
            # Comprobamos que todos los teléfonos solo contengan números
            if (!is_numeric($data['phone']) || !is_numeric($data['cel']) || !is_numeric($data['cel_ger']) || !is_numeric($data['phone_ger'])) {
                throw new Exception('<b>Error: </b>Los campos telefónicos solo deben contener números.');
            }
            return false;
        } catch (Exception $e) {
            return Func::sendResponse(false, $e->getMessage());
        }
    }

    # Método para añadir una nueva agencia
    final public function Add(array $data) : array {
        /*
        * Si $error no es booleado entonces retornamos el error de la Excepción
        * Pasamos true como segundo parametro de Errors para indicarle que estamos en añadir
        */
        $error = $this->Errors($data, true);
        if (!is_bool($error)) {
            return $error;
        }
        /*
        * Creamos un array con los datos que vamos a introducir en la base de datos
        * Evaluamos que los campos que estén vacíos se le coloque 0 por defecto
        */
        $g = array(
            'email' => $data['email'],
            'pass' => Strings::hash($data['pass']),
            'name' => $data['name'],
            'phone' => $data['phone'],
            'agencia' => 1,
            'razon_social' => !Func::emp($data['razon']) ? $data['razon'] : 0,
            'RIF' => $data['rif'],
            'direccion' => $data['direc'],
            'celular_oficina' => $data['cel'],
            'RTN' => !Func::emp($data['rtn']) ? $data['rtn'] : 0,
            'VT' => !Func::emp($data['vt']) ? $data['vt'] : 0,
            'gerente' => $data['gerente'],
            'phone_ger' => $data['phone_ger'],
            'celular_ger' => $data['cel_ger'],
            'email_ger' => $data['email_ger']
        );
        # Insertamos lo datos
        $this->db->insert('users', $g);
        # Devolvemos un mensaje de éxito
        return Func::sendResponse(true, 'Agencia creada de forma exitosa');
    }

    # Método para editar una agencia
    final public function Edit(array $data) : array {
        /*
        * Si $error no es booleado entonces retornamos el error de la Excepción
        * No pasamos nada como segundo parámetro de errors ya que #add viene por defecto en false por lo tanto estamos en editar
        */
        $error = $this->Errors($data);
        if (!is_bool($error)) {
            return $error;
        }
        # Creamos un arreglo con las opciones a modificar
        $g = array(
            'email' => $data['email'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'activo' => $data['act'],
            'razon_social' => !Func::emp($data['razon']) ? $data['razon'] : 0,
            'RIF' => $data['rif'],
            'direccion' => $data['direc'],
            'celular_oficina' => $data['cel'],
            'RTN' => !Func::emp($data['rtn']) ? $data['rtn'] : 0,
            'VT' => !Func::emp($data['vt']) ? $data['vt'] : 0,
            'gerente' => $data['gerente'],
            'phone_ger' => $data['phone_ger'],
            'celular_ger' => $data['cel_ger'],
            'email_ger' => $data['email_ger']
        );
        # Si la contraseña no viene vacía entonces le damos el valor en la posicion ['pass']  del arreglo creado
        if (!Func::emp($data['pass'])) {
            $g['pass'] = Strings::hash($data['pass']);
        }
        # Capturamos el id que enviamos ya que en la API REST no existe $this->id
        $id = (int) $data['id'];
        # Actualizamos los campos en la base de datos
        $this->db->update('users', $g, "id='$id' AND agencia = '1'");
        # Devolvemos un mensaje de éxito
        return Func::sendResponse(true, 'Agencia Editada de forma exitosa');
    }

    public function __destruct() {
        parent::__destruct();
    }
}
?>