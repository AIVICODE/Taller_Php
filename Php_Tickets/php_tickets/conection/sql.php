<?php
function conectar(){
    $server = "localhost";
    $user = "hphp";
    $pass = "Php.2025#";
    $db = "hphp_equipo6";
	$conn = mysqli_connect($server, $user, $pass, $db);
    if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
    }
    //echo "Connected successfully";
	return $conn;
}

function desconectar($conn){
    mysqli_close($conn);
}

function modifico($conn, $sql){
	$resultado = mysqli_query($conn,$sql) or die ("Error al modificar el contenido de la base de datos");
}

function buscar($conn, $sql){
	$resultado = mysqli_query($conn,$sql) or die ("Error al buscar");
	return $resultado;
}

?>