<?php 
    require "../../services/connection.php";

    $bindings = [];
    $data=[];
    if($pdo!=null){
        error_log("Connection is not null");
        $bindings[] = file_get_contents('php://input');
        $sql = 'SELECT idUsuario, usuario.nombre, correo, usuario.telefono, usuario, s.nombre, tipo FROM usuario
                    LEFT JOIN sucursal_usuario ON fkUsuario = idUsuario
                    LEFT JOIN sucursal s ON usuario.idUsuario = s.fkAdmin
                    JOIN tipo t on usuario.fkTipo = t.idTipo
                    WHERE fkTipo !=3 AND activo = 1 
                    ORDER BY fkTipo;';
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
