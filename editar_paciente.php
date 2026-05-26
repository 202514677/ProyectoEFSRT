<?php
require_once 'config/conexion.php';

$mensaje_alerta = "";
$tipo_alerta = "";

// 1. Validar que llegue un ID válido por la URL
$id = (int)($_GET['id'] ?? 0);

// 2. Buscar los datos actuales del paciente para rellenar los inputs
$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id_paciente = ?");
$stmt->execute([$id]);
$paciente = $stmt->fetch();

// Si el ID no existe en la BD, lo regresamos a la lista
if (!$paciente) {
    header("Location: pacientes.php");
    exit;
}

// 3. Procesar la actualización cuando se presione "Actualizar Datos"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $fecha_nac = $_POST['fecha_nacimiento'];
    $sexo = $_POST['sexo'];

    // Ejecutamos el UPDATE en la tabla
    $update = $pdo->prepare("UPDATE pacientes SET nombre = ?, apellido = ?, telefono = ?, email = ?, fecha_nacimiento = ?, sexo = ? WHERE id_paciente = ?");
    
    if ($update->execute([$nombre, $apellido, $telefono, $email, $fecha_nac, $sexo, $id])) {
        // Dejamos huella en la auditoría para la exposición
        registrarLog($pdo, $_SESSION['usuario_id'], 'MODIFICAR', 'pacientes', "Se actualizaron los datos del paciente: {$apellido}, {$nombre} (ID: {$id})");
        
        // Redireccionamos de golpe a la lista principal para ver los cambios
        header("Location: pacientes.php");
        exit;
    } else {
        $mensaje_alerta = "No se pudieron actualizar los datos del paciente.";
        $tipo_alerta = "error";
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <h2>Editar Historial de Paciente</h2>
    <p>Modifique los campos necesarios del paciente seleccionado.</p>

    <?php if (!empty($mensaje_alerta)): ?>
        <div style="background:#f8d7da; color:#721c24; padding:1rem; border-radius:5px; margin-bottom:1.5rem;">
            <strong>⚠️ Error:</strong> <?= $mensaje_alerta; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" style="background:#fff; padding:2rem; border-radius:8px; max-width:600px; box-shadow:0 2px 5px rgba(0,0,0,0.1); margin-top:1.5rem;">
        
        <div class="form-group">
            <label>DNI (No editable por seguridad de identidad)</label>
            <input type="text" class="form-control" value="<?= $paciente['dni']; ?>" disabled style="background:#e9ecef; cursor:not-allowed;">
        </div>

        <div class="form-group">
            <label>Nombres</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($paciente['nombre']); ?>" required>
        </div>

        <div class="form-group">
            <label>Apellidos</label>
            <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($paciente['apellido']); ?>" required>
        </div>

        <div class="form-group">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($paciente['telefono'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($paciente['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" class="form-control" value="<?= $paciente['fecha_nacimiento']; ?>" required>
        </div>

        <div class="form-group">
            <label>Sexo</label>
            <select name="sexo" class="form-control" required>
                <option value="M" <?= $paciente['sexo'] === 'M' ? 'selected' : ''; ?>>Masculino</option>
                <option value="F" <?= $paciente['sexo'] === 'F' ? 'selected' : ''; ?>>Femenino</option>
            </select>
        </div>

        <div style="margin-top:1.5rem; display:flex; gap:10px;">
            <button type="submit" class="btn" style="width:auto; background:var(--secondary);">Guardar Cambios</button>
            <a href="pacientes.php" class="btn" style="width:auto; background:#95a5a6; text-decoration:none;">Cancelar</a>
        </div>
    </form>
</div>
</div></div>
<script src="js/main.js"></script>
</body>
</html>