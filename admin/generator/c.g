<?php

class {{controller}} extends Controllers {

  public function __construct() {
    parent::__construct(true);
    Helper::load('bootstrap');

    /*
      switch ($this->method) {
        case 'crear':
        break;
        case 'editar':
        break;
        case 'eliminar':
        break;
        default:
        break;
      }
    */

    echo $this->template->render('{{view}}/{{view}}');
  }

}

?>
