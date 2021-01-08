<?php
class Procedimiento {
	private $id;
	private $idgrado_modalidad;
    private $idprocedimiento;
    private $idrol;
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

	function getIdRol() {
		return $this->idrol;
	}

	function setIdRol($idrol) {
		$this->idrol = $idrol;
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

    public function getProcedimientos($codi_usuario) {

        $result = array('error' => false);    
                    
        $sql = "SELECT GT_P.*, COUNT(GT_E.id) AS total_expedientes
                FROM gt_procedimientos AS GT_P                        
                INNER JOIN gt_expediente AS GT_E ON GT_E.idprocedimiento = GT_P.id
                INNER JOIN SIAC_OPER_DEPE AS AC_OP ON AC_OP.codi_depe = GT_E.nues
                WHERE GT_P.deleted_at IS NULL AND GT_P.idgradomodalidad = $this->idgrado_modalidad 
                AND GT_P.idrol =  $this->idrol
                AND AC_OP.codi_oper = '$codi_usuario'
                GROUP BY GT_P.id 
                ORDER BY GT_P.id ASC";          				

        $result_query = mysqli_query($this->conn, $sql);

        $array_procedimiento = array();

        while ($row = $result_query->fetch_assoc()) {
            array_push($array_procedimiento, $row);
        }

        $result['array_procedimiento'] = $array_procedimiento;        

        return $result;
    }    
}