<?php

error_reporting(0);
require_once "vistas/VistaJson.php";
require_once "utilidades/ExcepcionApi.php";
require_once "umodelos/Objeto1.php";
require_once "umodelos/Objeto2.php";
require_once "umodelos/Objeto3.php";



const ESTADO_METODO_NO_PERMITIDO=403;
$objeto = NULL;

$vista = new VistaJson();

$peticion = $_REQUEST["PATH_INFO"];

$array = explode("/", $peticion);

$recurso = $array[0];
$peticion = $array[1];
$opc1 = $array[2];


$recursos_existentes = array('objeto1','objeto2','objeto3');

/**
 * Comprobamos si el recurso existe
 *
 **/
if (!in_array($recurso, $recursos_existentes)) {
	die("no existe el recurso solicitado");
}

$metodo = strtolower($_SERVER['REQUEST_METHOD']);


/**
 *  Arquitectura => ip_servidor/api/v1/objeto/funcion (metodos: get,post,put,delete)
 *
 *	Enrutador => se enruta el Objeto llamando de manera estatica a su funcion atraves de los metodos soportados.
 *
 *	Retorno => vista en json de la respuesta del metodo del objeto.
 *
 **/




switch ($recurso){

	case 'objeto1':

		$objeto=new Objeto1();
		enrutador($metodo,$objeto,$peticion,$vista);
		break;
	case 'objeto2':
		$objeto=new Objeto2();

		enrutador($metodo,$objeto,$peticion,$vista);
		break;
	case 'objeto3':
		$objeto=new Objeto3();
		enrutador($metodo,$objeto,$peticion,$vista);
		break;
	default: die('algo falla');
}

function enrutador($metodo,$objeto,$peticion,$vista){


	switch ($metodo) {
		case 'get':
			$vista->imprimir($objeto::get($peticion));
			break;
		case 'post':
			$vista->imprimir($objeto::post($peticion));
			// Procesar método post
			break;
		case 'put':
			$vista->imprimir($objeto::put($peticion));
			break;

		case 'delete':
			$vista->imprimir($objeto::delete($peticion));
			break;
		default:
			$vista->estado = 405;
			$cuerpo = [
				"estado" => ESTADO_METODO_NO_PERMITIDO,
				"mensaje" => utf8_encode("Esa solicitud es ilegal")
			];
			$vista->imprimir($cuerpo);
	}

}


set_exception_handler(function ($exception) use ($vista) {
		$cuerpo = array(
			"estado" => $exception->estado,
			"mensaje" => $exception->getMessage()
		);
		if ($exception->getCode()) {
			$vista->estado = $exception->getCode();
		} else {
			$vista->estado = 500;
		}

		$vista->imprimir($cuerpo);
	}
);

throw new ExcepcionApi(2, "Error con estado 2", 404);

?>