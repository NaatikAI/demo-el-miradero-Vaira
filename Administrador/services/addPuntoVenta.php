<?php 
    require "../../services/connection.php";

    $bindings = [];
    $data=[];
    if($pdo!=null){
        error_log("Connection is not null");
        $bindings[] = file_get_contents('php://input');
        $bindings = json_decode($bindings[0]);
        $sql = 'INSERT INTO punto_venta VALUES (0, :sucursal, NULL, :nombre);';
        $stmt = $pdo->prepare($sql);

        if($stmt->execute(array(
            ':sucursal' => $bindings->sucursal,
            ':nombre' => $bindings->nombre
        ))){
            $data[] = $stmt->rowCount();
        }else{
            $data[] = "Error";
        }
    }
    else{
        $data[] = "Connection Error";
    }
    echo json_encode($data);
?>
