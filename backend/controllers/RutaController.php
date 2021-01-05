<?php
require_once "models/Ruta.php";

class RutaController 
{	
	public function getRutasByProc() 
	{ 
		$ruta = new Ruta();				
		$ruta->setIdProcOrigen($_POST['idproc_origen']);    
		$result = $ruta->getRutasByIdProcOrigen();

		echo json_encode($result);             
	}
}