<?php
require_once 'config/conexion.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$citas = $pdo->query("
SELECT
c.fecha_cita,
c.hora_cita,
p.nombre,
p.apellido,
c.estado
FROM citas c
INNER JOIN pacientes p
ON c.id_paciente=p.id_paciente
ORDER BY c.fecha_cita DESC
")->fetchAll();
?>

<div class="main-content">

<h2>Reporte de Citas</h2>

<table>

<thead>
<tr>
<th>Fecha</th>
<th>Hora</th>
<th>Paciente</th>
<th>Estado</th>
</tr>
</thead>

<tbody>

<?php foreach($citas as $c): ?>

<tr>
<td><?= $c['fecha_cita'] ?></td>
<td><?= $c['hora_cita'] ?></td>
<td><?= htmlspecialchars($c['nombre']." ".$c['apellido']) ?></td>
<td><?= $c['estado'] ?></td>
</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>