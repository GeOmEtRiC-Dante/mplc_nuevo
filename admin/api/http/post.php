<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

/*
$app->post('/example',function($request, $response){

  $e = new Example;
  $response->withJson($e->Foo($_POST));

  return $response;
});
*/

//------------------------------------------------

/**
  * Inicio de Sesión
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/login',function($request, $response) {

  $login = new Login();
  $response->withJson($login->SignIn($_POST));

  return $response;
});

/**
	* Crea un hotel
	* @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/hoteles/crear',function($request, $response) {

	$model = new Hoteles;
	$response->withJson($model->Add($_POST));

	return $response;
});

/**
	* Edita un hotel
	* @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/hoteles/editar',function($request, $response) {

	$model = new Hoteles;
	$response->withJson($model->Edit($_POST));

	return $response;
});


/**
	* Sube fotografías a un hotel
	* @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/hoteles/uploads',function($request, $response) {

	$model = new Hoteles;
	$response->withJson($model->upload_images());

	return $response;
});


/**
	* Elimina fotografías en stand by que serán subidas en un hotel
	* @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/hoteles/uploads/delete',function($request, $response) {

	$model = new Hoteles;
	$response->withJson($model->delete_image($_POST['name'],$_POST['tmp_dir']));

	return $response;
});

/**
 * Elimina fotografías que YA existen en el hotel (se usa en la edición)
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/hoteles/deleteimg',function($request, $response) {

	$model = new Hoteles;
	$response->withJson($model->delete_image_in_hotel($_POST['route']));

	return $response;
});


/**
	* Subir Imagenes para el Slider Frontend
	* @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/slider/upload',function($request, $response) {

	$s = new Slider;
	$response->withJson($s->upload($_POST));

	return $response;
});


/**
	* Elimina una entidad
	* @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/entities/delete',function($request, $response) {

	$e = new Entities;
	$response->withJson($e->del_entity($_POST));

	return $response;
});

/**
 * Añade una entidad
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/entities/add',function($request, $response) {

	$e = new Entities;
	$response->withJson($e->add_entity($_POST));

	return $response;
});

/**
 * Edita una entidad
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/entities/edit',function($request, $response) {

	$e = new Entities;
	$response->withJson($e->edit_entity($_POST));

	return $response;
});

/**
 * Crea una divisa
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/divisas/add',function($request, $response) {

	$d = new Divisas;
	$response->withJson($d->Add($_POST));

	return $response;
});

/**
 * Edita una divisa
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/divisas/edit',function($request, $response) {

	$d = new Divisas;
	$response->withJson($d->Edit($_POST));

	return $response;
});


/**
 * Crea un administrador
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/admins/add',function($request, $response) {

	$a = new Admins;
	$response->withJson($a->Add($_POST));

	return $response;
});

/**
 * Edita un administrador
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/admins/edit',function($request, $response) {

	$a = new Admins;
	$response->withJson($a->Edit($_POST));

	return $response;
});


/**
 * Configuración general
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/config',function($request, $response) {
	$model = new Config;
	$response->withJson($model->add_config($_POST));
	return $response;
});


/**
 * Envío de promociones
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/suscriptores/promo',function($request, $response) {
	$model = new Suscriptores;
	$response->withJson($model->Send($_POST));
	return $response;
});

/**
 * Agrega nuevos usuarios
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/usuarios/add',function($request, $response) {
	$model = new Usuarios;
	$response->withJson($model->Add($_POST));
	return $response;
});

/**
 * Edita un usuario
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/usuarios/edit',function($request, $response) {
	$model = new Usuarios;
	$response->withJson($model->Edit($_POST));
	return $response;
});

/**
 * Agrega una nueva agencia
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/agentes/add',function($request, $response) {
	$model = new Agentes;
	$response->withJson($model->Add($_POST));
	return $response;
});

/**
 * Edita una nueva agencia
 * @return Devuelve un json con información acerca del éxito o posibles errores.
 */
$app->post('/agentes/edit',function($request, $response) {
	$model = new Agentes;
	$response->withJson($model->Edit($_POST));
	return $response;
});
