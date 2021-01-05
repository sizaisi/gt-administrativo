<?php

class Ruta {
	private $id;
	private $nombre;
	private $idproc_origen;
    private $idproc_destino;
	private $etiqueta;
	
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

	function getNombre() {
		return $this->nombre;
	}

	function setNombre($nombre) {
		$this->nombre = $nombre;
	}	

	function getIdProcOrigen() {
		return $this->idproc_origen;
	}

	function setIdProcOrigen($idproc_origen) {
		$this->idproc_origen = $idproc_origen;
	}

	function getIdProcDestino() {
		return $this->idproc_destino;
	}

	function setIdProcDestino($idproc_destino) {
		$this->idproc_destino = $idproc_destino;
	}

	function getEtiqueta() {
		return $this->etiqueta;
	}

	function setEtiqueta($etiqueta) {
		$this->etiqueta = $etiqueta;
	}
  
	function getRutasByIdProcOrigen() { //para devolver las rutas posibles dado un grado-procedimiento origen
		$result = array('error' => false);

		$sql = "SELECT GT_R.*, GT_P.nombre AS procedimiento_destino, GT_RO.nombre AS rol_area_destino
				FROM gt_rutas GT_R 
				LEFT JOIN gt_procedimientos AS GT_P ON GT_R.idproc_destino = GT_P.id				
				LEFT JOIN gt_roles AS GT_RO ON GT_P.idrol = GT_RO.id
				WHERE GT_R.idproc_origen = $this->idproc_origen AND GT_R.deleted_at IS NULL";
		$result_query = mysqli_query($this->conn, $sql);

		if ($result_query) {			
			$array_ruta = array();

			while ($row = $result_query->fetch_assoc()) {
				array_push($array_ruta, $row);
			}

			$result['array_ruta'] = $array_ruta;  			
		}
		else {
			$result['error'] = true;
			$result['message'] = "No se pudo obtener las rutas.";            
		}		  

		//$result['message'] = $sql;  
		
		return $result;
	}  
}