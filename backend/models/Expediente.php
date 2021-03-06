<?php
class Expediente {
	private $id;
	private $url_repo;

	private $conn;

	function getId() {
		return $this->id;
	}

	function setId($id) {
		$this->id = $id;
	}

	function getUrlRepo() {
		return $this->url_repo;
	}

	function setUrlRepo($url_repo) {
		$this->url_repo = $url_repo;
	}

	public function __construct() {
		$this->conn = Database::conectar();
	}
	
	public function getList($idprocedimiento, $codi_usuario) {
		$result = array('error' => false);  
		
		$sql = "SELECT GT_E.id, GT_E.codigo, GT_E.fecha AS fecha_recepcion, GT_E.estado, 
						GROUP_CONCAT(REPLACE(AC_I.apn,'/',' ') SEPARATOR ' / ') AS graduando,
						AC_E.nesc AS escuela
				FROM gt_expediente AS GT_E
					INNER JOIN gt_graduando_expediente AS GT_GE ON GT_GE.idexpediente = GT_E.id 
					INNER JOIN gt_graduando AS GT_G ON GT_G.id = GT_GE.idgraduando 																	
					INNER JOIN acdiden AS AC_I ON AC_I.cui = GT_G.cui	
					INNER JOIN actescu AS AC_E ON AC_E.nues = GT_E.nues							
					INNER JOIN SIAC_OPER_DEPE AC_OP ON AC_OP.codi_depe = AC_E.nues						
				WHERE GT_E.idprocedimiento = $idprocedimiento					
					AND AC_OP.codi_oper = '$codi_usuario'						
				GROUP BY GT_GE.idexpediente 
				ORDER BY GT_E.id ASC";					
		$result_query = mysqli_query($this->conn, $sql);

		if ($result_query) {
			$array_expediente = array();
  
			while ($row = $result_query->fetch_assoc()) {         			
				array_push($array_expediente, $row);
			}
	
			$result['array_expediente'] = $array_expediente;      
		}
		else {
			$result['error'] = true;                    
			$result['message'] = "No se pudo obtener los expedientes, vuelve a intentarlo más tarde.";
		}
		  
		return $result;
	}  

	public function getListByCodi($codi_usuario) {  
		$result = array('error' => false);
  
		$sql="SELECT DISTINCT gt_expediente.*,actescu.nesc
			  FROM gt_movimiento 
			  INNER JOIN gt_expediente ON gt_expediente.id = gt_movimiento.idexpediente
			  INNER JOIN gt_usuario ON gt_usuario.id = gt_movimiento.idusuario
			  INNER JOIN actescu ON gt_expediente.nues= actescu.nues
			  WHERE gt_usuario.codi_usuario='$codi_usuario'";	 
		$result_query = mysqli_query($this->conn, $sql);
  
		$array_expediente = array();
  
		while ($row = $result_query->fetch_assoc()) {            
			$sql2 = "SELECT REPLACE(acdiden.apn,'/',' ') AS apell_nombres FROM acdiden 
						INNER JOIN gt_graduando ON gt_graduando.cui = acdiden.cui
						INNER JOIN gt_usuario ON gt_usuario.codi_usuario = gt_graduando.cui
						INNER JOIN gt_usuario_expediente ON gt_usuario_expediente.idusuario = gt_usuario.id
						INNER JOIN gt_expediente ON gt_expediente.id = gt_usuario_expediente.idexpediente
						WHERE gt_expediente.id = ".$row['id'];							
			$result_query2 = mysqli_query($this->conn, $sql2);

			$row2 = $result_query2->fetch_assoc();
			$row['apell_nombres'] = $row2['apell_nombres'];         
						
			array_push($array_expediente, $row);						
		}

		$result['array_expediente'] = $array_expediente;

		return $result;
	 }
  
	 public function getMovByIdExp($idexpediente) {  
		$result = array('error' => false);
  
		$sql =  "SELECT gt_movimiento.* FROM gt_expediente
				 INNER JOIN gt_movimiento ON gt_expediente.id = gt_movimiento.idexpediente
				 INNER JOIN gt_usuario ON gt_usuario.id = gt_movimiento.idusuario
				 WHERE gt_expediente.id = '$idexpediente'";
  
		$result_query = mysqli_query($this->conn, $sql);
  
		$row = $result_query->fetch_assoc();      
  
		$result['array_movimiento'] = $row;      
  
		return $result;
	 }

	 public function getURL() {
  
		$result = array('error' => false);
  
		$sql = "SELECT url_repo FROM gt_expediente WHERE id = $this->id";

		$result_query = mysqli_query($this->conn, $sql);

		if ($result_query) {			
			if (mysqli_num_rows($result_query) > 0) {
				$row = $result_query->fetch_assoc();        
				$result['url_repo'] = $row['url_repo'];
			}			
			else {
				$result['error'] = true;
				$result['message'] = "No se pudo encontrar la url de repositorio.";            
			}			
		}
		else {
			$result['error'] = true;
			$result['message'] = "No se pudo obtener el url de repositorio.";            
		}		
  
		return $result;
	 }   
  
	 public function getExpediente() {
  
		$result = array('error' => false);  
				
		$sql = "SELECT GT_E.*, AC_E.nesc AS escuela, AC_F.nfac AS facultad, GT_G.cui, AC_G.grad_cred AS creditos
				FROM gt_expediente AS GT_E
					INNER JOIN gt_graduando_expediente AS GT_GE ON GT_GE.idexpediente = GT_E.id
					INNER JOIN gt_graduando AS GT_G ON GT_G.id = GT_GE.idgraduando					
					INNER JOIN actescu AS AC_E ON GT_E.nues = AC_E.nues
					INNER JOIN actfacu AS AC_F ON AC_F.facu = AC_E.facu
					INNER JOIN ACM_GRADUADO AS AC_G ON AC_G.cui = GT_G.cui
				WHERE AC_G.nues = GT_E.nues 
					AND AC_G.espe = GT_E.espe
					AND GT_E.id = $this->id";
  
		$result_query = mysqli_query($this->conn, $sql);
  
		if ( $row = $result_query->fetch_assoc() ) {
			$cui = $row['cui'];
			$acdlnues = 'acdl' . $row['nues'];
			$acdhnues = 'acdh' . $row['nues'];

			$sql2 = "SELECT DATE_FORMAT(MAX(fech), '%d-%m-%Y') AS max_fecha_evaluacion
					 FROM ( SELECT * 
					        FROM $acdlnues 
							WHERE cui='$cui' 
							UNION 
							SELECT * 
							FROM $acdhnues
							WHERE cui='$cui' ) as tabla 
					 WHERE anoh = (
							SELECT max(anoh) 
							FROM (SELECT anoh 
									FROM $acdlnues
									WHERE cui='20002728' 
									UNION 
								  SELECT anoh 
								  FROM $acdhnues
								  WHERE cui='$cui') as subtabla ) 
					  AND fech <> ''";

            $result_query2 = mysqli_query($this->conn, $sql2);           

            if ( $row2 = $result_query2->fetch_assoc() ) {
                $row['max_fecha_evaluacion'] = $row2['max_fecha_evaluacion'];                
			}
			
			$sql3 = "SELECT DATE_FORMAT(MIN(fdig), '%d-%m-%Y') AS fecha_primera_matricula
					FROM
					(
						SELECT * FROM $acdlnues WHERE cui='$cui'
						UNION
						SELECT * FROM $acdhnues WHERE cui='$cui'
					) AS tabla
					WHERE SUBSTRING(casi,4,1)=1
					AND anoh = (SELECT min(anoh)
									FROM (SELECT anoh FROM $acdlnues WHERE cui='$cui'
											UNION
											SELECT anoh FROM $acdhnues WHERE cui='$cui'
										) AS subtabla
								)
					AND anoh >= 1995 AND fdig<>''";

            $result_query3 = mysqli_query($this->conn, $sql3);           

            if ( $row3 = $result_query3->fetch_assoc() ) {
                $row['fecha_primera_matricula'] = $row3['fecha_primera_matricula'];                
            }
		}      
  
		$result['expediente'] = $row;      
  
		return $result;
	 }  
	 
	 public function actualizar_url(){
        $result = array('error' => false);

        $sql = "UPDATE gt_expediente SET url_repo = '$this->url_repo' WHERE id = $this->id";

        $result_query = mysqli_query($this->conn, $sql);

        if ($result_query) {
            $result['message'] = "URL actualizado con éxito.";
        }
        else {
            $result['error'] = true;
            $result['message'] = "No se pudo actualizar el URL.";
        }

        return $result;   
	}
	
	public function getAsesorPropuesto() {
		$result = array('error' => false);

		$sql = "SELECT REPLACE(AC_D.apn, '/', ' ') AS apn				
				FROM gt_usuario AS GT_U
				INNER JOIN SIAC_DOC AS AC_D ON AC_D.codper = GT_U.codi_usuario 
				WHERE GT_U.id = (SELECT idasesor 
							   FROM gt_expediente
							   WHERE id = $this->id)";	

		$result_query = mysqli_query($this->conn, $sql);

		if ($result_query) {			
			$result['asesor_propuesto'] = $result_query->fetch_assoc();
		}
		else {
			$result['error'] = true;
			$result['message'] = "No se pudo obtener el asesor propuesto.";            
		}		
  
		return $result;		
	}   
}