<?php

class homeController extends Controllers {

  public function __construct() {
    parent::__construct();
    Helper::load ( 'files' ) ;
    $imagenes = Files::get_files_in_dir ( 'views/app/images/sliders/' ) ;
    echo $this->template->render('home/home', array("imagenes" => $imagenes));
  }

}

?>
