<?php

final class Datos extends Models implements OCREND {

    public function __construct() {
        parent::__construct();
    }

    # Método para obtener los datos que se mostrarán en la tabla principal
    final public function get_datos(string $campos, string $where = '') {
        return $this->db->select($campos, 'users', $where);
    }

    # Método para obtenener los datos de un usuario o agencia la cual se editará
    final public function check_datos(string $where = ''){
        return $this->db->select('*','users',"id='$this->id' $where");
    }

    # Método para borrar un usuario o agencia
    final public function Delete(string $where = '', string $url){
        $this->db->delete('users',"id='$this->id' $where");
        Func::redir(URL . $url . '?success=true');
    }

    # Método para activar un usuario o agencia
    final public function Activar(string $where = '', string $redir){
        $user =  $this->check_datos("AND admin !='1'");

        if(false === $user) {
            Func::redir(URL . $redir);
            return;
        }

        $user = $user[0];

        $e['activo'] = '1';
        $s = '?activar=true';

        if ($user['activo'] == '1') {
            $e['activo'] = '0';
            $s = '?desactivar=true';
        }

        $this->db->update('users', $e, "id='$this->id' $where");

        Func::redir(URL . $redir . $s);
    }

    public function __destruct() {
        parent::__destruct();
    }
}

?>