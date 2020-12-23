<?php
class Procedimiento {
	private $id;
	private $idgrado_modalidad;
    private $idprocedimiento;
    private $idrol_area;
    private $tipo_rol; 
    private $url;
    private $orden;
    private $descripcion;

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

	function getIdGradoModalidad() {
		return $this->idgrado_modalidad;
	}

	function setIdGradoModalidad($idgrado_modalidad) {
		$this->idgrado_modalidad = $idgrado_modalidad;
	}	

	function getIdProcedimiento() {
		return $this->idprocedimiento;
	}

	function setIdProcedimiento($idprocedimiento) {
		$this->idprocedimiento = $idprocedimiento;
	}

	function getIdRolArea() {
		return $this->idrol_area;
	}

	function setIdRolArea($idrol_area) {
		$this->idrol_area = $idrol_area;
	}

	function getTipoRol() {
		return $this->tipo_rol;
	}

	function setTipoRol($tipo_rol) {
		$this->tipo_rol = $tipo_rol;
	}

	function getUrl() {
		return $this->url;
	}

	function setUrl($url) {
		$this->url = $url;
	}

	function getOrden() {
		return $this->orden;
	}

	function setOrden($orden) {
		$this->orden = $orden;
    }
    
    function getDescripcion() {
		return $this->descripcion;
	}

	function setDescripcion($descripcion) {
		$this->descripcion = $descripcion;
	}   

    public function getProcedimientos($idusuario, $codi_usuario, $tipo_usuario) {

        $result = array('error' => false);
        
        $sql = "SELECT idgrado_procedimiento FROM gt_usuario_grado_procedimiento WHERE idusuario = $idusuario";
        $result_query = mysqli_query($this->conn, $sql);       
  
		if ($result_query && mysqli_num_rows($result_query) == 0) {

            if ($tipo_usuario == 'Administrativo') {
                $sql = "SELECT GT_P.*, GT_P.id AS idproc, COUNT(GT_E.id) AS total_expedientes,
                        GT_P.nombre AS proc_nombre, GT_P.descripcion AS proc_descripcion 
                        FROM gt_procedimientos AS GT_P                        
                        INNER JOIN gt_expediente AS GT_E ON GT_E.idgrado_procedimiento = GT_P.id
                        INNER JOIN SIAC_OPER_DEPE AS AC_OP ON AC_OP.codi_depe = GT_E.nues
                        WHERE GT_P.deleted_at IS NULL AND GT_P.idgradomodalidad = $this->idgrado_modalidad 
                        AND GT_P.idrol =  $this->idrol_area
                        AND AC_OP.codi_oper = '$codi_usuario'
                        GROUP BY GT_P.id 
                        ORDER BY GT_P.id ASC";

            }
            else if ($tipo_usuario == 'Docente') {	
                $sql = "SELECT GT_GP.*, GT_P.id AS idproc, COUNT(GT_E.id) AS total_expedientes,
                        GT_P.nombre AS proc_nombre, GT_GP.descripcion AS proc_descripcion 
                        FROM gt_grado_procedimiento AS GT_GP
                        INNER JOIN gt_procedimiento AS GT_P ON GT_P.id = GT_GP.idprocedimiento 
                        INNER JOIN gt_expediente AS GT_E ON GT_E.idgrado_procedimiento = GT_GP.id                    
                        WHERE GT_P.condicion = 1 AND GT_GP.idgrado_modalidad = $this->idgrado_modalidad 
                        AND GT_GP.idrol_area =  $this->idrol_area
                        AND GT_E.id IN (SELECT R.idexpediente
                                        FROM gt_recurso AS R
                                            INNER JOIN gt_persona AS P ON P.idrecurso = R.id
                                            INNER JOIN gt_usuario AS U ON U.id = P.iddocente
                                        WHERE IF(GT_GP.tipo_rol='asesor', P.tipo='asesor', P.tipo IN ('presidente', 'secretario', 'suplente')) 
                                            AND P.estado = 1  
                                            AND U.codi_usuario='$codi_usuario')                    
                        GROUP BY GT_GP.id 
                        ORDER BY GT_GP.id ASC";
            }
            else if ($tipo_rol == 'jurado') {
                $sql = "SELECT GT_GP.*, GT_P.id AS idproc, COUNT(GT_E.id) AS total_expedientes,
                        GT_P.nombre AS proc_nombre, GT_GP.descripcion AS proc_descripcion 
                        FROM gt_grado_procedimiento AS GT_GP
                        INNER JOIN gt_procedimiento AS GT_P ON GT_P.id = GT_GP.idprocedimiento 
                        INNER JOIN gt_expediente AS GT_E ON GT_E.idgrado_procedimiento = GT_GP.id                    
                        WHERE GT_P.condicion = 1 AND GT_GP.idgrado_modalidad = $this->idgrado_modalidad 
                        AND GT_GP.idrol_area =  $this->idrol_area
                        AND GT_E.id IN (SELECT R.idexpediente
										FROM gt_recurso AS R
										INNER JOIN gt_persona AS P ON P.idrecurso = R.id
										INNER JOIN gt_usuario AS U	ON U.id = P.iddocente									
										WHERE P.tipo IN ('presidente', 'secretario', 'suplente') 
										AND P.estado = 1 
										AND U.codi_usuario='$codi_usuario')
                        GROUP BY GT_GP.id 
                        ORDER BY GT_GP.id ASC";
            }          
		}
		else {
            /*$array_idgrado_procedimiento = array();

            while($row = $result_query->fetch_assoc()) {
                $array_idgrado_procedimiento[] = $row['idgrado_procedimiento'];
            }

			$sql = "SELECT GT_GP.*, GT_P.id AS idproc, GT_P.nombre AS proc_nombre, GT_P.descripcion AS proc_descripcion 
                    FROM gt_grado_procedimiento AS GT_GP
                    INNER JOIN gt_procedimiento AS GT_P ON GT_P.id = GT_GP.idprocedimiento 
                    WHERE GT_P.condicion = 1 AND GT_GP.idgrado_modalidad = $this->idgrado_modalidad 
                    AND GT_GP.idrol_area =  $this->idrol_area 
                    AND GT_GP.id IN (" . implode(',', $array_idgrado_procedimiento) . ") ORDER BY GT_GP.id ASC";*/
		}        

        $result_query = mysqli_query($this->conn, $sql);

        $array_grado_procedimiento = array();

        while ($row = $result_query->fetch_assoc()) {
            array_push($array_grado_procedimiento, $row);
        }

        $result['array_grado_procedimiento'] = $array_grado_procedimiento;        

        return $result;
    }    
}