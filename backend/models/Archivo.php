<?php
require_once 'Recurso.php';

class Archivo extends Recurso {	
	private $nombre_asignado;
	private $nombre_archivo;
	private $mime;
	private $data;
	
	private $conn;
	
	public function __construct() {
		$this->conn = Database::conectar();
	}
	
	function getNombreAsignado() {
		return $this->nombre_asignado;
	}

	function setNombreAsignado($nombre_asignado) {
		$this->nombre_asignado = $nombre_asignado;
	}	

	function getNombreArchivo() {
		return $this->nombre_archivo;
	}

	function setNombreArchivo($nombre_archivo) {
		$this->nombre_archivo = $nombre_archivo;
	}	

	function getMime() {
		return $this->mime;
	}

	function setMime($mime) {
		$this->mime = $mime;
	}	

	function getData() {
		return $this->data;
	}

	function setData($data) {
		$this->data = $data;
	}

	public function getTodosArchivos() {

		$result = array('error' => false);		
  
		$sql = "SELECT GT_R.id, GT_A.nombre_asignado AS nombre, GT_A.mime, 
					   GT_P.nombre AS procedimiento, GT_RO.nombre AS area 
				FROM gt_recurso AS GT_R
				INNER JOIN gt_archivo GT_A ON GT_A.idrecurso = GT_R.id				
				INNER JOIN gt_procedimientos GT_P ON GT_P.id = GT_R.idprocedimiento				
				INNER JOIN gt_usuario GT_U ON GT_U.id = GT_R.idusuario
				INNER JOIN gt_roles GT_RO ON GT_RO.id = GT_U.idrol
				WHERE GT_R.idexpediente = $this->idexpediente ORDER BY id ASC";
		$result_query = mysqli_query($this->conn, $sql);
  
		$array_archivo = array();
  
		while ($row = $result_query->fetch_assoc()) {         
		   array_push($array_archivo, $row);
		}
  
		$result['array_archivo'] = $array_archivo;      
  
		return $result;
	 }   

	 public function getArchivosProcOrigen() {

		$result = array('error' => false);
  
		$sql = "SELECT GT_RE.id, GT_A.nombre_asignado AS nombre, GT_A.mime, 
					   GT_P.nombre AS procedimiento, GT_RO.nombre AS area 
				FROM gt_recurso AS GT_RE
				INNER JOIN gt_archivo GT_A ON GT_A.idrecurso = GT_RE.id				
				INNER JOIN gt_procedimientos GT_P ON GT_P.id = GT_RE.idprocedimiento 				
				INNER JOIN gt_usuario GT_U ON GT_U.id  = GT_RE.idusuario
				INNER JOIN gt_roles GT_RO ON GT_RO.id  = GT_U.idrol 
				WHERE GT_RE.idexpediente = $this->idexpediente 
				AND GT_RE.idprocedimiento = ( SELECT GT_R.idproc_origen 
											FROM gt_movimiento GT_M 
											INNER JOIN gt_rutas GT_R ON GT_M.idruta = GT_R.id 
											WHERE GT_R.idproc_destino = $this->idprocedimiento 
											AND GT_M.idexpediente = $this->idexpediente AND GT_R.deleted_at IS NULL
											ORDER BY GT_M.id desc limit 1
								  		) 
				ORDER BY GT_RE.id ASC";			
		
		$result_query = mysqli_query($this->conn, $sql);
  
		$array_archivo_ultimo = array();
  
		while ($row = $result_query->fetch_assoc()) {         
		   array_push($array_archivo_ultimo, $row);
		}
  
		$result['array_archivo_ultimo'] = $array_archivo_ultimo;      
  
		return $result;
	 }   
  
	 public function getArchivo() {
  
		$result = array('error' => false);

		$sql = "SELECT GT_R.id, GT_A.nombre_asignado, GT_A.nombre_archivo, GT_A.mime
				FROM gt_recurso AS GT_R
				INNER JOIN gt_archivo AS GT_A ON GT_A.idrecurso = GT_R.id
				WHERE GT_R.idexpediente = $this->idexpediente 
				AND GT_R.idprocedimiento = $this->idprocedimiento 
				AND GT_R.idusuario = $this->idusuario
				AND GT_R.idmovimiento IS NULL";		

		$result_query = mysqli_query($this->conn, $sql);
  
		if ($result_query) {
			$array_documento = array();
  
			while ($row = $result_query->fetch_assoc()) {         
				array_push($array_documento, $row);
			}
	
			$result['array_documento'] = $array_documento;
		}
		else {
			$result['error'] = true;
			$result['message'] = "No se pudo obtener los documentos o archivos.";
		}		      
  
		return $result;
	 }
  
	 public function insertar() {

		$result = array('error' => false);   
		$this->conn->autocommit(FALSE); //iniciar transaccion	
		
		$sql = "INSERT INTO gt_recurso(idexpediente, idprocedimiento, idusuario, idmovimiento, idruta) 
				VALUES ($this->idexpediente, $this->idprocedimiento, $this->idusuario, NULL, $this->idruta)";      
		$result_query = mysqli_query($this->conn, $sql);     
		
		$idrecurso = NULL;

		if (!$result_query) {
		   	$result['error'] = true;                    
		}       
		else {
			$idrecurso = mysqli_insert_id($this->conn);
		}
  
		$sql = "INSERT INTO gt_archivo(idrecurso, nombre_asignado, nombre_archivo, mime, data) 
				VALUES ($idrecurso, '$this->nombre_asignado', '$this->nombre_archivo', '$this->mime', '$this->data')";		
		$result_query = mysqli_query($this->conn, $sql);

		if (!$result_query) {
			$result['error'] = true;                    
		} 

		if ( $result['error'] == false) { //si no hay ningun error en querys
			$this->conn->commit();          
			$result['message'] = "Archivo registrado correctamente.";
		 }
		else {
			$this->conn->rollback(); // deshacer transaccion
			$result['message'] = "No se pudo registrar el archivo.";
		}
   
		$this->conn->autocommit(TRUE); //finalizar transaccion           
  
		return $result;
	 }
  
	 public function eliminar() {

		$result = array('error' => false);                
		$this->conn->autocommit(FALSE); //iniciar transaccion	
		
		$sql = "DELETE FROM gt_archivo where idrecurso = $this->id";      
		$result_query = mysqli_query($this->conn, $sql);     	
		
		if (!$result_query) {
		   	$result['error'] = true;                  
		}       	
		
		$sql = "DELETE FROM gt_recurso where id = $this->id AND idmovimiento IS NULL";
		$result_query = mysqli_query($this->conn, $sql);     		
  
		if (!$result_query) {
			$result['error'] = true;              	       
		} 	

		if (mysqli_affected_rows($this->conn) == 0) { //no debe eliminar si el recurso ya tiene movimiento
			$result['error'] = true;
		}
		
		if( $result['error'] == false) { //si no hay ningun error en querys
		   	$this->conn->commit();          
		   	$result['message'] = "Archivo eliminado correctamente.";
		}
		else {
		   	$this->conn->rollback(); // deshacer transaccion
		   	$result['message'] = "No se pudo eliminar el archivo.";
		}
  
		$this->conn->autocommit(TRUE); //finalizar transaccion
  
		return $result;
	 }
}