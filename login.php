<?php
//Incluyo mi clases necesarias
require_once "./models/Usuarios.php";
require_once "./models/Roles.php";

//Me traigo el fichero que tiene todas las librerias básicas del proyecto
require_once "utils.php";

//Siempre que entro en login me cargo la sesion
borrarSesion();

//Creo mis dos clases de las entidades que necesito
$usu = new Usuario();
$rol = new Rol();

//Me traigo el query string del id
$usuario_id = $_GET['usuario_id'] ?? '0';
$accion = $_GET['accion'] ?? '';

//Me traigo el query string del error si lo hubiera
$error = $_GET['error'] ?? '';
$usuario="";
if ($error != "") {
    $usuario = $_GET['usuario'] ?? '';
    
    echo "<script>alert('". str_replace("--", "\\n", $error) ."')</script>";
} 

if ($accion == "sesioncaducada") {
    echo "<script>alert('Se te ha caducado la sesión vuelve a hacer login')</script>";
} 


?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Login Usuario</title>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="container">
        <h1>Login Usuario</h1>

        <form method="post">
            <div>
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" placeholder="Nombre de usuario"  value="<?= $usuario ?>">
            </div>


            <div>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••"  value="">
            </div>

            <button onclick="login()">Entrar</button>


        </form>
    </div>
</body>

</html>