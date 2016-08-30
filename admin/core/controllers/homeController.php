<?php

class homeController extends Controllers {

  public function __construct() {
    parent::__construct(false, true);

    $d = new Dashboard;

    echo $this->template->render('home/home', array(
      'hoteles' => $d->get_hotels(),
      'visitas' => $d->get_visitas(),
      'clientes' => $d->get_clients(),
      'divisas' => $d->get_divisas_graf()
    ));
  }

}

?>
