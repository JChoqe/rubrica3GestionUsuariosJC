<?php

//Me traigo el fichero que tiene todas las librerias básicas del proyecto
require_once "utils.php";

//Siempre que entro en login me cargo la sesion
borrarSesion();
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="./estilos/estilos.css">
    <title>Inicio</title>
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="container">
        <form id="frmInicio" name="frmInicio" action="" method="post">
            <h1>Bienvenido</h1>
            <button class="btn primary" onclick="IrFicha()">Crear nuevo usuario</button>
            <button class="btn secondary" onclick="IrLogin()">Login</button>
        </form>
    </div>
</body>

</html>