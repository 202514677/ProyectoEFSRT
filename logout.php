<?php
require_once 'config/conexion.php';
if (isset($_SESSION['usuario_id'])) {
    registrarLog($pdo, $_SESSION['usuario_id'], 'LOGOUT', 'usuarios', 'Sesión finalizada.');
}
session_destroy();
header("Location: index.php");
exit;