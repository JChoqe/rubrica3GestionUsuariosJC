<?php

// Sanitiza todos los datos recibidos por POST
foreach ($_POST as $clave => $valor) {
    $_POST[$clave] = strip_tags(htmlspecialchars($valor));
}

// Sanitiza todos los datos recibidos por GET
foreach ($_GET as $clave => $valor) {
    $_GET[$clave] = strip_tags(htmlspecialchars($valor));
}
