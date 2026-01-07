<?php

include("products.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda-Master-Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4" >
        <div class="container" >
            <a href="../../index.php" class="navbar-brand" >⬅️ Volver al Dashboard</a>
            <span class="navbar-text text-white">📦 Gestión de Inventario</span>
        </div>

    </nav>

    <div class="container" >
        <div class="row" >
            <div class="col-md-4" >
                <div class="card shadow-sm mb-4" >

                    <div class="card-header bg-success text-white" >
                        <h5 class="card-title mb-0 " >Datos del producto</h5>
                    </div>
                    <div class="card-body" >
                        <form action="" method="post" enctype="multipart/form-data">
    
                            <input type="hidden" name="txtID" value="<?php echo $txtID;?>" id="">
    
                            <div class="mb-3" >
                                <label class="form-label">Nombre:</label>
                                <input type="text" class="form-control" name="txtName" value="<?php echo $txtName; ?>" required placeholder="Cocacola" >
                            </div>
                            <div class="row" >
                                <div class="col-6 mb-3" >
                                    <label class="form-label">Precio (S/):</label>
                                    <input type="number" step="0.01" class="form-control" name="txtPrice" value="<?php echo $txtPrice;?>" required placeholder="0.00" >
        
                                </div>
                                <div class="col-6 mb-3" >
                                    <label class="form-label">Stock:</label>
                                    <input type="number" class="form-control" name="txtStock" value="<?php echo $txtStock;?>" required placeholder="0" >
        
                                </div>
                            </div>
                            <div class="mb-3" >
                                <label class="form-label">Categoria:</label>
                                <input type="text" class="form-control" name="txtCategory" value="<?php echo $txtCategory;?>" required placeholder="Bebidas" >
    
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Foto del Producto:</label>
                                <input type="file" class="form-control" name="txtImage">
                                <?php if(isset($producto['image']) && ($producto['image']!="imagen.jpg")){ ?>
                                    <div class="mt-2 text-center">
                                        <small>Imagen actual:</small><br>
                                        <img src="../../assets/img/<?php echo $producto['image']; ?>" class="img-thumbnail" width="100">
                                    </div>
                                <?php } ?>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="action" value="btnAgregar" class="btn btn-success" <?php echo $accionAgregar; ?>>Agregar</button>
                                <button type="submit" name="action" value="btnModificar" class="btn btn-warning" <?php echo $accionModificar; ?>>Modificar</button>
                                <button type="submit" name="action" value="btnCancelar" class="btn btn-secondary" <?php echo $accionCancelar; ?>>Cancelar</button>
                            </div>
    
                            
                        </form>
    
    
                    </div>
                </div>
            </div>

            <div class="col-md-8" >
                <div class="card shadow-sm" >
                    <div class="card-body p-0">
                        <table class="table table-bordered table-hover align-middle mb-0" >
                            <thead class="table-dark" >
                                <thead class="table-dark">
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            
                            <tbody>
                                <?php foreach($listaProductos as $producto){ ?>
                                        <tr>
                                            <td class="text-center" style="width: 100px;">
                                                <img class="img-thumbnail rounded" style="max-height: 80px;" 
                                                     src="../../assets/img/<?php echo $producto['image']; ?>" 
                                                     alt="Foto">
                                            </td>
                                            
                                            <td>
                                                <strong><?php echo $producto['name']; ?></strong><br>
                                                <span class="badge bg-secondary"><?php echo $producto['category']; ?></span>
                                            </td>
                                            
                                            <td class="fw-bold text-success">S/ <?php echo $producto['price']; ?></td>
                                            
                                            <td class="<?php echo ($producto['stock'] < 5) ? 'text-danger fw-bold' : ''; ?>">
                                                <?php echo $producto['stock']; ?> un.
                                                <?php if($producto['stock'] < 5) echo '⚠️'; ?>
                                            </td>
    
                                            <td class="text-center">
                                                <form action="" method="post">
                                                    <input type="hidden" name="txtID" value="<?php echo $producto['id']; ?>">
                                                    
                                                    <button value="btnSeleccionar" type="submit" name="action" class="btn btn-info btn-sm mb-1" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    
                                                    <button value="btnEliminar" type="submit" name="action" class="btn btn-danger btn-sm mb-1" onclick="return confirm('¿Seguro que deseas borrar este producto?');" title="Borrar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php } ?>
                            </tbody>
    
                        </table>
    
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    
</body>
</html>