<?php
require_once 'config/conexion.php';

if (
    !isset($_SESSION['usuario_id']) ||
    $_SESSION['usuario_rol'] !== 'Administrador'
) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->query("
SELECT

l.id_log,
u.nombre,
l.accion,
l.tabla_afectada,
l.detalles,
l.ip_direccion,
l.fecha_registro

FROM logs_sistema l

LEFT JOIN usuarios u
ON l.id_usuario = u.id_usuario

ORDER BY l.fecha_registro DESC

");

$logs = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">

<h2>Auditoría del Sistema</h2>

<div class="table-responsive">

<table>

<thead>

<tr>

<th>ID</th>
<th>Usuario</th>
<th>Acción</th>
<th>Tabla</th>
<th>Detalle</th>
<th>IP</th>
<th>Fecha</th>

</tr>

</thead>

<tbody>

<?php foreach($logs as $log): ?>

<tr>

<td>
<?= $log['id_log']; ?>
</td>

<td>
<?= htmlspecialchars($log['nombre'] ?? 'Usuario eliminado'); ?>
</td>

<td>

<?php
$color = '#3498db';

if ($log['accion'] == 'LOGIN')
    $color = '#27ae60';

if ($log['accion'] == 'LOGOUT')
    $color = '#e67e22';

if ($log['accion'] == 'ELIMINAR')
    $color = '#e74c3c';

if ($log['accion'] == 'CREAR')
    $color = '#2ecc71';

if ($log['accion'] == 'EDITAR')
    $color = '#3498db';
?>

<span
style="
background:<?= $color ?>;
color:white;
padding:4px 8px;
border-radius:5px;
font-size:12px;
font-weight:bold;">

<?= htmlspecialchars($log['accion']); ?>

</span>

</td>

<td>
<?= htmlspecialchars($log['tabla_afectada']); ?>
</td>

<td>
<?= htmlspecialchars($log['detalles']); ?>
</td>

<td>
<?= htmlspecialchars($log['ip_direccion']); ?>
</td>

<td>
<?= date(
'd/m/Y H:i',
strtotime($log['fecha_registro'])
); ?>
</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>
</div>

<script src="js/main.js"></script>

</body>
</html>