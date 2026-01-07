

<?php 

include("clients.php"); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de clientes | Tienda Master </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a  class="navbar-brand" href="../../index.php"  >⬅️ Volver al Dashboard</a>
            <span class="navbar-text text-white " >Gestion de clientes</span>
        </div>
    </nav>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">Datos del cliente</h5>


                    </div>
                    <div class="card-body">
                        <form action="" method="post" >
                            <input type="hidden" name="txtID" value="<?php echo $txtID; ?>">

                            <div class="mb-3" >
                                <label class="form-label">Nombre:</label>
                                <input type="text" class="form-control" name="txtFirstName" value="<?php echo $txtFirstName; ?>" required placeholder="Ej: Daniel" >
                                
                            </div>
                            <div class="mb-3" >
                                <label class="form-label">Apellido:</label>
                                <input type="text" class="form-control" name="txtLastName" value="<?php echo $txtLastName; ?>" required placeholder="Ej: Singer" >
                                
                            </div>
                            <div class="mb-3" >
                                <label class="form-label">DNI/Documento:</label>
                                <input type="text" class="form-control" name="txtDocNumber" value="<?php echo $txtDocNumber; ?>" required placeholder="Ej: 956146471" >
                                
                            </div>
                            <div class="mb-3" >
                                <label class="form-label">Correo:</label>
                                <input type="email" class="form-control" name="txtEmail" value="<?php echo $txtEmail; ?>" required placeholder="Ej: daniel@gmail.com" >
                                
                            </div>
                            <div class="d-grid gap-2" >
                                <button type="submit" name="action" value="btnAgregar" class="btn btn-success"  <?php echo $accionAgregar; ?>>Agregar</button>
                                <button type="submit" name="action" value="btnModificar" class="btn btn-warning"  <?php echo $accionModificar; ?>>Modificar</button>
                                <button type="submit" name="action" value="btnCancelar" class="btn btn-secondary"  <?php echo $accionCancelar; ?>>Cancelar</button>    
                            </div>
                        </form>


                    </div>

                </div>

            </div>
            <div class="col-md-8">
                <table class="table table-bordered table-hover bg-white shadow-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Documento</th>
                            <th>Correo</th>
                            <th>Acciones</th>
        
                        </tr>
        
                    </thead>
                    <tbody>
                        <?php foreach($listaClientes as $cliente){ ?>
                            <tr>
                                <td><?php echo $cliente['first_name']." ".$cliente['last_name']; ?></td>
                                <td><?php echo $cliente['doc_number'];?></td>
                                <td><?php echo $cliente['email'];?></td>
                                <td>
                                    <form action="" method="post" >
                                        <input type="hidden" name="txtID" value="<?php echo $cliente['id']; ?>" >
                                        <button value="btnSeleccionar" type="submit" name="action" class="btn btn-info btn-sm" >Editar</button>
                                        <button value="btnEliminar" type="submit" name="action" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar Cliente?');" >Borrar</button>
                                    </form>
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