<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

/*
$app->get('/example',function($request, $response){

  $e = new Example;
  $response->withJson($e->Foo($_GET));

  return $response;
});
*/

/*
  Obtiene estados
  @return json con información acerca de posibles errores
*/
$app -> get ( '/hoteles/estados' , function($request , $response)
{
    $model = new Hoteles;
    $response -> withJson ( $model->getStates( (int) $_GET['id_pais'] ) ) ;

    return $response ;
} ) ;
