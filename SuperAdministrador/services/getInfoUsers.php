<?php 
    require "../../services/connection.php";

    $bindings = [];
    $data=[];
    if($pdo!=null){
        error_log("Connection is not null");
        $bindings[] = file_get_contents('php://input');
        $sql = 'SELECT idUsuario, nombre, apellidoP, apellidoM, usuario, tipo, correo, telefono FROM usuario JOIN tipo t on usuario.fkTipo = t.idTipo WHERE fkTipo != 3 AND activo = 1;';
        $stmt = $pdo->prepare($sql);

        if($stmt->execute()){
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
