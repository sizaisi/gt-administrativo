<?php
require_once 'models/Archivo.php';

class ArchivoController 
{        
    public function index() 
    { 
		$archivo = new Archivo();        
		$archivo->setIdExpediente($_POST['idexpediente']);
        $result = $archivo->getTodosArchivos();

        echo json_encode($result);           
    }
    
    public function show() 
    { 
        $archivo = new Archivo();                        
        $archivo->setIdProcedimiento($_POST['idprocedimiento']);
		$archivo->setIdExpediente($_POST['idexpediente']);
        $result = $archivo->getArchivosProcOrigen();

        echo json_encode($result);           
    } 

    public function get() 
    {
        $archivo = new Archivo();                
        $archivo->setIdExpediente($_POST['idexpediente']);
        $archivo->setIdProcedimiento($_POST['idprocedimiento']);
        $archivo->setIdUsuario($_POST['idusuario']);        
        $result = $archivo->getArchivo();

        echo json_encode($result);       
	}
	
    public function store() 
    {
        $archivo = new Archivo();                
        $archivo->setIdExpediente($_POST['idexpediente']);
        $archivo->setIdProcedimiento($_POST['idprocedimiento']);
        $archivo->setIdUsuario($_POST['idusuario']);
        $archivo->setIdRuta($_POST['idruta']);
        $archivo->setNombreAsignado($_POST['nombre_asignado']);
        $archivo->setNombreArchivo($_FILES['file']['name']);
        $archivo->setMime($_FILES['file']['type']);
        $archivo->setData(base64_encode(file_get_contents($_FILES['file']['tmp_name'])));                
        $result = $archivo->insertar();

        echo json_encode($result);         
	}	
		
    public function delete() 
    {
		$archivo = new Archivo();      
        $archivo->setId($_POST['id']);     
        $result = $archivo->eliminar();

        echo json_encode($result);           
    }      
}