<?php

class Ruta {
	private $id;
	private $nombre;
	private $idgradproc_origen;
    private $idgradproc_destino;
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

	function getIdGradProcOrigen() {
		return $this->idgradproc_origen;
	}

	function setIdGradProcOrigen($idgradproc_origen) {
		$this->idgradproc_origen = $idgradproc_origen;
	}

	function getIdGradProcDestino() {
		return $this->idgradproc_destino;
	}

	function setIdGradProcDestino($idgradproc_destino) {
		$this->idgradproc_destino = $idgradproc_destino;
	}

	function getEtiqueta() {
		return $this->etiqueta;
	}

	function setEtiqueta($etiqueta) {
		$this->etiqueta = $etiqueta;
	}
  
	function getRutasByIdProcOrigen() { //para devolver las rutas posibles dado un grado-procedimiento origen
		$result = array('error' => false);

		$sql = "SELECT GT_R.*, GT_P.nombre AS procedimiento_destino, GT_RA.nombre AS rol_area_destino
				FROM gt_ruta GT_R 
				LEFT JOIN gt_grado_procedimiento AS GT_GP ON GT_R.idgradproc_destino = GT_GP.id
				LEFT JOIN gt_procedimiento AS GT_P ON GT_GP.idprocedimiento = GT_P.id
				LEFT JOIN gt_rol_area AS GT_RA ON GT_GP.idrol_area = GT_RA.id
				WHERE GT_R.idgradproc_origen = $this->idgradproc_origen AND GT_R.condicion = 1";
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

		$result['message'] = $sql;  
		
		return $result;
	}  
}