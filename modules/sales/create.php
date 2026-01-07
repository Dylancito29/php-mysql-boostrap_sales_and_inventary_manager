<?php 
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../../login.php");
    exit;
}
include("../../config/db.php");

// 1. OBTENER LA LISTA DE CLIENTES (para el select)
$sentenciaSQL = $pdo->prepare("SELECT * FROM clients");
$sentenciaSQL->execute();

$listaClientes = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

// 2. OBTENER LISTA DE PRODUCTOS (para buscarlos)
$sentenciaSQL = $pdo->prepare("SELECT * FROM products WHERE stock > 0"); // Solo los que tienen stock
$sentenciaSQL->execute();
$listaProductos = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);


// 3. GENERAR FECHA DE HOY
$fechaHoy = date('Y-m-d H:i');
?>

<!DOCTYPE html>
<html lang="en">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta | Tienda Master </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4 " >
        <div class="container" >
            <a class="navbar-brand" href="../../index.php" ></a>
        </div>
    </nav>
    <div class="container" >
        <form action="save_sale.php" method="post" >
            <div class="row" >
                <div class="col-md-4" >
                    <div class="card shadow-sm mb-3" >
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0 " ><i class="bi bi-person-badge" ></i>Datos del cliente: </h5>
                        </div>
                        <div class="card-body" >
                            <div class="mb-3" >
                                <label class="form-label">Fecha:</label>
                                <input type="text" class="form-control" value="<?php echo $fechaHoy; ?>" readonly>

                            </div>
                            <div class="mb-3" >
                                <label class="form-label" >Seleccionar cliente:</label>
                                <select name="selectCliente" class="form-select" required>
                                    <option value="">-- Elige un cliente --</option>
                                    <?php foreach($listaClientes as $cliente){ ?>
                                        <option value="<?php echo $cliente['id']; ?>" >
                                            <?php echo $cliente['first_name']." ".$cliente['last_name']; ?>
                                            (DNI: <?php echo $cliente['doc_number']; ?>)
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <hr>
                            <div class="d-grid" >
                                <button type="submit" class="btn btn-success btn-lg" >
                                    <i class="bi bi-currency-dollar" ></i> Finalizar Venta

                                </button>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-8" >
                    <div class="card shadow-sm" >
                        <div class="card-header bg-secondary text-white d-flex justify-content-between">
                            <h5 class="card-title mb-0" ><i class="bi bi-cart3" ></i>Carrito de Productos</h5>
                        </div>
                        <div class="card-body" >
                            <div class="row mb-3 align-items-end">
                                <div class="col-8" >
                                    <label for="">Producto:</label>
                                    <select name="" id="selectProducto" class="form-select" >
                                        <option value="">-- Buscar Producto --</option>
                                        <?php foreach($listaProductos as $producto){ ?>
                                            <option value="<?php echo $producto['id']; ?>"
                                            data-precio="<?php echo $producto['price']; ?>"
                                            data-nombre="<?php echo $producto['name']; ?>">
                                            <?php echo $producto['name']; ?> - S/ <?php echo $producto['price']; ?>
                                            </option>
                                            <?php } ?>
                                    </select>
                                </div>
                                <div class="col-2" >
                                    <label for="">Cant:</label>
                                    <input type="number" id="txtCantidad" class="form-control" value="1" min="1">


                                </div>
                                <div class="col-2" >
                                    <button type="button" class="btn btn-primary w-100" onclick="agregarAlCarrito()" >+</button>
                                </div>
                            </div>
                            <table class="table table-bordered table-striped " >
                                <thead class="table-dark" >
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cant.</th>
                                        <th>Precio Unit</th>
                                        <th>Subtotal</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaCarrito" >
                                    <tr>
                                        <td colspan="5" class="text-center text-muted" >El carrito esta vacio</td>
                                    </tr>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold" >TOTAL A PAGAR:</td>
                                        <td colspan="2" class="fw-bold fs-5" >S/ <span id="txtTotal">0.00</span></td>
                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                    </div>

                </div>

            </div>
        </form>
    </div>
    <script>
        // Variable global para llevar la suma
        let totalVenta = 0.00;

        function agregarAlCarrito(){
            // 1. OBTENER ELEMENTOS DEL DOM
            const select = document.getElementById('selectProducto');
            const cantidadInput = document.getElementById('txtCantidad');
            const tabla = document.getElementById('tablaCarrito');
            const totalDisplay = document.getElementById('txtTotal');

            // 2. VALIDACIONES BASICAS 
            if(select.value===""){
                alert("⚠️ Por favor, selecciona un producto");
                return;
            }
            if(cantidadInput.value <1){
                alert("⚠️ La cantidad debe ser al menos 1.");
                return;

            }

            // 3. CAPTURAR DATOS DEL PRODUCTO  (usando los atributos data- que pusimos en PHP)
            const selectedOption = select.options[select.selectedIndex];
            const id = select.value;
            const nombre = selectedOption.getAttribute('data-nombre');
            const precio = parseFloat(selectedOption.getAttribute('data-precio'));
            const cantidad = parseInt(cantidadInput.value);

            // 4. CALCULOS MATEMATICOS
            const subtotal = precio * cantidad;
            totalVenta += subtotal;

            // 5. LIMPIAR LA FILA DE "VACIO" (si es el primer producto)
            // Si la tabla tiene texto "vacio", lo borramos
            if(tabla.rows.length > 0 && tabla.rows[0].cells.length===1){
                tabla.innerHTML="";
            }

            // 6. CREAR LA FILA HTML Y LOS INPUTS OCULTOS
            // Insertsamos una nueva fila(tr)
            const row = tabla.insertRow();

            row.innerHTML=`
                <td>
                    ${nombre}
                    <input type="hidden" name="productos_id[]" value="${id}">
                </td>
                <td>
                    ${cantidad}
                    <input type="hidden" name="cantidades[]" value="${cantidad}">
                    <input type="hidden" name="precios_unitarios[]" value="${precio}">
                </td>
                <td>S/ ${precio.toFixed(2)}</td>
                <td>S/ ${subtotal.toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this, ${subtotal})">
                        <i class="bi bi-trash"></i> X
                    </button>
                </td>
            `;
            // 7. ACTUALIZAR EL TOTAL EN LA PANTALLA
            totalDisplay.innerText = totalVenta.toFixed(2);

            // 8. RESETEAR EL FORMULARIO
            select.value = "";
            cantidadInput.value=1;
            select.focus(); // Volver el cursor al select para seguir agregando rapido
        }
        function eliminarFila(btn, subtotalRestar){
            //Borrar la fila visualmente
            const row = btn.parentNode.parentNode;
            row.remove();

            // Restar del totalDisplay
            totalVenta -= subtotalRestar;
            document.getElementById('txtTotal').innerText = totalVenta.toFixed(2);

        }

    </script>
    
</body>
</html>
