<?php

final class Dashboard extends Models implements OCREND {

    public function __construct() {
        parent::__construct();
    }

    # Obtiene la cantidad de X elemento
    final private function get_e(string $table, string $where = '1=1', string $e = 'id') : int {
        $sql = $this->db->query("SELECT COUNT($e) FROM $table WHERE $where;");
        return $sql->fetch()[0];
    }

    # Obtiene la cantidad de hoteles
    final public function get_hotels() : int {
        return $this->get_e('hotels');
    }

    # Obtiene la cantidad de clientes registrados
    final public function get_clients() : int {
        return $this->get_e('users',"admin='0'");
    }

    # Obtiene la cantidad de visitas diarias
        final public function get_visitas() : int {
        $sql = $this->db->query("SELECT cantidad,timer FROM visitas WHERE id='1' LIMIT 1;");
        $data = $sql->fetch();

        # Si ya ha pasado un día, reiniciamos el contador a 0
        if($data['timer'] <= time()) {
            $e = array(
                'cantidad' => 0,
                'timer' => time() + (60*60*24)
            );
            $this->db->update('visitas',$e,"id='1'");

            return 0;
        }

        return $data['cantidad'];
    }

    # Genera código javascript para el gráfico de divisas
    final public function get_divisas_graf() : string {
        # Obtengo un arreglo con TODAS las divisas
        $divisas = $this->db->select('*','divisas');

        # Preparo la query en la que el valor variable será el ID de la divisa
        $prepare = $this->db->prepare('SELECT COUNT(id) FROM hotels WHERE divisa = ? ;');

        # Recorro todas las divisas
        foreach($divisas as $d) {
            # Aquí estoy asignando en el "?" de la query, el valor del id de la divisa y ejecutando la query preparada
            $prepare->execute(array($d['id']));
            $script[] = array(
                'divisa' => $d['nombre'] . ' (' . $d['signo'] . ')',
                'cantidad' => $prepare->fetchAll()[0][0] # Aquí obtengo el resultado de la query prepada
            );
        }

        # Convierto en JSON el arreglo, así se despliega como un arreglo de javascript en la vista
        return json_encode($script);
    }


    public function __destruct() {
        parent::__destruct();
    }

}

?>
