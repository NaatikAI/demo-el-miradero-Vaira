<?php 
// ? LOCAL STORAGE
    $servername = "localhost";
    $username = "naatika1_VARIA_150622";
    //$password = "root";
    $password = "Ekq3l#pE6qYr";
    $dbname = "naatika1_db_vaira";


    try {
        $pdo = new PDO('mysql:host='.$servername.';dbname='.$dbname, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        // set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
        $pdo = null;
    }     
?>
