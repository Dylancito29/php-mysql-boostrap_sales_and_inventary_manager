<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../../login.php");
    exit;
}
include("../../config/db.php");

// CONSULTA AVANZADA (JOIN):
// Traemos todo de la factura (i.*) y pegamos el nombre/apellido del cliente (c...)
// Ordenamos por fecha descendiente (lo mas nuevo arriba)
$sentenciaSQL = $pdo->prepare("SELECT i.*, c.first_name, c.last_name FROM invoices i JOIN clients c ON i.client_id = c.id ORDER BY i.date DESC");
$sentenciaSQL->execute();

$listaVentas = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Ventas | Tienda Master</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


</head>
<body class="bg-light" >
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 " >
        <div class="container" >
            <a class="navbar-brand"  href="../../index.php"  >📊 Historial de Ventas 📊 </a>
            <div class="d-flex" >
                <a href="create.php" class="btn btn-success" >
                    <i class="bi bi-plus-circle"></i> Nueva Venta

                </a>
            </div>

        </div>

    </nav>
    <div class="container" >
        <div class="card shadow-sm" >
            <div class="card-body" >
                <h4 class="card-title mb-4" > Ultimas Ventas Registradas </h4>
                <table class="table table-hover table-bordered  align-middle" >
                    <thead class="table-dark" >
                        <tr>
                            <th># ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach($listaVentas as $venta){ ?>
                        <tr>
                            <td> <?php echo $venta['id']; ?> </td>
                            <td> <?php echo date("d/m/Y h:i A", strtotime($venta['date'])); ?> </td>
                            <td> <?php echo $venta['first_name']." ".$venta['last_name']; ?> </td>
                            <td class="fw-bold text-success" >S/ <?php echo number_format($venta['total'],2) ?> </td>
                            <td>
                                <?php if($venta['status']=='pending'){ ?>
                                    <span class="badge bg-warning text dark" >Pendiente</span>
                                <?php }else{ ?>
                                    <span class="badge bg-success" >Pagado</span>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <a href="report.php?id=<?php echo $venta['id']; ?>" class="btn btn-primary btn-sm" target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i> Ver Factura
                                </a>

                            </td>
                        </tr>
                        <?php } ?>

                        

                    </tbody>
                </table>

            </div>

        </div>

    </div>
    
</body>
</html>