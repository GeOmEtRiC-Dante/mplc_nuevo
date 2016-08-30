<?php

class loginController extends Controllers {

  public function __construct() {
    parent::__construct(false,true);
    echo $this->template->render('login/login');
  }

}

?>
