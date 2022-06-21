<?php 
    require "../../services/connection.php";

    $bindings = [];
    $data=[];
    if($pdo!=null){
        error_log("Connection is not null");
        $bindings[] = file_get_contents('php://input');
        $bindings = json_decode($bindings[0]);
        if($bindings->img == ""){
            $sql = 'UPDATE producto SET nombre = :nombre, costo = :costo, precio = :precio, fkCategoria = :fkCategoria, fkProveedor = :fkProveedor WHERE idProducto = :idProducto;';
            $stmt = $pdo->prepare($sql);

            if($stmt->execute(array(
                ':idProducto' => $bindings->idProducto,
                ':nombre' => $bindings->nombre,
                ':costo' => $bindings->costo,
                ':precio' => $bindings->precio,
                ':fkCategoria' => $bindings->categoria,
                ':fkProveedor' => $bindings->proveedor
            ))){
                $data[] = "Success";
              
            }else{
                $data[] = "Error";
            }
        } else{
            $sql = 'UPDATE producto SET nombre = :nombre, costo = :costo, precio = :precio, imagen = :imagen, fkCategoria = :fkCategoria, fkProveedor = :fkProveedor WHERE idProducto = :idProducto;';
            $stmt = $pdo->prepare($sql);

            if($stmt->execute(array(
                ':idProducto' => $bindings->idProducto,
                ':nombre' => $bindings->nombre,
                ':costo' => $bindings->costo,
                ':precio' => $bindings->precio,
                ':imagen' => $bindings->img,
                ':fkCategoria' => $bindings->categoria,
                ':fkProveedor' => $bindings->proveedor
            ))){
                $data[] = "Success";
              
            }else{
                $data[] = "Error";
            }
            
        }
    }
    else{
        $data[] = "Connection Error";
    }


    echo json_encode($data);
?>
