<?php
class Tramite {
    private $conn;

    public function __construct() {
        $this->conn = Database::conectar();
    }    

    public function getTramites($codi_usuario, $idrol) {
        $result = array('error' => false);

        $sql = "SELECT * FROM gt_tramites WHERE deleted_at IS NULL ORDER BY nombre ASC";

        $result_query = mysqli_query($this->conn, $sql);

        $array_tramite = array();

        while ($row = $result_query->fetch_assoc()) {

            $sql2 = "SELECT COUNT(*) AS total_expedientes 
                        FROM gt_procedimientos AS P INNER JOIN gt_expediente AS GE
                        ON P.id = GE.idprocedimiento
                        WHERE P.idrol = $idrol
                        AND GE.nues IN (SELECT codi_depe FROM SIAC_OPER_DEPE WHERE codi_oper='$codi_usuario') 
                        AND P.idtramite = " . $row['id'];

            $result_query2 = mysqli_query($this->conn, $sql2);

            $row2 = $result_query2->fetch_assoc();

            if ($row2['total_expedientes'] > 0) { //obtener solo aquellos items que tengan expedientes en proceso
                $row['total_expedientes'] = $row2['total_expedientes'];
                array_push($array_tramite, $row);
            }
        }

        $result['array_tramite'] = $array_tramite;        

        return $result;
    }    

    /*public function searchByIdGradoTitulo($id) {
        $result = array('error' => false);

        $sql = "SELECT * FROM gt_grado_modalidad WHERE idgrado_titulo = $id";
        $result_query = mysqli_query($this->conn, $sql);

        $array_result = array();

        while ($row = $result_query->fetch_assoc()) {
            array_push($array_result, $row);
        }

        $result['array_result'] = $array_result;

        return $result;
    }*/    
}
