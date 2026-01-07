<?php 
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../../login.php");
    exit;
}
include("../../config/db.php");
// 2. RECUPERAMOS EL ID DE LA VENTA 
$id_factura = (isset($_GET['id']))?$_GET['id']:"";

if($id_factura == ""){
    header("Location: index.php");
    exit;
}

// 2. CONSULTA CABECERA (Datos de la Venta + Cliente)
$sentenciaSQL = $pdo->prepare("SELECT i.*, c.first_name, c.last_name, c.doc_number, c.email
                                    FROM invoices i
                                    JOIN clients c ON i.client_id = c.id
                                    WHERE i.id = :id");
$sentenciaSQL->bindParam(':id',$id_factura);
$sentenciaSQL->execute();
$factura = $sentenciaSQL->fetch(PDO::FETCH_LAZY);


// 3. CONSULTA DETALLES (Lista de productos comprados)
$sentenciaDetalle = $pdo->prepare("SELECT d.*, p.name
                                        FROM invoice_details d
                                        JOIN products p ON d.product_id = p.id
                                        WHERE d.invoice_id = :id");
$sentenciaDetalle->bindParam(':id', $id_factura);
$sentenciaDetalle->execute();
$listaDetalles = $sentenciaDetalle->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?php echo $id_factura ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ESTILOS PARA IMPRESION */
        @media print{
            .btn-print { display: none;} /* Ocultar boton al imprimir */
            body { -webkit-print-color-adjust: exact;} /* Forzar colores de fondo */
        }
        .invoice-header { background-color: #f8f9fa; border-bottom: 3px solid #0d6efd; padding: 20px 0; }
        .invoice-footer { margin-top: 50px; text-align: center; color: #6c757d; font-size: 0.9em; }
    
    </style>
</head>
<body class="bg-white" >
    <div class="container mt-5" >
        <div class="text-end mb-4 btn-print" >
            <button onclick="window.print()" class="btn btn-primary btn-lg" >
                🖨️ Imprimir / Guardar PDF
            </button>
            <a href="index.php" class="btn btn-secondary btn-lg" >Volver</a>
        </div>
        <div class="invoice-header mb-4" >
            <div class="row align-items-center" >
                <div class="col-6" >
                    <h1 class="text-primary  fw-bold" >TIENDA MASTER</h1>
                    <p class="mb-0" >Tu direccion comercial 123</p>
                    <p class="mb-0" >Lima, Perú</p>
                    <p>RUC: 2039134532</p>

                </div>
                <div class="col-6 text-end" >
                    <h2 class="text-secondary" >FACTURA DE VENTA</h2>
                    <h3 class="fw-bold">Nº 000-<?php echo str_pad($factura['id'],6,"0",STR_PAD_LEFT); ?></h3>
                    <p>Fecha: <?php echo date("d/m/Y h:i:A", strtotime($factura['date'])); ?> </p>

                </div>

            </div>

        </div>
        <div class="card mb-4 border-0" >
            <div class="card-body p-0" >
                <div class="row" >
                    <div class="col-md-6" >
                        <h5 class="txt-primary border-bottom pb-2" >Facturar a:</h5>
                        <h4 class="fw-bold"><?php echo $factura['first_name']." ".$factura['last_name']; ?></h4>
                        <p class="mb-1" ><strong>DNI/RUC:</strong><?php echo $factura['doc_number']; ?></p>
                        <p class="mb-1"><strong>Email:</strong><?php echo $factura['email']; ?></p>
                    </div>

                </div>

            </div>

        </div>
        <table class="table table-striped table-bordered" >
            <thead class="table-dark text-center ">
                <tr>
                   <th>Decripcion</th> 
                   <th width="100px">Cant.</th>
                   <th width="150px">Precio Unit.</th>
                   <th width="200px">Subtotal</th>
                </tr>

            </thead>
            <tbody>
                <?php foreach($listaDetalles as $item){ ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td class="text-center"><?php echo $item['quantity'];?> </td>
                    <td class="text-end" >S/ <?php echo number_format($item['unit_price'],2); ?></td>
                    <td class="text-end" >S/ <?php echo number_format($item['subtotal'],2); ?></td>
                </tr>
                <?php }?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-bold fs-4" >TOTAL A PAGAR:</td>
                    <td class="text-end fw-bold fs-4 bg-light text-primary" >
                        S/ <?php echo number_format($factura['total'],2); ?>
                    </td>
                </tr>
            </tfoot>

        </table>
        <div class="invoice-footer">
            <hr>
            <p>Gracias por su compra. ¡Vuelva pronto!</p>
            <small>Este documento es un comprobante electronico generado por el sistema Tienda Master</small>

        </div>
    </div>

    
</body>
</html>

