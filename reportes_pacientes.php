<?php
require_once 'config/conexion.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$total = $pdo->query("SELECT COUNT(*) FROM pacientes")->fetchColumn();

$pacientes = $pdo->query("
SELECT *
FROM pacientes
ORDER BY id_paciente DESC
LIMIT 10
")->fetchAll();
?>

<div class="main-content">

<h2>Reporte de Pacientes</h2>

<h3>Total de pacientes registrados: <?= $total ?></h3>

<table>
<thead>
<tr>
<th>ID</th>
<th>Nombre</th>
<th>Apellido</th>
</tr>
</thead>

<tbody>

<?php foreach($pacientes as $p): ?>

<tr>
<td><?= $p['id_paciente'] ?></td>
<td><?= htmlspecialchars($p['nombre']) ?></td>
<td><?= htmlspecialchars($p['apellido']) ?></td>
</tr>

<?php endforeach; ?>

</tbody>
</table>

</div>