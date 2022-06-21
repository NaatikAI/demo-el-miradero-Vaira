<?php 
session_start();

session_destroy();
$_SESSION = array(); // ! Limpiamos el array de sesiones

header('location: ../index.php');

?>