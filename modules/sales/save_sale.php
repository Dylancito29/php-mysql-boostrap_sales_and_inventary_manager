<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../../login.php");
    exit;
}

// modules/sales/save_sale.php
include("../../config/db.php");

if($_POST){
    // 1. RECIBIR DATOS DEL FORMULARIO
    $id_cliente = $_POST['selectCliente'];

    // Estos llegan como ARRAYS (listas) porque pusimos [] en los names del HTML 
    $productos_ids = $_POST['productos_id'];
    $cantidades = $_POST['cantidades'];
    $precios = $_POST['precios_unitarios'];

    $total_venta = 0;

    try {
        // --- INICIO DE TRANSACCION (Modo seguro) ---
        // Esto le dice a la base de datos. "O guardas TODO, o no guardes NADA"
        $pdo->beginTransaction();
        
        // 2. CALCULAR TOTAL REAL (Sumamos todo antes de guardar la cabecera)
        for($i = 0; $i < count($productos_ids); $i++){
            $total_venta += ($precios[$i]* $cantidades[$i]);

        }

        // 3. INSERTAR LA CABECERA (La factura en si)
        $fecha = date("Y-m-d H:i:s"); // Año-mes-dia Hora:min:seg

        // El status por defecto para 'Pending' segun la base de datos
        $sentenciaSQL = $pdo->prepare("INSERT INTO invoices (client_id,total, date, status ) VALUES(:client_id, :total, :date, 'pending') ");
        $sentenciaSQL->bindParam(':client_id', $id_cliente);
        $sentenciaSQL->bindParam(':total', $total_venta);
        $sentenciaSQL->bindParam(':date',$fecha);
        $sentenciaSQL->execute();


        // 4. OBTENER EL ID DE LA FACTURA RECIEN CREADA
        // Este comando magico nos da el ID (ej: 1,2,50) que la base de datos le asigno
        $id_factura = $pdo->lastInsertId();

        // 5.INSERTAR LOS DETALLES Y ACTUALIZAR STOCK (Bucle)
        // Recorremos la lisra de productos uno por uno
        // 5. INSERTAR DETALLES Y ACTUALIZAR STOCK
        for($i = 0; $i < count($productos_ids); $i++){
            $producto_id = $productos_ids[$i];
            $cantidad = $cantidades[$i];
            $precio = $precios[$i];
            $subtotal = $precio * $cantidad;

            // A) Guardar el detalle de la venta
            $sentenciaDetalle = $pdo->prepare("INSERT INTO invoice_details (invoice_id, product_id, quantity, unit_price, subtotal)  
            VALUES(:invoice_id, :product_id, :quantity, :unit_price, :subtotal)");

            $sentenciaDetalle->bindParam(':invoice_id', $id_factura);
            $sentenciaDetalle->bindParam(':product_id', $producto_id);
            $sentenciaDetalle->bindParam(':quantity', $cantidad);
            $sentenciaDetalle->bindParam(':unit_price', $precio);
            $sentenciaDetalle->bindParam(':subtotal', $subtotal);
            $sentenciaDetalle->execute();

            // B) DESCONTAR STOCK (¡Esto faltaba!) 📉
            // Le decimos a la base de datos: "Al stock actual, réstale la cantidad vendida"
            $sentenciaStock = $pdo->prepare("UPDATE products SET stock = stock - :cantidad WHERE id = :id");
            $sentenciaStock->bindParam(':cantidad', $cantidad);
            $sentenciaStock->bindParam(':id', $producto_id);
            $sentenciaStock->execute();
        }
            //--- Confirmar la transaccion ---
            // Si llegamos aqui sin errores, guardamos todo permanentemente
            $pdo->commit();

            // Redirigimos a ala lista de ventas (que haremos en el siguiente paso)
            header("Location: index.php");

         
        }
        catch (Exception $e) {
            // --- ERROR ---
            // Si algo fallo, deshaceos todos los cambios (Rollback) para no dejar datos corruptos
            $pdo->rollBack();
            echo "<h1> Error fatal al registrar la venta: </h1>";
            echo $e->getMessage();
            echo "<br><a href='create.php'>Volver a intentar</a>";
        

    }


}




?>