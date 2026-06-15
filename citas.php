<?php
require_once 'config/conexion.php';
$mensaje_alerta = ""; $tipo_alerta = "";
$consultorios = $pdo->query("
SELECT *
FROM consultorios
WHERE estado = 1
ORDER BY nombre
")->fetchAll();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_cita'])) {
   $id_paciente = (int)$_POST['id_paciente'];
	$id_medico = (int)$_POST['id_medico'];
	$id_consultorio = (int)$_POST['id_consultorio'];
	$fecha = $_POST['fecha_cita'];
	$hora = $_POST['hora_cita'];
	$motivo = trim($_POST['motivo']);


    if ($hora < "08:00:00" || $hora > "18:00:00") {
        $mensaje_alerta = "La clínica está cerrada. Horario laboral: 08:00 AM a 06:00 PM."; $tipo_alerta = "error";
    } else if ($pdo->query("SELECT COUNT(*) FROM citas WHERE id_paciente = $id_paciente AND fecha_cita = '$fecha' AND estado != 'Cancelado'") -> fetchColumn() > 0) {
        $mensaje_alerta = "El paciente ya cuenta con una cita agendada para esta misma fecha."; $tipo_alerta = "error";
    } else if ($pdo->query("SELECT COUNT(*) FROM citas WHERE id_medico = $id_medico AND fecha_cita = '$fecha' AND hora_cita = '$hora' AND estado != 'Cancelado'") -> fetchColumn() > 0) {
        $mensaje_alerta = "El médico ya se encuentra ocupado en esa hora y fecha seleccionada."; $tipo_alerta = "error";
    } else {
        $stmt = $pdo->prepare("
		INSERT INTO citas
		(
			id_paciente,
			id_medico,
			id_consultorio,
			fecha_cita,
			hora_cita,
			motivo
		)
		VALUES
		(
			?,
			?,
			?,
			?,
			?,
			?
		)
		");

		if(
			$stmt->execute([
				$id_paciente,
				$id_medico,
				$id_consultorio,
				$fecha,
				$hora,
				$motivo
			])
)			{
            registrarLog($pdo, $_SESSION['usuario_id'], 'CREAR', 'citas', "Cita registrada.");
            $mensaje_alerta = "Cita agendada de forma exitosa."; $tipo_alerta = "exito";
        }
    }
}

$fecha_filtro = $_GET['fecha_filtro'] ?? ''; $rango = $_GET['rango'] ?? '';
$sql = "

SELECT

c.*,

CONCAT(
p.nombre,
' ',
p.apellido
) AS paciente,

CONCAT(
m.nombre,
' ',
m.apellido
) AS medico,

co.nombre AS consultorio

FROM citas c

INNER JOIN pacientes p
ON c.id_paciente = p.id_paciente

INNER JOIN medicos m
ON c.id_medico = m.id_medico

LEFT JOIN consultorios co
ON c.id_consultorio = co.id_consultorio

WHERE 1=1

";
if (!empty($fecha_filtro)) { $sql .= " AND c.fecha_cita = '$fecha_filtro'"; } 
else if (!empty($rango)) {
    if ($rango === 'dia') $sql .= " AND c.fecha_cita = CURDATE()";
    if ($rango === 'semana') $sql .= " AND YEARWEEK(c.fecha_cita, 1) = YEARWEEK(CURDATE(), 1)";
    if ($rango === 'mes') $sql .= " AND MONTH(c.fecha_cita) = MONTH(CURDATE()) AND YEAR(c.fecha_cita) = YEAR(CURDATE())";
}
$citas = $pdo->query($sql)->fetchAll();

include 'includes/header.php'; include 'includes/sidebar.php';
?>
<div class="main-content">
    <h2>Gestión de Citas Médicas</h2>
    <?php if (!empty($mensaje_alerta)): ?>
        <div style="background:<?= $tipo_alerta==='error'?'#f8d7da':'#d4edda'; ?>; color:<?= $tipo_alerta==='error'?'#721c24':'#155724'; ?>; padding:1rem; border-radius:5px; margin-bottom:1.5rem;"><?= $mensaje_alerta; ?></div>
    <?php endif; ?>

    <fieldset style="padding:1rem; margin-bottom:1.5rem; border:1px solid #ccc; border-radius:5px;">
        <legend>Filtros Avanzados y Exportación</legend>
        <form method="GET" class="filter-box">
            <div class="form-group"><label>Rango:</label><select name="rango" class="form-control"><option value="">-- Todos --</option><option value="dia" <?= $rango=='dia'?'selected':''; ?>>Hoy</option><option value="semana" <?= $rango=='semana'?'selected':''; ?>>Esta Semana</option><option value="mes" <?= $rango=='mes'?'selected':''; ?>>Este Mes</option></select></div>
            <div class="form-group"><label>O por Fecha:</label><input type="date" name="fecha_filtro" class="form-control" value="<?= htmlspecialchars($fecha_filtro); ?>"></div>
            <button type="submit" class="btn" style="width:auto;">Filtrar</button>
            <a href="reportes/exportar.php?tipo=excel&rango=<?= $rango ?>&fecha=<?= $fecha_filtro ?>" class="btn" style="background:#27ae60; width:auto;">Descargar XLS</a>
            <a href="reportes/exportar.php?tipo=csv&rango=<?= $rango ?>&fecha=<?= $fecha_filtro ?>" class="btn" style="background:#e67e22; width:auto;">Descargar CSV</a>
            <a href="#" onclick="window.print();" class="btn" style="background:#e74c3c; width:auto;">Imprimir (PDF)</a>
        </form>
    </fieldset>

    <h3>Agendar Nueva Cita</h3>
    <form action="citas.php" method="POST" class="filter-box" style="background:#ebf5fb;">
        <input type="hidden" name="registrar_cita" value="1">
        <div class="form-group"><label>Paciente</label><select name="id_paciente" class="form-control" required><option value="">-- Seleccione --</option><?php foreach($pdo->query("SELECT id_paciente, CONCAT(apellido, ', ', nombre) as nom FROM pacientes")->fetchAll() as $p) echo "<option value='{$p['id_paciente']}'>{$p['nom']}</option>"; ?></select></div>
        <div class="form-group"><label>Médico</label><select name="id_medico" class="form-control" required><option value="">-- Seleccione --</option><?php foreach($pdo->query("SELECT id_medico, CONCAT(apellido, ', ', nombre, ' - ', especialidad, ' (CMP: ', cmp, ')') as nom FROM medicos")->fetchAll() as $m) echo "<option value='{$m['id_medico']}'>{$m['nom']}</option>"; ?></select></div>
		<div class="form-group">

<label>Consultorio</label>

<select
name="id_consultorio"
class="form-control"
required>

<option value="">
-- Seleccione --
</option>

<?php foreach($consultorios as $co): ?>

<option
value="<?= $co['id_consultorio']; ?>">

<?= htmlspecialchars($co['nombre']); ?>

</option>

<?php endforeach; ?>

</select>

</div>
        <div class="form-group"><label>Fecha</label><input type="date" name="fecha_cita" class="form-control" required min="<?= date('Y-m-d'); ?>"></div>
        <div class="form-group"><label>Hora</label><input type="time" name="hora_cita" class="form-control" required></div>
        <div class="form-group"><label>Motivo</label><input type="text" name="motivo" class="form-control"></div>
        <button type="submit" class="btn" style="width:auto;">Guardar Cita</button>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
				<tr>
				<th>ID</th>
				<th>Paciente</th>
				<th>Médico</th>
				<th>Consultorio</th>
				<th>Fecha</th>
				<th>Hora</th>
				<th>Estado</th>
				<th>Acciones</th>
				</tr>
		    </thead>
            <tbody>
               <?php foreach($citas as $c): ?>
<tr>

<td><?= $c['id_cita']; ?></td>

<td><?= htmlspecialchars($c['paciente']); ?></td>

<td><?= htmlspecialchars($c['medico']); ?></td>

<td><?= htmlspecialchars($c['consultorio']); ?></td>

<td><?= $c['fecha_cita']; ?></td>

<td><?= date('h:i A', strtotime($c['hora_cita'])); ?></td>

<td>
<span style="padding:4px 8px; background:#f1c40f; border-radius:4px;">
<?= $c['estado']; ?>
</span>
</td>

<td>
<a
href="editar_cita.php?id=<?= $c['id_cita']; ?>"
style="color:#3498db;font-weight:bold;">
[ Editar ]
</a>
</td>

</tr>
<?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div></div>
<script src="js/main.js"></script>
</body>
</html>