<?php

// require("services/connection.php");

function limpiarDatos($datos){
    $datos = trim($datos); // * Elimina espacios antes y despues
    $datos = stripslashes($datos); // * Elimina '\'
    $datos = htmlspecialchars($datos); // * Convierte caracteres especiales en entidades HTML
    return $datos;
}

function fecha($fecha){
    $timestamp = strtotime($fecha);
    // $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    $dia = date('d', $timestamp);
    $mes = date('m', $timestamp);
    $year =  date('Y', $timestamp);

    $fecha = "$dia/" . $mes . "/$year";
    return $fecha;
}

function hora($fecha){
    $timestamp = strtotime($fecha);
    $hora = date('H:i', $timestamp);
    return $hora;
}

// function obtenerSucursalID($pdo, $userId, $punto_venta){
//     $stm = $pdo->prepare("SELECT fkSucursal FROM punto_venta WHERE fkUsuario = $userId AND idPunto = $punto_venta[0];");
//     $stm->execute();
//     $result = $stm->fetch();
//     return ($result) ? $result : false;
// }

// function obtenerPuntosVenta($pdo, $userId){
//     $stm = $pdo->prepare("SELECT idPunto FROM punto_venta WHERE fkUsuario = $userId;");
//     $stm->execute();
//     return $stm->fetchAll();
//     // return ($result) ? $result : false;
// }