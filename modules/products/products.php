<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../../login.php");
    exit;
}
// ---------------------------------------------------
// PASO 1: CONEXIÓN (La tubería a la base de datos)
// ---------------------------------------------------
// Salimos de "products" (..) -> salimos de "modules" (..) -> entramos a "config"

include("../../config/db.php");

// ---------------------------------------------------
// PASO 2: RECEPCIÓN DE DATOS (Las variables)
// ---------------------------------------------------
// Aquí atrapamos lo que el formulario nos lanza.
// Usamos el operador ternario: (¿Existe?) ? Tómalo : Déjalo vacío.

$txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
$txtName=(isset($_POST['txtName']))?$_POST['txtName']:"";
$txtPrice=(isset($_POST['txtPrice']))?$_POST['txtPrice']:"";
$txtStock=(isset($_POST['txtStock']))?$_POST['txtStock']:"";
$txtCategory=(isset($_POST['txtCategory']))?$_POST['txtCategory']:"";


// Las imagenes no viajan por POST , viajan por FILES
$txtImage = (isset($_FILES['txtImage']['name']))?$_FILES['txtImage']['name']:"";


$action = (isset($_POST['action']))?$_POST['action']:"";


// ---------------------------------------------------
// SECCIÓN: EL "CHISMOSO" (LOGS VISUALES)
// ---------------------------------------------------
// Esto imprimirá un cuadro amarillo en tu pantalla SOLO si presionas un botón.
// Así sabrás exactamente qué datos está recibiendo tu código.
/*
if($action != ""){
    echo "<div style='background-color: #ffeb3b; padding: 15px; border: 2px solid #000; margin: 20px; font-family: monospace;'>";
    echo "<h3>🕵️ LOG DEL SISTEMA (Datos Recibidos):</h3>";
    echo "<strong>Acción:</strong> " . $action . "<br>";
    echo "<strong>ID:</strong> " . $txtID . "<br>";
    echo "<strong>Nombre:</strong> " . $txtName . "<br>";
    echo "<strong>Precio:</strong> " . $txtPrice . "<br>";
    echo "<strong>Stock:</strong> " . $txtStock . "<br>";
    echo "<strong>Categoría:</strong> " . $txtCategory . "<br>";
    echo "<strong>Nombre Imagen:</strong> " . $txtImage . "<br>";
    echo "</div>";
};
*/
$accionAgregar = "";
$accionModificar = $accionEliminar = $accionCancelar = "disabled";
$mostrarModal= false;
$error = [];

switch($action){
    case "btnAgregar":
        // 1. Validaciones basicas
        
        if($txtName==""){
            $error['Nombre']="Ingrese el nombre";

        }
        if($txtPrice==""){
            $error['Price']="Ingrese el precio";

        }
        
        if(count($error)>0){
            $mostrarModal = true;
            break;
        }
        
        // 2. Preparamos la secuencia SQL
        $sentenciaSQL = $pdo->prepare("INSERT INTO products(name,category,price,stock,image)VALUES(:name,:category,:price,:stock,:image)");

        // 3. Vinculamos los parametros de la consulta SQL con una variable PHP.
        // Vincula los parametros de la consulta SQL con las variales PHP correspondientes

        $sentenciaSQL->bindParam(':name', $txtName);
        $sentenciaSQL->bindParam(':category', $txtCategory);
        $sentenciaSQL->bindParam(':price', $txtPrice);
        $sentenciaSQL->bindParam(':stock', $txtStock);

        // 4. Logica de la IMAGEN (Magic Timestamp)
        $fecha = new DateTime();

        // Si subieron Image, usamos timestamp_nombre.jpg, si no, usamos la default
        $nombreArchivo = ($txtImage !="")?$fecha->getTimestamp()."_".$_FILES["txtImage"]["name"]:"imagen.jpg";

        $tmpImage=$_FILES["txtImage"]["tmp_name"];


        if($tmpImage !=""){
            move_uploaded_file($tmpImage, "../../assets/img/".$nombreArchivo);
        }

        $sentenciaSQL->bindParam(':image',$nombreArchivo);
        // 5. Ejecutamos y guardamos
        $sentenciaSQL->execute();

        header('Location: index.php');

    break;
    case 'btnModificar':
        $sentenciaSQL=$pdo->prepare("UPDATE products SET 
        name=:name,
        category=:category,
        price=:price,
        stock=:stock
         WHERE id=:id
        ");

        $sentenciaSQL->bindParam(':name',$txtName);
        $sentenciaSQL->bindParam(':category', $txtCategory);
        $sentenciaSQL->bindParam(':price',$txtPrice);
        $sentenciaSQL->bindParam(':stock',$txtStock);
        $sentenciaSQL->bindParam(':id',$txtID);

        $sentenciaSQL->execute();

        $fecha = new DateTime();

        $nombreArchivo = ($txtImage!="")? $fecha->getTimestamp()."_".$_FILES['txtImage']['name']:"imagen.jpg";

        $tmpImage = $_FILES['txtImage']['tmp_name'];

        if($tmpImage!=""){
            move_uploaded_file($tmpImage, "../../assets/img/".$nombreArchivo);

            $sentenciaSQL=$pdo->prepare("SELECT image FROM products WHERE id=:id");
            $sentenciaSQL->bindParam(':id', $txtID);
            $sentenciaSQL->execute();


            $producto = $sentenciaSQL->fetch(PDO::FETCH_LAZY);

            if(isset($producto['image'])&&($producto['image']!="image.jpg")){
                if(file_exists("../../assets/img/".$producto['image'])){
                    unlink("../../assets/img/".$producto['image']);
                }
            }

            $sentenciaSQL=$pdo->prepare("UPDATE products SET 
            image=:image WHERE id=:id");
            $sentenciaSQL->bindParam(':image',$nombreArchivo);
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();

        }

        header('Location: index.php');
        break;

     
    

    // Aqui iran los otros casos (Modificar, Eliminar) mas adelante...
    case "btnEliminar":
        // 1. Buscamos la imagen antes de borrar el registro (para eliminarla de la carpeta)
        $sentenciaSQL=$pdo->prepare("SELECT image FROM products WHERE id=:id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        $producto=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
        

        if(isset($producto['image'])&& ($producto['image']!="imagen.jpg")){
            if(file_exists("../../assets/img/".$producto['image'])){
                unlink("../../assets/img/".$producto['image']);
            }
        }

        $sentenciaSQL=$pdo->prepare("DELETE FROM products WHERE id=:id");

        $sentenciaSQL->bindParam(':id', $txtID);

        $sentenciaSQL->execute();

        header('Location: index.php');
        break;


    case'btnCancelar':
        header('Location: index.php');
        break;
        
    case 'btnSeleccionar':
        $accionAgregar = "disabled";
        $accionModificar = $accionEliminar = $accionCancelar = "";
        $mostrarModal= true;

        $sentenciaSQL=$pdo->prepare("SELECT * FROM products WHERE id=:id");
        $sentenciaSQL->bindParam(":id", $txtID);
        $sentenciaSQL->execute();

        $producto=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

        $txtName=$producto['name'];
        $txtCategory=$producto['category'];
        $txtPrice=$producto['price'];
        $txtStock=$producto['stock'];

    break;

    





}
// ---------------------------------------------------
// PASO FINAL: LEER LOS PRODUCTOS (SELECT)
// ---------------------------------------------------
// Esto llenará la tabla que haremos en el HTML

$sentenciaSQL = $pdo->prepare("SELECT * FROM products");
$sentenciaSQL->execute();
$listaProductos = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

?>