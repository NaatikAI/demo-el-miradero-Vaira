<?php 
    require "../../services/connection.php";

    $bindings = [];
    $data=[];
    if($pdo!=null){
        error_log("Connection is not null");
        $bindings[] = file_get_contents('php://input');
        $bindings = json_decode($bindings[0]);
        $sql = 'SELECT idVenta, fecha, nombre, total FROM venta JOIN sucursal WHERE idSucursal = fkSucursal AND fkAdmin = :idUsuario;';
        $stmt = $pdo->prepare($sql);

        if($stmt->execute(array(
            ':idUsuario' => $bindings->idUsuario
        ))){
            while($row = $stmt->fetch(PDO::FETCH_NUM)){
                $data[] = $row;
            }
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
