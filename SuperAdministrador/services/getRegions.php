<?php 
    require "../../services/connection.php";
    $bindings = [];
    $data=[];
    if($pdo!=null){
        error_log("Connection is not null");
        $bindings[] = file_get_contents('php://input');
        $sql = 'SELECT idRegion, iva, nombre FROM region_iva JOIN ciudad c on region_iva.fkCiudad = c.idCiudad;';
        $stmt = $pdo->prepare($sql);
        if($stmt->execute($bindings)){
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
