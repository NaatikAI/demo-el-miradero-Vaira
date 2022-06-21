<?php 
session_start();
include 'services/connection.php';
$go_to_super_admin="Location: ./SuperAdministrador/index.php?recibos=true";
$go_to_administrador="Location: ./Administrador/index.php?recibos=true";
$go_to_vendedor="Location: ./Vendedor/index.php?nueva_venta=true";
// var_dump($pdo);

if(isset($_SESSION['userType'])){
    switch($_SESSION['userType']) {
        case 1:
            header($go_to_super_admin);
            break;
        case 2:
            header($go_to_administrador);
            break;
        case 3:
            header($go_to_vendedor);
            break;
    }
}

$errores = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // * GET data from form and store in variables
    $usuario = filter_var( $_POST['user'] , FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $sql = 'SELECT tipo.idTipo, usuario.idUsuario, usuario.usuario FROM usuario INNER JOIN tipo WHERE usuario.fkTipo = tipo.idTipo AND usuario = ? AND password = SHA2(?,512) AND activo = 1;';
    $bindings = [];
    $bindings[] = $usuario;
    $bindings[] = $password;
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($bindings);

    $result = $stmt->fetch();

    if($result !== false){
        $_SESSION['userType'] = $result['idTipo'];
        $_SESSION['user'] = $result['idUsuario'];
        $_SESSION['userName'] = $result['usuario'];

        // User types:
        // 1.- Super Admin
        // 2.- Admin
        // 3.- User
        switch($_SESSION['userType']) {
            case 1:
                header($go_to_super_admin);
                break;
            case 2:
                header($go_to_administrador);
                break;
            case 3:
                header($go_to_vendedor);
                break;
        }
    } else {
        $statement = $pdo->prepare(
            'SELECT * FROM usuario WHERE usuario = :usuario AND activo = 1'
        );
        $statement->execute(array(
            ':usuario' => $usuario
        ));
        $result = $statement->fetch();
        
        if($result !== false)
            $errores .= '<li>Usuario y/o contrase&ntilde;a incorrecta</li>';
        else
            $errores .= '<li>El usuario no existe</li>';
    }

}

?>