<?php
require_once 'config/conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: dashboard.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_usuario'])) {
    $username = trim($_POST['username']); $nombre = trim($_POST['nombre']); $rol = $_POST['rol'];
    $password = md5(trim($_POST['password']));

    $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, nombre, rol, estado) VALUES (?, ?, ?, ?, 1)");
    if ($stmt->execute([$username, $password, $nombre, $rol])) {
        registrarLog($pdo, $_SESSION['usuario_id'], 'CREAR', 'usuarios', "Se creó el usuario: $username");
    }
    header("Location: usuarios.php"); exit;
}

if (isset($_GET['cambiar_estado']) && isset($_GET['id'])) {
    $id_u = (int)$_GET['id']; $nuevo_estado = (int)$_GET['cambiar_estado'];
    $stmt = $pdo->prepare("UPDATE usuarios SET estado = ? WHERE id_usuario = ?");
    if ($stmt->execute([$nuevo_estado, $id_u])) {
        registrarLog($pdo, $_SESSION['usuario_id'], 'MODIFICAR_ESTADO', 'usuarios', "Cambio de estado del usuario ID: $id_u a $nuevo_estado");
    }
    header("Location: usuarios.php"); exit;
}

$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY id_usuario DESC")->fetchAll();
include 'includes/header.php'; include 'includes/sidebar.php';
?>
<div class="main-content">
    <h2>Administración de Usuarios del Sistema (Exclusivo Administrador)</h2>
    <form action="" method="POST" class="filter-box" style="background:#fcfcfc;">
        <input type="hidden" name="registrar_usuario" value="1">
        <div class="form-group"><label>Nombre</label><input type="text" name="nombre" class="form-control" required></div>
        <div class="form-group"><label>Username</label><input type="text" name="username" class="form-control" required></div>
        <div class="form-group"><label>Contraseña</label><input type="password" name="password" class="form-control" required></div>
        <div class="form-group"><label>Rol</label><select name="rol" class="form-control"><option value="Administrador">Administrador</option><option value="Recepcionista">Recepcionista</option><option value="Medico">Médico</option></select></div>
        <button type="submit" class="btn" style="width:auto; height:42px; align-self:flex-end;">Guardar Usuario</button>
    </form>

    <div class="table-responsive">
        <table>
            <thead><tr><th>ID</th><th>Username</th><th>Nombre</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach($usuarios as $u): ?>
                <tr>
                    <td><?= $u['id_usuario']; ?></td><td><strong><?= htmlspecialchars($u['username']); ?></strong></td><td><?= htmlspecialchars($u['nombre']); ?></td><td><?= $u['rol']; ?></td>
                    <td><?= $u['estado'] == 1 ? '<span style="color:green; font-weight:bold;">Activo</span>' : '<span style="color:red; font-weight:bold;">Inactivo</span>'; ?></td>
                    <td>
                        <?php if($u['estado'] == 1): ?>
                            <a href="usuarios.php?cambiar_estado=0&id=<?= $u['id_usuario']; ?>" class="btn-delete" style="color:#e67e22; font-weight:bold; margin-right:10px;">[ Dar de Baja ]</a>
                        <?php else: ?>
                            <a href="usuarios.php?cambiar_estado=1&id=<?= $u['id_usuario']; ?>" style="color:#2bc155; font-weight:bold; margin-right:10px;">[ Activar ]</a>
                        <?php endif; ?>
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