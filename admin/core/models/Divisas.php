<?php

final class Divisas extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }

  # Control de errores para add y edit
  final private function errors(array $data) {
    try {
      if(!Func::all_full($data) or !is_numeric($data['tasa'])) {
        throw new Exception('<b>Error:</b> Todos los campos deben estar llenos.');
      }

      return false;
    } catch(Exception $e) {
      return array('success' => 0, 'message' => $e->getMessage());
    }
  }

  # Agrega una divisa
  final public function Add(array $data) : array {
    if(!is_bool($this->errors($data))) {
      return $this->errors($data);
    }

    $a = array(
      'nombre' => $data['nombre'],
      'tasa' => $data['tasa'],
      'signo' => $data['signo']
    );
    $this->db->insert('divisas',$a);

    return array('success' => 1, 'message' => '<b>Realizado:</b> divisa creada con éxito.');
  }

  # Edita una divisa
  final public function Edit(array $data) : array {
    if(!is_bool($this->errors($data))) {
      return $this->errors($data);
    }

    $e = array(
        'nombre' => $data['nombre'],
        'tasa' => $data['tasa'],
        'signo' => $data['signo']
    );

    $id = (int) $data['id'];
    $this->db->update('divisas',$e,"id='$id'");

    return array('success' => 1, 'message' => '<b>Realizado:</b> divisa creada con éxito.');
  }

  # Elimina una divisa
  final public function Del() {

    $this->db->delete('divisas',"id='$this->id'");

    Func::redir(URL . 'divisas/'); # Faltaba redireccionar
  }

  # Obtiene todas las divisas si se pasa true, obtiene una sola si se pasa false (si no la consigue redirecciona al listado)
  final public function get_divisas(bool $multiple) {
    if($multiple) {
      return $this->db->select('*','divisas');
    }

    $result = $this->db->select('*','divisas',"id='$this->id'");
    if(false === $result) {
      Func::redir(URL . 'divisas/');
    }

    return $result;
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
