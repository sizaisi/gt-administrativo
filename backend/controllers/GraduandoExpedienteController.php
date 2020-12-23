<?php
require_once 'models/GraduandoExpediente.php';

class GraduandoExpedienteController 
{
    public function getGraduando() { 	
        $graduando_expediente = new GraduandoExpediente();
        $graduando_expediente->setIdExpediente($_POST['idexpediente']);
        $result = $graduando_expediente->getGraduando();

        echo json_encode($result);           
    }
}