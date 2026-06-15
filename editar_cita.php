<?php
require_once 'config/conexion.php';

if (!isset($_GET['id'])) {
    header("Location: citas.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT *
    FROM citas
    WHERE id_cita = ?
");
$stmt->execute([$id]);

$cita = $stmt->fetch();

if (!$cita) {
    header("Location: citas.php");
    exit;
}

$mensaje_alerta = "";
$tipo_alerta = "";

$pacientes = $pdo->query("
    SELECT *
    FROM pacientes
    ORDER BY apellido, nombre
")->fetchAll();

$medicos = $pdo->query("
    SELECT *
    FROM medicos
    ORDER BY apellido, nombre
")->fetchAll();

$consultorios = $pdo->query("
    SELECT *
    FROM consultorios
    WHERE estado = 1
    ORDER BY nombre
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_paciente = (int)$_POST['id_paciente'];
    $id_medico = (int)$_POST['id_medico'];
    $id_consultorio = (int)$_POST['id_consultorio'];

    $fecha = $_POST['fecha_cita'];
    $hora = $_POST['hora_cita'];

    $motivo = trim($_POST['motivo']);
    $estado = $_POST['estado'];

    $stmt = $pdo->prepare("
        UPDATE citas
        SET
            id_paciente = ?,
            id_medico = ?,
            id_consultorio = ?,
            fecha_cita = ?,
            hora_cita = ?,
            motivo = ?,
            estado = ?
        WHERE id_cita = ?
    ");

    if ($stmt->execute([
        $id_paciente,
        $id_medico,
        $id_consultorio,
        $fecha,
        $hora,
        $motivo,
        $estado,
        $id
    ])) {

        if (function_exists('registrarLog')) {
            registrarLog(
                $pdo,
                $_SESSION['usuario_id'],
                'EDITAR',
                'citas',
                "Se editó la cita ID: $id"
            );
        }

        header("Location: citas.php");
        exit;
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">

    <h2>Editar Cita Médica</h2>

    <?php if (!empty($mensaje_alerta)): ?>
        <div style="
            background: <?= $tipo_alerta=='error' ? '#f8d7da' : '#d4edda'; ?>;
            color: <?= $tipo_alerta=='error' ? '#721c24' : '#155724'; ?>;
            padding:12px;
            border-radius:6px;
            margin-bottom:15px;">
            <?= $mensaje_alerta; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="filter-box" style="background:#eef7ff;">

        <div class="form-group">
            <label>Paciente</label>
            <select name="id_paciente" class="form-control" required>

                <?php foreach($pacientes as $p): ?>

                    <option
                        value="<?= $p['id_paciente']; ?>"
                        <?= ($p['id_paciente'] == $cita['id_paciente']) ? 'selected' : ''; ?>>

                        <?= htmlspecialchars($p['apellido'] . ', ' . $p['nombre']); ?>

                    </option>

                <?php endforeach; ?>

            </select>
        </div>

        <div class="form-group">
            <label>Médico</label>

            <select name="id_medico" class="form-control" required>

                <?php foreach($medicos as $m): ?>

                    <option
                        value="<?= $m['id_medico']; ?>"
                        <?= ($m['id_medico'] == $cita['id_medico']) ? 'selected' : ''; ?>>

                        <?= htmlspecialchars($m['apellido'] . ', ' . $m['nombre']); ?>

                    </option>

                <?php endforeach; ?>

            </select>
        </div>

        <div class="form-group">
            <label>Consultorio</label>

            <select name="id_consultorio" class="form-control" required>

                <?php foreach($consultorios as $c): ?>

                    <option
                        value="<?= $c['id_consultorio']; ?>"
                        <?= ($c['id_consultorio'] == $cita['id_consultorio']) ? 'selected' : ''; ?>>

                        <?= htmlspecialchars($c['nombre']); ?>

                    </option>

                <?php endforeach; ?>

            </select>
        </div>

        <div class="form-group">
            <label>Fecha</label>

            <input
                type="date"
                name="fecha_cita"
                class="form-control"
                required
                value="<?= $cita['fecha_cita']; ?>">
        </div>

        <div class="form-group">
            <label>Hora</label>

            <input
                type="time"
                name="hora_cita"
                class="form-control"
                required
                value="<?= substr($cita['hora_cita'],0,5); ?>">
        </div>

        <div class="form-group">
            <label>Motivo</label>

            <input
                type="text"
                name="motivo"
                class="form-control"
                value="<?= htmlspecialchars($cita['motivo']); ?>">
        </div>

        <div class="form-group">
            <label>Estado</label>

            <select name="estado" class="form-control">

                <option value="Pendiente"
                    <?= ($cita['estado'] == 'Pendiente') ? 'selected' : ''; ?>>
                    Pendiente
                </option>

                <option value="Atendido"
                    <?= ($cita['estado'] == 'Atendido') ? 'selected' : ''; ?>>
                    Atendido
                </option>

                <option value="Cancelado"
                    <?= ($cita['estado'] == 'Cancelado') ? 'selected' : ''; ?>>
                    Cancelado
                </option>

            </select>
        </div>

        <br>

        <button
            type="submit"
            class="btn"
            style="width:auto;">
            Actualizar Cita
        </button>

        <a
            href="citas.php"
            class="btn"
            style="
                width:auto;
                background:#7f8c8d;">
            Cancelar
        </a>

    </form>

</div>

<script src="js/main.js"></script>

</body>
</html>