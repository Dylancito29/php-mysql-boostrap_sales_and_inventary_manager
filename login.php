<?php

session_start();
include("config/db.php");

$mensaje = "";


if($_POST){
    $email = $_POST['txtEmail'];
    $password = $_POST['txtPassword'];

    //Buscamos al usuario por su email
    $sentencia = $pdo->prepare("SELECT * FROM users WHERE email=:email ");
    $sentencia->bindParam(':email',$email);
    $sentencia->execute();
    $usuario = $sentencia->fetch(PDO::FETCH_LAZY);
    // Verificamos si existe y si la contraseña coincide
    if(isset($usuario['email'])){
        // COMPARACION DIRECTA (Solo para responder, luego usaremos encriptacion)
        if(password_verify($password,$usuario['password'])){

            // ¡Login Exitoso!
            $_SESSION['usuario'] = $usuario['name'];
            $_SESSION['estado'] = "conectado";
            header("Location: index.php"); // Lo mandamos al dashboard


        }else{
            $mensaje = "Error: Contraseña incorrecta.";
        }
    }else{
        $mensaje = "Error: Usuario no encontrado.";
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Tienda Master</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card{
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="login-card text-center" >
        <h3 class="mb-4" >🔐 Iniciar Sesión</h3>
        <?php if($mensaje != "" ) {?>
            <div class="alert alert-danger"> <?php echo $mensaje; ?> </div>
        <?php } ?>
        <form action="" method="post" >
            <div class="mb-3 text-start" >
                <label class="form-label" >Correo:</label>
                <input type="email" name="txtEmail" class="form-control" placeholder="admin@test.com" required >

            </div>
            <div class="mb-3 text-start">
                <label class="form-label" >Contraseña:</label>
                <input type="password" name="txtPassword" class="form-control" placeholder="123456" required>

            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Ingresar al sistema</button>

            </div>
        </form>

    </div>
    
</body>
</html>