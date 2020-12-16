<?php

class GradoModalidad
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::conectar();
    }    

    public function getGradoModalidades($codi_usuario, $idrol_area)
    {
        $result = array('error' => false);

        $sql = "SELECT GM.id, GT.nombre AS nombre_grado_titulo, GMO.nombre AS nombre_modalidad_obtencion
                FROM gt_grado_modalidad AS GM 
                INNER JOIN gt_grado_titulo AS GT ON GM.idgrado_titulo = GT.id 
                INNER JOIN gt_modalidad_obtencion AS GMO ON GM.idmodalidad_obtencion = GMO.id
                WHERE GM.condicion = 1
                ORDER BY nombre_grado_titulo ASC, GM.id ASC";

        $result_query = mysqli_query($this->conn, $sql);

        $array_grado_modalidad = array();

        while ($row = $result_query->fetch_assoc()) {

            $sql2 = "SELECT COUNT(*) AS total_expedientes 
                        FROM gt_grado_procedimiento AS GP INNER JOIN gt_expediente AS GE
                        ON GP.id = GE.idgrado_procedimiento
                        WHERE GP.idrol_area = $idrol_area
                        AND GE.nues IN (SELECT codi_depe FROM SIAC_OPER_DEPE WHERE codi_oper='$codi_usuario') 
                        AND GP.idgrado_modalidad = " . $row['id'];

            $result_query2 = mysqli_query($this->conn, $sql2);

            $row2 = $result_query2->fetch_assoc();

            if ($row2['total_expedientes'] > 0) { //obtener solo aquellos items que tengan expedientes en proceso
                $row['total_expedientes'] = $row2['total_expedientes'];
                array_push($array_grado_modalidad, $row);
            }
        }

        $result['array_grado_modalidad'] = $array_grado_modalidad;

        return $result;
    }    

    public function searchByIdGradoTitulo($id)
    {
        $result = array('error' => false);

        $sql = "SELECT * FROM gt_grado_modalidad WHERE idgrado_titulo = $id";
        $result_query = mysqli_query($this->conn, $sql);

        $array_result = array();

        while ($row = $result_query->fetch_assoc()) {
            array_push($array_result, $row);
        }

        $result['array_result'] = $array_result;

        return $result;
    }    
}
