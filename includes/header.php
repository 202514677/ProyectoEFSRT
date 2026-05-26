<?php
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Clínica</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div style="width: 100%; display: flex; flex-direction: column;">
        <div class="top-navbar">
            <button class="menu-toggle" id="menuToggle">&#9776;</button>
            <span>Clínica ERP v1.0</span>
            <span><?= htmlspecialchars($_SESSION['usuario_nom']); ?></span>
        </div>
        <div style="display: flex; flex: 1;">