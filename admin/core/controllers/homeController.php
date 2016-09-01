<?php

class homeController extends Controllers {

  public function __construct() {
    parent::__construct(true);
    echo $this->template->render('home/home');
  }

}

?>
