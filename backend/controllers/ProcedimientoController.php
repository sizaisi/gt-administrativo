<?php
require_once 'models/Procedimiento.php';

class ProcedimientoController 
{			
	public function getProcedimientos() 
	{		
		$procedimiento = new Procedimiento();      
		$procedimiento->setIdGradoModalidad($_POST['idgrado_modalidad']);
		$procedimiento->setIdRolArea($_POST['idrol_area']);
		$idusuario = $_POST['idusuario'];
		$codi_usuario = $_POST['codi_usuario'];
		$tipo_usuario = $_POST['tipo_usuario'];
		$result = $procedimiento->getProcedimientos($idusuario, $codi_usuario, $tipo_usuario);

		echo json_encode($result);           				
	}	
	
}