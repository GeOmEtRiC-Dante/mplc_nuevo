<?php

final class Hoteles extends Models implements OCREND {

  private $imagesdir;

  # Extrae información sobre entidades
  final private function get_entities_info(string $table) : array {
    return $this->select_array($this->db->query("SELECT * FROM $table;"));
  }

  # Cargas las imagenes del directorio temporal, al definitivo del hotel y elimina el directorio temporal
  final private function upload_in_hotel_dir(int $the_id) {
    Helper::load('files');
    $new_dir = '../../views/app/images/hoteles/'. $the_id .'/';
    if(!is_dir($new_dir)) {
      mkdir($new_dir,0777,true);
    }

    foreach (glob($this->imagesdir . '*') as $img) {
      if(Files::is_image($img)) {
        $name = explode('/',$img);
        $name = end($name);
        $name_no_ext = explode('.',$name);

        if(file_exists($new_dir . $name)) {
          unlink($new_dir . $name);
        }

        if(strtolower($name_no_ext[0]) == 'perfil') {
          # Copiamos el thumb
          $this->generate_thumbnail($img,$new_dir . 'perfil_thumb',Files::get_file_ext($img));
        }
        # Copiamos la original
        copy($img,$new_dir . $name);
      }
      unlink($img);
    }

  }

  final private function generate_thumbnail(string $oldimage, string $newroute, string $ext) {
    # Obtenemos la imagen original
    if(in_array($ext,['jpg','JPG','jpeg','JPEG'])) {
      $original = imagecreatefromjpeg($oldimage);
    } else if(in_array($ext,['png','PNG'])) {
      $original = imagecreatefrompng($oldimage);
    } else {
      $original = imagecreatefromgif($oldimage);
    }

    # Hacemos el thumb en blanco con una dimensión de 270x160
    $thumb = imagecreatetruecolor(270,160);

    # Obtengo las dimensiones del a original
    $ancho = imagesx($original);
    $alto = imagesy($original);

    # Copiamos la imagen original en el thumbnail centrandola para no deformar
    imagecopyresampled($thumb,$original, 0, 0, abs(($ancho - 270) / 2), abs(($alto - 160) / 2), $ancho, $alto, $ancho, $alto);

    # Guardamos en el disco duro (95 es la calidad de compresión)
    if(in_array($ext,['jpg','JPG','jpeg','JPEG'])) {
      imagejpeg($thumb,$newroute .'.'. $ext,95);
    } else if(in_array($ext,['png','PNG'])) {
      imagepng($thumb,$newroute .'.'. $ext);
    } else {
      imagegif($thumb,$newroute .'.'. $ext);
    }

  }

  # Control de errores sobre los parámetros recibidos en los formularios crear/editar hotel
  final private function check_errors(array $data, bool $edit = false) {
    try {

      # Todos los campos aquí pasados, deben estar llenos
      if(Func::e($data['name'],$data['direc'],$data['descrip'],$data['markup'],$data['destiny'],$data['hab_child_range'],$data['hab_inft_range'])) {
        throw new Exception('<b>Error:</b> Los campos marcados con <span class="red" style="font-weight:bold;">*</span> deben estar llenos.');
      }

      if(!array_key_exists(0, $data['hab_name']) or Func::emp($data['hab_name'][0])) {
        throw new Exception('<b>Error:</b> Debe existir al menos una habitación para el hotel.');
      }

      $this->imagesdir = '../views/app/.tmp/'.$data['tmp_dir'].'/';
      $glob = glob($this->imagesdir . '{*.jpg,*.gif,*.png,*.gif,*.jpeg,*.JPG,*.GIF,*.PNG,*.JPEG}',GLOB_BRACE);
      if(count($glob) == 0 and !$edit) {
        throw new Exception('<b>Error:</b> Debe existir al menos una imagen subida con el formato (jpg, png, gif o jpeg).');
      }

      return true;
    } catch(Exception $e) {
      return $e->getMessage();
    }
  }

  # Crea el Json de las habitaciones
  final private function habs(array $data) : string {

    $i = 0;
    foreach ($data['hab_name'] as $h) {
      if(!Func::emp($h)) {

        # En caso de edición, verificamos la existencia de hab_$i_extra[]
        if(isset($data['hab_' . $i . '_extra'])) {
          foreach($data['hab_' . $i . '_extra'] as $ext) {
            $extras[$ext] = $data['hab_' . $i . '_extra_price_' . $ext];
          }
        } else {
          $extras['null'] = 0;
        }

        $habs[] = array(
            'nombre' => $h,
            'plan' => (int) $data['hab_plan'][$i],
            'totales' => Func::emp($data['hab_totales'][$i]) ? 0 : (int) $data['hab_totales'][$i],
            'extras' => $extras
        );

        /*
          Algoritmo:
          Dentro de cada ocupaciónun Json con las tarifas asociadas a el de la forma
          'tarifas' => array(
            '01-08_01-09' => array(
              'lun-vie' => array(
                'precio' => 100,
                'ninos' => 500,
              ),
              'sab-dom' => array(
                'precio' => 100,
                'ninos' => 500,
              )

            )
          )


        */

        $ocups = array();
        foreach ($data['hab'.$i.'_ocup'] as $id) {
          $tarifas = array();

          if(isset($data['tarifas'])) {

            foreach($data['tarifas'] as $z => $t) {
              $s = explode('_',$t);
              $hab = $s[0];
              $ocup = $s[1];

              if($i == $hab) {
                if($ocup == $id) {

                  /*$fecha = '';
                  foreach($data['fecha_i_dia_hab'.$hab.'_ocup'.$ocup] as $num => $fech) {
                    $fecha = $fech . '-' .  $data['fecha_i_mes_hab'.$hab.'_ocup'.$ocup][$num] . '_';
                    $fecha .= $data['fecha_f_dia_hab'.$hab.'_ocup'.$ocup][$num] . '-' . $data['fecha_f_mes_hab'.$hab.'_ocup'.$ocup][$num];


                    $prices = array();
                    foreach($data['dia_i_hab'.$hab.'_ocup'.$ocup.'_'.$num] as $n => $c_dias) {
                      $dias = $c_dias . '-' . $data['dia_f_hab'.$hab.'_ocup'.$ocup.'_'.$num][$n];
                      $prices[$dias] = array(
                          'precio' => (float) $data['price_hab'.$hab.'_ocup'.$ocup.'_'.$num][$n],
                          'ninos' => (float) $data['price_childs_hab'.$hab.'_ocup'.$ocup.'_'.$num][$n]
                      );
                    }

                    $tarifas[$fecha] = $prices;
                  }*/

                  $fecha = '';
                  foreach($data['fecha_inicio_hab'.$hab.'_ocup'.$ocup] as $num => $fech) {
                    $fecha = $data['fecha_inicio_hab'.$hab.'_ocup'.$ocup][$num];
                    $fecha .= '_' . $data['fecha_final_hab'.$hab.'_ocup'.$ocup][$num];

                    $prices = array();
                    foreach($data['dia_i_hab'.$hab.'_ocup'.$ocup.'_'.$num] as $n => $c_dias) {
                      $dias = $c_dias . '-' . $data['dia_f_hab'.$hab.'_ocup'.$ocup.'_'.$num][$n];
                      $prices[$dias] = array(
                          'precio' => (float) $data['price_hab'.$hab.'_ocup'.$ocup.'_'.$num][$n],
                          'ninos' => (float) $data['price_childs_hab'.$hab.'_ocup'.$ocup.'_'.$num][$n]
                      );
                    }

                    $tarifas[$fecha] = $prices;
                  }

                }

              }

            }
          } else {
            # Carga por defecto
            $tarifas = array(
                '01-01-'.date('Y').'_01-02-'.date('Y') => array(
                    'lun-dom' => array(
                        'precio' => 0,
                        'ninos' => 0
                    )
                )
            );
          }

          if(!Func::emp($data['hab'.$i.'_ocup' . $id . '_name'])) {
            $ocups[] = array(
                'active' => 1,
                'nombre' => $data['hab'.$i.'_ocup' . $id . '_name'],
                'adultos' => (int) $data['hab'.$i.'_ocup' . $id . '_adults'],
                'ninos' => (int) $data['hab'.$i.'_ocup' . $id . '_childs'],
                'tarifas' => $tarifas
            );
          }
        }


        $habs[$i]['ocupaciones'] = $ocups;

        $i++;
      }
    }

    return json_encode($habs);
  }

  /**
   * Métodos públicos
   */
  public function __construct() {
    parent::__construct();
  }

  # Genera un directorio temporal para la subida de imagenes antes de la creación del Hotel en la BD
  final public function generate_tmp_dir() {
    $tmp = uniqid();
    $dir = 'views/app/.tmp/';
    if(!is_dir($dir . $tmp)) {
      mkdir($dir . $tmp,0777,true);
    } else {
      $tmp = $tmp . md5(time());
      mkdir($dir . $tmp,0777,true);
    }

    return $tmp;
  }

  # Borra una imagen
  final public function delete_image(string $name, string $dir) : array {

    $dir = '../views/app/.tmp/' .$dir .'/' . $name;

    if(file_exists($dir)) {
      unlink($dir);
    }

    return array('success' => 1, 'message' => $dir);
  }

  # Sube una imágen (API REST)
  final public function upload_images() {

    if (!empty($_FILES)) {
      $dir = '../views/app/.tmp/' .$_POST['tmp_dir'] .'/';
      $name = $_FILES['file']['name'];
      $tempFile = $_FILES['file']['tmp_name'];

      if(file_exists($dir . $name)) {
        $dir = $dir . time() . $name;
      } else {
        $dir = $dir . $name;
      }
      move_uploaded_file($tempFile,$dir);
    }

    return array('success' => 0, 'message' => 'test');
  }

  # Crea un nuevo hotel, llamado desde api/hoteles/crear (API REST)
  final public function Add(array $data) : array {
    $check = $this->check_errors($data);
    if(!is_bool($check)) {
      return array('success' => 0, 'message' => $check, 'url' => '#');
    }

    # Doc framework.ocrend.com/modelos/
    $a = array(
        'name' => $data['name'],
        'categ' => $data['categ'],
        'markup' => $data['markup'],
        'destiny' => $data['destiny'],
        'map' => $data['map'],
        'without_markup' => (isset($data['markup_exists']) and $data['markup_exists'] == '1') ? 1 : 0,
        'type' => $data['type'],
        'country' => $data['country'],
        'state' => $data['state'],
        'city' => $data['city'],
        'direc' => $data['direc'],
        'descrip' => $data['descrip'],
        'tyc' => $data['tyc'],
        'childs' => $data['hab_child_range'],
        'infant' => $data['hab_inft_range'],
        'features' => isset($data['features']) ? json_encode($data['features']) : json_encode(['0']),
        'habs' => $this->habs($data),
        'check_in' => $data['check_in'],
        'check_out' => $data['check_out'],
        'divisa' => $data['divisa']
    );

    # Guardamos en la base de datos
    $this->db->insert('hotels',$a);

    # En este punto todo está perfecto y se suben las imágenes
    $the_id = $this->db->lastInsertId();
    $this->upload_in_hotel_dir($the_id);

    return array(
        'success' => 1,
        'message' => '<b>Realizado:</b> Hotel <b>'.$data['name'].'</b> creado con éxito.',
        'url' => 'hoteles/editar/' . $the_id .'/?success_create=true'
    );
  }

  # Edita información acerca de un hotel, llamado desde api/hoteles/editar
  final public function Edit(array $data) : array {

    # Ya que se está tomando desde la API REST, la ruta $this->route->getId() evidentemente no se está tomando
    $this->id = (int) $data['id'];

    $check = $this->check_errors($data,true);
    if(!is_bool($check)) {
      return array('success' => 0, 'message' => $check, 'url' => '#');
    }

    # A editar
    $e = array(
        'name' => $data['name'],
        'categ' => $data['categ'],
        'destiny' => $data['destiny'],
        'markup' => $data['markup'],
        'map' => $data['map'],
        'without_markup' => (isset($data['markup_exists']) and $data['markup_exists'] == '1') ? 1 : 0,
        'type' => $data['type'],
        'country' => $data['country'],
        'state' => $data['state'],
        'city' => $data['city'],
        'direc' => $data['direc'],
        'descrip' => $data['descrip'],
        'tyc' => $data['tyc'],
        'childs' => $data['hab_child_range'],
        'infant' => $data['hab_inft_range'],
        'features' => isset($data['features']) ? json_encode($data['features']) : json_encode(['0']),
        'habs' => $this->habs($data),
        'check_in' => $data['check_in'],
        'check_out' => $data['check_out'],
        'divisa' => $data['divisa']
    );

    # Guardamos en la base de datos
    $this->db->update('hotels',$e,"id='$this->id'");

    # En este punto todo está perfecto y se suben las imágenes
    $this->upload_in_hotel_dir($this->id);

    return array(
        'success' => 1,
        'message' => '<b>Realizado:</b> Hotel <b>'.$data['name'].'</b> editado con éxito.',
        'url' => 'hoteles/editar/' . $this->id .'/?success=true'
    );
  }

  /*
    Dice si existe o no un hotel por su id, pasada por por hoteles/editar/ID
    Si este existe, devuelve un PDOStatement con la información del hotel
    Si no existe devuelve false
  */
  final public function check_hotel_exist() {
    return $this->db->select('*','hotels',"id='$this->id'",'LIMIT 1');
  }

  # Elimina un hotel pasado por hoteles/eliminar/ID
  final public function Del() {
    $this->db->delete('hotels',"id='$this->id'");
    Func::redir(URL .'hoteles/?success=true');
  }

  # Obtiene todos los hoteles existentes desde el más reciente hasta el más viejo creado
  final public function getHotels() {
    return $this->db->select('id,name,categ,destiny,country,type','hotels','1=1','ORDER BY id DESC');
  }

  # Transforma un object de query en un arreglo para los selects
  final private function select_array(PDOStatement $query) : array {
    foreach ($query as $i) {
      $e[$i['id']] = array_key_exists('name',$i) ? $i['name'] : $i['nombre'];
    }
    return $e;
  }

  # Obtiene los países
  final public function get_countries() : array {
    return $this->select_array($this->db->query("SELECT id,name FROM countries;"));
  }

  # Obtiene los estados de todos los países, cuando se le pasa un ID, se buscan solo los estados de ese país
  final public function get_states(int $id = 0) : array {
    if(0 == $id) {
      return $this->select_array($this->db->query("SELECT id,name FROM states;"));
    }
    return $this->select_array($this->db->query("SELECT id,name FROM states WHERE id_country='$id';"));
  }

  #  Se buscan solo los estados de ese país y genera las opciones del select (API REST)
  final public function getStates(int $id ) : array {

    $resul = $this->db->select ( 'id,name' , 'states' , "id_country='$id'") ;

    $resp = '';
    foreach ( $resul as $value )
    {
      $resp .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
    }

    return array ('resultado' => $resp);
  }

  # Carga inicial de ocupaciones por defecto
  final public function get_default_ocup() : array {
    return array(
        array(true,'Sencilla',1,0),
        array(true,'Doble',2,1),
        array(true,'Triple',3,2),
        array(false,'Cuádruple',4,3),
        array(false,'Quintuple',5,4),
        array(false,'Séxtuple',6,5)
    );
  }

  # Obtiene las imagenes que están cargadas del hotel
  final public function get_images() {
    Helper::load('files');
    $files = Files::get_files_in_dir('../views/app/images/hoteles/'.$this->id.'/');
    if(sizeof($files) > 0) {
      return $files;
    }

    return false;
  }

  # Borra una imagen YA subida en un hotel
  final public function delete_image_in_hotel(string $route) : array {
    $route = '../' . $route;
    if(file_exists($route)) {
      unlink($route);
    }
    return array('success' => 1);
  }

  # Borra todos los directorios temporales que estén vacíos y no creados hace 2 horas
  final public function free_tmp_dirs() {
    $tmp_dir = 'views/app/.tmp/';
    Helper::load('files');
    foreach(glob($tmp_dir . '*') as $dir) {
      if(filectime($dir) < (time() - (60*60*2))) {
        Files::rm_dir($dir);
        rmdir($dir);
      }
    }
  }

  # Extrae información acerca de las categorías
  final public function get_categorias() {
    return $this->get_entities_info('categ');
  }

  # Extrae información acerca de los planes
  final public function get_planes() {
    return $this->get_entities_info('planes');
  }

  # Extrae información acerca de los tipos de alojamiento
  final public function get_alojamientos() {
    return $this->get_entities_info('tipos_alojamiento');
  }

  # Obtiene las divisas
  final public function get_divisas() : array {
    return $this->get_entities_info('divisas');
  }

  # Obtiene las características en la base de datos (no las del hotel, si no las de los checkbox)
  final public function get_features() : array {
    return $this->get_entities_info('hotels_features');
  }

  # Obtiene los extras de costes para habitaciones
  final public function get_extras() : array {
    return $this->get_entities_info('habs_extras');
  }


  # Excepto esta claro está
  public function __destruct() {
    parent::__destruct();
  }

}

?>
