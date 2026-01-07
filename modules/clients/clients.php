<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../../login.php");
    exit;
}
include("../../config/db.php");

// 1. RECEPCION DE DATOS
// Recibimos los datos del formulario (si existen), sino los dejamos vacios

$txtID = (isset($_POST['txtID']))?$_POST['txtID']:"";
$txtFirstName = (isset($_POST['txtFirstName']))?$_POST['txtFirstName']:"";
$txtLastName = (isset($_POST['txtLastName']))?$_POST['txtLastName']:"";
$txtEmail= (isset($_POST['txtEmail']))?$_POST['txtEmail']:"";
$txtDocNumber = (isset($_POST['txtDocNumber']))?$_POST['txtDocNumber']:"";


$action = (isset($_POST['action']))?$_POST['action']:"";

// Variables para controlar los botones
$accionAgregar = "";
$accionModificar=$accionEliminar = $accionCancelar = "disabled";
$mostrarModal=false;

// 2. Logica de botones (CRUD)
switch($action){
    case "btnAgregar":
        // Sentencia SQL para INSERTAR
        // Fijate que aqui no hay imagen, es mas simple
        $sentenciaSQL = $pdo->prepare("INSERT INTO clients (first_name, last_name,email,doc_number)
         VALUES (:first_name,:last_name ,:email,:doc_number)");

        $sentenciaSQL->bindParam(':first_name',$txtFirstName);
        $sentenciaSQL->bindParam(':last_name',$txtLastName);
        $sentenciaSQL->bindParam(':email',$txtEmail);
        $sentenciaSQL->bindParam(':doc_number',$txtDocNumber);

        $sentenciaSQL->execute();
        header('Location: index.php');

        break;
    case "btnModificar":
        $sentenciaSQL=$pdo->prepare("UPDATE clients SET 
        first_name = :first_name,
        last_name=:last_name,
        email=:email,
        doc_number=:doc_number
        WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->bindParam(':first_name', $txtFirstName);
        $sentenciaSQL->bindParam(':last_name',$txtLastName);
        $sentenciaSQL->bindParam(':email',$txtEmail);
        $sentenciaSQL->bindParam(':doc_number',$txtDocNumber);

        $sentenciaSQL->execute();

        header('Location: index.php');
        break;

    case "btnEliminar":
        //Sentencia SQL para Borrar
        $sentenciaSQL=$pdo->prepare("DELETE FROM clients WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();

        header('Location: index.php');
        break;
    case "btnCancelar":
        header('Location: index.php');
    case "btnSeleccionar":
        $accionAgregar="disabled";
        $accionModificar=$accionCancelar=$accionEliminar = "";
        $mostrarModal =true;

        $sentenciaSQL = $pdo->prepare("SELECT * FROM clients WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();
        $cliente = $sentenciaSQL->fetch(PDO::FETCH_LAZY);

        $txtFirstName = $cliente['first_name'];
        $txtLastName = $cliente['last_name'];
        $txtEmail = $cliente['email'];
        $txtDocNumber = $cliente['doc_number'];

        break;
    

}

// 3. LEER LA LISTA COMPLETA (para la tabla)
$sentenciaSQL = $pdo->prepare("SELECT * FROM clients");
$sentenciaSQL->execute();
$listaClientes = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

?>