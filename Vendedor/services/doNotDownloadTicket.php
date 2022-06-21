<?php

    session_start();
    unset($_SESSION["cart"][$_SESSION['id_punto_de_venta']]);
    echo json_encode("not printing ticket :)");

?>