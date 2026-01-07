<?php
session_start();
include("config/db.php");

// --- EL CANDADO DE SEGURIDAD ---
// Si no existe la variable de sesion 'usuario', lo mandamos al login
if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit; // Importatante para detener el codigo aqui
}

// 2. CONSULTAS DE ESTADISTICAS (KPI'S)

// A. Conteo de productos
$sqlProd= $pdo->prepare("SELECT count(*) as total FROM products");
$sqlProd->execute();
$totalProductos = $sqlProd->fetch(PDO::FETCH_ASSOC)['total'];


// B. Contar Clientes
$sqlClients = $pdo->prepare("SELECT count(*) as total FROM clients");
$sqlClients->execute();
$totalClientes = $sqlClients->fetch(PDO::FETCH_ASSOC)['total'];


// C. Sumar Ventas (Total de dinero historico)
$sqlVentas = $pdo->prepare("SELECT SUM(total) as total FROM invoices");
$sqlVentas->execute();
$rowVentas = $sqlVentas->fetch(PDO::FETCH_ASSOC);

$totalVentas = $rowVentas['total']?$rowVentas['total']:0; //Si es null, pon 0



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Tienda Master</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light" >
    <nav class="navbar navbar-expand-lg  navbar-dark bg-dark mb-4" >
        <div class="container" >
            <a href="#" class="navbar-brand" >🚀Tienda Master</a>
            <div class="d-flex text-white align-items-center" >
                <span class="me-3">Hola, <strong><?php echo $_SESSION['usuario'];?></strong></span>
                <a href="cerrar.php" class="btn btn-outline-danger btn-sm">Cerrar Session</a>
            </div>
        </div>
    </nav>

    <div class="container" >
        <div class="row text-center" >
            <div class="col-12 mb-4" >
                <h1 class="display-4" >Resumen del negocio</h1>
                <p class="lead" >Estadisticas en tiempo real.</p>
            </div>
        </div>

        <div class="row justify-content-center" >

            <div class="col-md-4 mb-4" >
                <div class="card h-100 shadow-sm border-primary" >
                    <div class="card-body text-center" >
                        <h1 class="display-1 text-primary"><i class="bi bi-currency-dollar"></i></h1>
                        <h2 class="fw-bold" >S/ <?php echo number_format($totalVentas,2); ?></h2>
                        <p class="text-muted">Ingresos Totales</p>
                        
                        <a href="modules/sales/index.php" class="btn btn-primary w-100"> Ir a Ventas</a>

                    </div>

                </div>

            </div>

            <div class="col-md-4 mb-4" >
                <div class="card h-100 shadow-sm border-success" >
                    <div class="card-body text-center" >
                        <h1 class="display-1 text-success" ><i class="bi bi-box-seam" ></i></h1>
                        <h2 class="fw-bold"><?php echo $totalProductos; ?> </h2>
                        <p class="text-muted">Productos en catalogo</p>

                        <a href="modules/products/index.php" class="btn btn-success w-100">Inventario</a>

                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-warning" >
                    <div class="card-body text-center" >
                        <h1 class="display-1 text-warning"><i class="bi bi-people"></i></h1>
                        <h2 class="fw-bold"><?php echo $totalClientes; ?> </h2>
                        <p class="text-muted">Clientes registrados</p>

                        <a href="modules/clients/index.php" class="btn btn-warning w-100">Base de Clientes</a>

                    </div>  
                </div>
            </div>

        </div>
            
    </div>
    
</body>
</html>