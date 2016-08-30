<?php

final class Usuarios extends Models implements OCREND {

    public function __construct() {
        parent::__construct();
    }

    # Método para los posibles errores que pueden ocurrir
    final private function Errors(array $data, bool $add = false){
        try {
            Helper::load('strings');
            # Si el parámetro add es verdadero entonces estamos en agregar nuevo usuario por lo tanto especificamos cuales campos son obligatorios
            if ($add) {
                if (Func::e($data['email'], $data['pass'], $data['name'], $data['phone'])) {
                    throw new Exception('<b>Error: </b>Los campos con * son obligatorios.');
                }
                $where = '';
            }else{
                # Si es falso add entonces especificamos los campos obligatorios menos la contraseña ya que está encriptada
                if (Func::e($data['email'], $data['name'], $data['phone'])) {
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
            # Para mas seguridad evaluamos que los campos que no pertecen al freelancer no puedan ser enviados
            if ($data['rango'] !== '1' and !Func::e($data['cel'], $data['razon'], $data['direc'], $data['rif'], $data['rtn'], $data['vt'])) {
                throw new Exception('<b>Error:</b> Los campos que intenta llenar solo pertenecen al rango de freelancer.');
            }
            # Comprobamos que el email tenga un formato válido
            if (!Strings::is_email($data['email'])) {
                throw new Exception('<b>Error: </b>El email debe tener un formato válido.');
            }
            # Comprobamos que los ampos telefonos solo sean números y que el campo celular no esté vacio en caso de ser freelancer
            if (!is_numeric($data['phone']) || (!is_numeric($data['cel']) and !Func::emp($data['cel']))) {
                throw new Exception('<b>Error: </b>El campo de teléfono solo debe contener números.');
            }
            return false;
        } catch (Exception $e) {
            return Func::sendResponse(false, $e->getMessage());
        }
    }

    # Método para añadir un nuevo usuario
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
        $u = array(
            'email' => $data['email'],
            'pass' => Strings::hash($data['pass']),
            'name' => $data['name'],
            'phone' => $data['phone'],
            'rango' => $data['rango'],
            'razon_social' => !Func::emp($data['razon']) ? $data['razon'] : 0,
            'RIF' => !Func::emp($data['rif']) ? $data['rif'] : 0,
            'direccion' => !Func::emp($data['direc']) ? $data['direc'] : 0,
            'celular_oficina' => !Func::emp($data['cel']) ? $data['cel'] : 0,
            'RTN' => !Func::emp($data['rtn']) ? $data['rtn'] : 0,
            'VT' => !Func::emp($data['vt']) ? $data['vt'] : 0
        );
        # Insertamos lo datos
        $this->db->insert('users', $u);
        # Devolvemos un mensaje de éxito
        return Func::sendResponse(true, 'Usuario creado de forma exitosa');
    }

    # Método para editar un usuario
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
        $u = array(
            'email' => $data['email'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'activo' => $data['act'],
            'razon_social' => !Func::emp($data['razon']) ? $data['razon'] : 0,
            'RIF' => !Func::emp($data['rif']) ? $data['rif'] : 0,
            'direccion' => !Func::emp($data['direc']) ? $data['direc'] : 0,
            'celular_oficina' => !Func::emp($data['cel']) ? $data['cel'] : 0,
            'RTN' => !Func::emp($data['rtn']) ? $data['rtn'] : 0,
            'VT' => !Func::emp($data['vt']) ? $data['vt'] : 0
        );
        # Si la contraseña no viene vacía entonces le damos el valor en la posicion ['pass']  del arreglo creado
        if (!Func::emp($data['pass'])) {
            $u['pass'] = Strings::hash($data['pass']);
        }
        # Capturamos el id que enviamos ya que en la API REST no existe $this->id
        $id = (int) $data['id'];
        # Actualizamos los campos en la base de datos
        $this->db->update('users', $u, "id='$id' AND admin != '1' AND agencia != '1'");
        # Devolvemos un mensaje de éxito
        return Func::sendResponse(true, 'Usuario editado de forma exitosa');
    }

    public function __destruct() {
        parent::__destruct();
    }
}

?>