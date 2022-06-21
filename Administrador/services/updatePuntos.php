<?php 
    require "../../services/connection.php";

    $bindings = [];
    $data=[];
    if($pdo!=null){
        error_log("Connection is not null");
        $bindings[] = file_get_contents('php://input');
        $sql = 'CALL actualizar_puntos_usuario(?);';
        $stmt = $pdo->prepare($sql);
        if($stmt->execute($bindings)){
            $data[] = "Success";
          
        }else{
            $data[] = "Error";
        }
    }
    else{
        $data[] = "Connection Error";
    }
    echo json_encode($data);
?>
