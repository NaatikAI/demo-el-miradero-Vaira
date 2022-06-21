<?php 
    require "../../services/connection.php";

    $bindings = [];
    $data=[];
    if($pdo!=null){
        error_log("Connection is not null");
        $bindings[] = file_get_contents('php://input');
        $bindings = json_decode($bindings[0]);
        $sql = 'SELECT fkSucursal FROM sucursal_usuario WHERE fkUsuario = :fkUsuario;';
        $stmt = $pdo->prepare($sql);

        if($stmt->execute(array(
            ':fkUsuario' => $bindings->fkUsuario
        ))){
            // while($row = $stmt->fetch(PDO::FETCH_NUM)){
                $data = $stmt->fetch(PDO::FETCH_NUM);
            // }
            // $data[] = "Success";
          
        }else{
            $data[] = "Error";
        }
    }
    else{
        $data[] = "Connection Error";
    }
    echo json_encode($data);
?>
