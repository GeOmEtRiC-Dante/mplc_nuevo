<?php

final class Entities extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }

  # Vagancia
  final private function return_success(int $s, int $id = 0, string $m = '<b>Realizado:</b> Operación realizada corectamente.') : array {
    return array('success' => $s, 'message' => $m, 'id' => $id);
  }

  # Control de errores para edit y add
  final private function errors(array $data)  {
    try {

      if(!isset($data['nombre']) or Func::emp($data['nombre'])) {
        throw new Exception('<b>Error:</b> El nombre no puede estar vacío');
      }

      return false;
    } catch(Exception $e) {
      return array('success' => 0, 'message' => $e->getMessage(), 'id' => 0);
    }
  }

  # Edita una entidad
  final public function edit_entity(array $data) : array {

    if(!is_bool($this->errors($data))) {
      return $this->errors($data);
    }

    $id = (int) $data['id'];

    $this->db->update($data['table'],array('nombre' => $data['nombre']),"id='$id'");

    return $this->return_success(1);
  }

  # Agrega una entidad
  final public function add_entity(array $data) : array {

    if(!is_bool($this->errors($data))) {
      return $this->errors($data);
    }

    $this->db->insert($data['table'],array('nombre' => $data['nombre']));

    return $this->return_success(1,$this->db->lastInsertId());
  }

  # Elimina una entidad
  final public function del_entity(array $data) : array {
    $id = (int) $data['id'];
    $this->db->delete($data['table'],"id='$id'");

    return $this->return_success(1);
  }

  # Obtiene una entidad
  final public function get_entity(string $table) {
    return $this->db->select('*',$table);
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
