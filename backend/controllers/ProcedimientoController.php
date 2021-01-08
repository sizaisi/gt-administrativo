<?php
require_once 'models/Procedimiento.php';

class ProcedimientoController 
{			
	public function getProcedimientos() 
	{		
		$procedimiento = new Procedimiento();      
		$procedimiento->setIdGradoModalidad($_POST['idgrado_modalidad']);
		$procedimiento->setIdRol($_POST['idrol']);		
		$codi_usuario = $_POST['codi_usuario'];		
		$result = $procedimiento->getProcedimientos($codi_usuario);

		echo json_encode($result);           				
	}	
	
}