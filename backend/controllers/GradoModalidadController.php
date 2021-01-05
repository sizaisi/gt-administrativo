<?php
require_once 'models/GradoModalidad.php';

class GradoModalidadController
{
	public function inicio()
	{
		$codi_usuario = $_POST['codi_usuario'];
		$idrol = $_POST['idrol'];
		$grado_modalidad = new GradoModalidad();
		$result = $grado_modalidad->getGradoModalidades($codi_usuario, $idrol);

		echo json_encode($result);		
	}	
}
