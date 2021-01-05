<?php
require_once 'models/Movimiento.php';

class MovimientoController 
{	
	public function ultimoMovimiento() 
	{ 
		$movimiento = new Movimiento();		
		$idproc_destino = $_POST['idproc_destino'];  		  
		$movimiento->setIdExpediente($_POST['idexpediente']);		
		$result = $movimiento->getLastMovimiento($idproc_destino);

		echo json_encode($result);          
	}
	
	public function expedientes_enviados() 
	{ 
		$movimiento = new Movimiento();		
		$movimiento->setIdUsuario($_POST['idusuario']);
		$idproc_origen = $_POST['idproc_origen'];  		  		
		$result = $movimiento->getExpedientesEnviados($idproc_origen);

		echo json_encode($result);          
	}
	
	public function mover() 
	{
		$movimiento = new Movimiento();
		$movimiento->setIdUsuario($_POST['idusuario']);   
		$movimiento->setIdExpediente($_POST['idexpediente']);                                         
		$movimiento->setIdRuta($_POST['idruta']);
		$movimiento->setIdMovAnterior($_POST['idmov_anterior']);
		$idproc_origen = $_POST['idproc_origen'];                                                     
		$idproc_destino = $_POST['idproc_destino'];
		$estado_expediente = $_POST['estado_expediente'];                                                     
		$result = $movimiento->mover($idproc_origen, $idproc_destino, $estado_expediente);

		echo json_encode($result);        
	}
	
	public function deshacer() 
	{
		$movimiento = new Movimiento();
		$movimiento->setId($_POST['id']);
		$movimiento->setIdExpediente($_POST['idexpediente']);   
		$idproc_origen = $_POST['idproc_origen'];
		$fecha_ant = $_POST['fecha_ant'];
		$estado_expediente_ant = $_POST['estado_expediente_ant'];
		$result = $movimiento->deshacer($idproc_origen, $fecha_ant, $estado_expediente_ant);

		echo json_encode($result);       
	}		
}