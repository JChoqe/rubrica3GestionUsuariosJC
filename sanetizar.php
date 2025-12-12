<?php
foreach ($_POST as $clave => $valor) {
    $_POST[$clave]=strip_tags(htmlspecialchars($valor));
}

foreach ($_GET as $clave => $valor) {
    $_GET[$clave]=strip_tags(htmlspecialchars($valor));
}
