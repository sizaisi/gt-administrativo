<?php
require_once 'models/Tramite.php';

class TramiteController
{
	public function inicio()
	{
		$codi_usuario = $_POST['codi_usuario'];
		$idrol = $_POST['idrol'];

		$tramite = new Tramite();
		$result = $tramite->getTramites($codi_usuario, $idrol);

		echo json_encode($result);		
	}	
}
