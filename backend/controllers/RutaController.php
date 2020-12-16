<?php
require_once "models/Ruta.php";

class RutaController {	

	public function getRutasByProc() { // obtener rutas salida por cada procedimiento
		$ruta = new Ruta();		
		
		$ruta->setIdGradProcOrigen($_POST['idgradproc_origen']);    

		$result = $ruta->getRutasByIdProcOrigen();

		echo json_encode($result);             
	}
}