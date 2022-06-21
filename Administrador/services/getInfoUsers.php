<?php 
    require "../../services/connection.php";

    $bindings = [];
    $data=[];
    if($pdo!=null){
        error_log("Connection is not null");
        $bindings[] = file_get_contents('php://input');
        $bindings = json_decode($bindings[0]);
        $sql = 'SELECT idUsuario, nombre, apellidoP, apellidoM, usuario, tipo, correo, telefono FROM usuario JOIN sucursal_usuario ON idUsuario = fkUsuario JOIN tipo t on usuario.fkTipo = t.idTipo WHERE fkSucursal = :sucursal AND fkTipo = 3;';
        $stmt = $pdo->prepare($sql);

        if($stmt->execute(array(
            ':sucursal' => $bindings->sucursal
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
