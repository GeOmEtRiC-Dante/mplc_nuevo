<?php

final class Admins extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }

  # Control de errores para add y edit
  final private function errors(array $data, bool $add = false) {
    try {

      if($add) {
        if(!Func::all_full($data)) {
          throw new Exception('<b>Error:</b> Todos los campos deben estar llenos.');
        }
        $where = '';
      } else {
        if(Func::emp($data['email'])) {
          throw new Exception('<b>Error:</b> El email debe estar lleno.');
        }
        $id = (int) $data['id'];
        $where = "AND id <> '$id'";
      }

      $email = $this->db->scape($data['email']);
      if(is_array($this->db->select('id','users',"email='$email' $where"))) {
        throw new Exception('<b>Error:</b> El email utilizado ya existe.');
      }

      return false;
    } catch(Exception $e) {
      return array('success' => 0, 'message' => $e->getMessage());
    }
  }

  # Añade un nuevo administrador
  final public function Add(array $data) : array {
    $error = $this->errors($data,true);
    if(!is_bool($error)) {
      return $error;
    }

    Helper::load('strings');

    $a = array(
      'email' => $data['email'],
      'pass' => Strings::hash($data['pass']),
      'admin' => 1
    );

    $this->db->insert('users',$a);

    return array('success' => 1, 'message' => '<b>Realizado: </b> Administrador creado');
  }

  # Edita un administrador
  final public function Edit(array $data) : array {
    $error = $this->errors($data);
    if(!is_bool($error)) {
      return $error;
    }

    $e = array(
      'email' => $data['email']
    );

    # ¿Llenó el campo de contraseña? Si es así, lleno el arreglo $e con la posición 'pass' para que se actualice
    if(!Func::emp($data['pass'])) {
      Helper::load('strings');
      $e['pass'] = Strings::hash($data['pass']);
    }

    # Esta variable se coloca en topnav.phtml para indicar el email del admin actual
    $_SESSION['app_email_admin'] = $data['email'];

    # Por qué no uso $this->id? porque estoy desde la API REST, aquí no está definida esa ruta ID
    $id = (int) $data['id'];
    $this->db->update('users',$e,"id='$id' AND admin='1'");

    return array('success' => 1, 'message' => '<b>Realizado: </b> Administrador editado');
  }

  # Elimina un administrador
  final public function Del() {

    $s = '?error=true';
    
    # Si el id del admin a borrar, no es el que tiene la sesión abierta
    if($this->id != $this->id_user) {
     $this->db->delete('users',"id='$this->id' AND admin='1'");
     $s = '?success=true';
    }

    Func::redir(URL . 'admins/' . $s);
  }

  # Chequea la existencia de un admin, si no existe devuelve false, si existe devuelve su información
  final public function check_admin() {
    return $this->db->select('id,email','users',"id='$this->id' AND admin='1'");
  }

  # Obtiene todos los administradores (Ojo, return array porque asumo que SIEMPRE devuelve al menos un admin)
  final public function get_admins() : array {
    return $this->db->select('id,email','users',"admin='1'");
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
