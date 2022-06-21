<?php 
    session_start();
    foreach ($_SESSION["cart"][$_SESSION['id_punto_de_venta']] as $clave => $valor){
        if($valor["id_producto"] == $_GET['id_producto']){
            $_SESSION["cart"][$_SESSION['id_punto_de_venta']][$clave]["cantidad"] = $_GET["nueva_cantidad"];
        }
    }
    unset($_GET["id_producto"]);
    unset($_GET["nueva_cantidad"]);

    echo json_encode("sucess");


?>