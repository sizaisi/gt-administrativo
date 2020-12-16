<?php
require_once 'models/GradoProcedimiento.php';

class GradoProcedimientoController {	
		
	public function gradoProcedimientos() {		
		$grado_procedimiento = new GradoProcedimiento();      

		$grado_procedimiento->setIdGradoModalidad($_POST['idgrado_modalidad']);
		$grado_procedimiento->setIdRolArea($_POST['idrol_area']);

		$idusuario = $_POST['idusuario'];
		$codi_usuario = $_POST['codi_usuario'];
		$tipo_usuario = $_POST['tipo_usuario'];

		$result = $grado_procedimiento->getGradoProcedimientos($idusuario, $codi_usuario, $tipo_usuario);

		echo json_encode($result);           
	}	
	
}