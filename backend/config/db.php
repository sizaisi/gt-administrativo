<?php

class Database {
	public static function conectar() {		
		$conexion = new mysqli("localhost", "prueba", "prueba", "siac"); //local y administrativo		
		
		if ($conexion->connect_errno) {
            die("Fallo al conectar a MySQL: (" . $conexion->connect_errno . ") " . $conexion->connect_error);
        }
		
		$conexion->query("SET NAMES 'utf8'");		
		
		return $conexion;
	}
}
