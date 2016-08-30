<?php

/**
  * Ocrend Framework - MVC Architecture for Web Applications
  * PHP Version 7
  * @package Ocrend Framework
  * @version v1.0.7
  * @author Brayan Narváez (Prinick) <prinick@ocrend.com> <youtube.com/user/prinick96>
  * @copyright 2016 - Ocrend Software
  * @license	http://opensource.org/licenses/MIT	MIT License
  * @link http://framework.ocrend.com/
*/

//------------------------------------------------
# Carga del núcleo
define('INDEX_DIR',true);
require('core/app_core.php');

//------------------------------------------------

# Detección del controlador actual
$Controller = $router->getController();

//------------------------------------------------

# Identificación del controlador en el sistema
if(!is_readable('core/controllers/' . $Controller . '.php')) {
  # Para no mostrar el 404, mandamos al dashboard
  $Controller = 'homeController';
}

# Carga del controlador seleccionado
require('core/controllers/' . $Controller . '.php');
new $Controller;

//------------------------------------------------

# Modo debug
!DEBUG ?: new Debug($startime);

?>
