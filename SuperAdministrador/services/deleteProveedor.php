<?php 
    require "../../services/connection.php";

    $bindings = [];
    $data=[];
    if($pdo!=null){
        error_log("Connection is not null");
        $bindings[] = file_get_contents('php://input');
        $bindings = json_decode($bindings[0]);
        $sql = 'UPDATE proveedor SET activo = 0 WHERE idProveedor = :idProveedor;';
        $stmt = $pdo->prepare($sql);

        if($stmt->execute(array(
            ':idProveedor' => $bindings->idProveedor
        ))){
            $data = "Success";
          
        }else{
            $data[] = "Error";
        }
    }
    else{
        $data[] = "Connection Error";
    }
    echo json_encode($data);
?>
