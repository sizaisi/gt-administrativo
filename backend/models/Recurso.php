<?php

class Recurso {
	protected $id;
	protected $idexpediente;
    protected $idprocedimiento;
    protected $idusuario;    
    protected $idmovimiento;
    protected $idruta;		
	
	private $conn;
	
	public function __construct() {
		$this->conn = Database::conectar();
	}
	
	function getId() {
		return $this->id;
	}

	function setId($id) {
		$this->id = $id;
	}

	public function getIdExpediente() {
		return $this->idexpediente;
	}

	function setIdExpediente($idexpediente) {
		$this->idexpediente = $idexpediente;
    }	
    
    function getIdProcedimiento() {
		return $this->idprocedimiento;
	}

	function setIdProcedimiento($idprocedimiento) {
		$this->idprocedimiento = $idprocedimiento;
    }	
    
    function getIdUsuario() {
		return $this->idusuario;
	}

	function setIdUsuario($idusuario) {
		$this->idusuario = $idusuario;
    }	
    
    function getIdMovimiento() {
		return $this->idmovimiento;
	}

	function setIdMovimiento($idmovimiento) {
		$this->idmovimiento = $idmovimiento;
    }	
    
    function getIdRuta() {
		return $this->idruta;
	}

	function setIdRuta($idruta) {
		$this->idruta = $idruta;
	}	
	
	//verificar si existe recursos en otras rutas del mismo procedimiento sin confirmacion (no hay movimiento)
	public function verificarRecursosVecinos() {
  
		$result = array('error' => false);
  
		$sql = "SELECT GT_RE.*
			    FROM gt_recurso AS GT_RE			    
			    WHERE GT_RE.idexpediente = $this->idexpediente 
				AND GT_RE.idprocedimiento = $this->idprocedimiento 
				AND GT_RE.idusuario = $this->idusuario 
				AND GT_RE.idruta <> $this->idruta 
				AND GT_RE.idmovimiento IS NULL 
				AND GT_RE.idruta IN (SELECT id
								     FROM gt_rutas AS GT_R 								   
								     wHERE GT_R.idproc_origen = $this->idprocedimiento)";
  
		$result_query = mysqli_query($this->conn, $sql);

		if ($result_query) {  
			if (mysqli_num_rows($result_query) == 0) {         
				$result['existeRecursoRutaVecinas'] = false;
			}
			else {
				$result['existeRecursoRutaVecinas'] = true;
			}			
		}
		else {
			$result['error'] = true;
			$result['message'] = "No se pudo verificar recursos en rutas vecinas.";    			
		}	      
  
		return $result;
	}   
}