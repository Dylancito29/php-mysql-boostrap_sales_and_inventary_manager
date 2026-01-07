<?php
$server="mysql:dbname=tienda_master;host=127.0.0.1";
$user="root";
$password="Dylan072912.";


try{
    $pdo= new PDO($server, $user,$password, array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
    //echo "conected...";
}catch(PDOException $e){
    echo "Something wrong with the conection :( " .$e->getMessage();
    die();
}