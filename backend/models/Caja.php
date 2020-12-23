<?php
require_once 'config/dbcaja.php';

class Caja {       
    private $conn;
	
    public function __construct() 
    {
		$this->conn = CajaDatabase::conectar();
	}
    	
    public function getPagosProfesionalTesis($cui, $nues, $espe) 
    {
        $result = array('error' => false);              

        $sql = "SELECT RC.RECA_ID AS idrecibo, RC.RECA_FDIG AS fecha_pago, 
                    CONCAT(RC.RECA_SERIE, '-', RC.RECA_CORRELATIVO) AS nro_recibo                       
                FROM CJCO_RECI_CABE AS RC                    		    
                WHERE RC.RECA_CODI_USUA = '20143489' 
                    AND RC.RECA_DEPE_USUA = '450'                    
                    AND RC.RECA_ANHO >= '2017'";

        $result_query = mysqli_query($this->conn, $sql);

        $array_pagos = array();

        while ($row = $result_query->fetch_assoc()) {
            $sql2 = "SELECT RD.REDE_COSTO AS monto, RD.REDE_CONC_ID AS idconcepto, C.CONC_DESC AS concepto
                     FROM CJCO_RECI_DETA AS RD		 
                     INNER JOIN CJMM_CONCEPTO AS C ON RD.REDE_CONC_ID = C.CONC_ID   
                     WHERE RD.REDE_CONC_ID IN (13, 27, 961)
                        AND RD.REDE_ESTA IN (0, 1)
                        AND RD.RECA_ID = '" . $row['idrecibo'] . "'";
            $result_query2 = mysqli_query($this->conn, $sql2);                      

            if (mysqli_num_rows($result_query2) > 0) {                 
                $row2 = $result_query2->fetch_assoc();
                $row['monto'] = $row2['monto'];
                $row['idconcepto'] = $row2['idconcepto'];
                $row['concepto'] = $row2['concepto'];

                array_push($array_pagos, $row);
            }
        }

        $result['array_pagos'] = $array_pagos;      

        return $result;
        
        /*$result = array('error' => false);  

        $sql = "SELECT RC.RECA_ID AS idrecibo, RC.RECA_FDIG AS fecha_pago, 
                       CONCAT(RC.RECA_SERIE, '-', RC.RECA_CORRELATIVO) AS nro_recibo, 
                       RD.REDE_COSTO AS monto, RD.REDE_CONC_ID AS idconcepto, C.CONC_DESC AS concepto
                FROM CJCO_RECI_CABE AS RC 
                    INNER JOIN CJCO_RECI_DETA AS RD ON RC.RECA_ID = RD.RECA_ID 
                    INNER JOIN CJMM_CONCEPTO AS C ON RD.REDE_CONC_ID = C.CONC_ID                                     
                WHERE RD.REDE_CONC_ID IN (8, 13, 27, 100, 148, 961)
                        AND RD.REDE_ESTA IN (0, 1)
                        AND RC.RECA_FDIG >= '2017-01-01'
                    AND RC.RECA_CODI_USUA = '20143489' 
                    AND RC.RECA_DEPE_USUA = '450'"; 

        $result_query = mysqli_query($this->conn, $sql);

        $array_pagos = array();

        while ($row = $result_query->fetch_assoc()) {                          
            array_push($array_pagos, $row);
        }

        $result['array_pagos'] = $array_pagos;      

        return $result;*/
    }
}