<?php
require_once 'config/conexion.php';

if (
    !isset($_SESSION['usuario_id']) ||
    $_SESSION['usuario_rol'] !== 'Administrador'
) {

    header("Location: dashboard.php");
    exit;
}

$id = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;

$stmt = $pdo->prepare(
"SELECT * FROM usuarios
WHERE id_usuario=?"
);

$stmt->execute([$id]);

$usuario = $stmt->fetch();

if (!$usuario) {

    header("Location: usuarios.php");
    exit;

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nombre = trim($_POST['nombre']);
    $username = trim($_POST['username']);
    $rol = $_POST['rol'];

    /* VALIDAR QUE EL USERNAME NO PERTENEZCA A OTRO USUARIO */

    $stmtValidar = $pdo->prepare(
    "SELECT COUNT(*)
     FROM usuarios
     WHERE username = ?
     AND id_usuario <> ?"
    );

    $stmtValidar->execute([
        $username,
        $id
    ]);

    if ($stmtValidar->fetchColumn() > 0) {

        echo "<script>
                alert('El nombre de usuario ya existe.');
                window.location='editar_usuario.php?id=$id';
              </script>";

        exit;
    }


    if (!empty($_POST['password'])) {

        $password =
        md5(trim($_POST['password']));

        $sql = "
        UPDATE usuarios
        SET nombre=?,
            username=?,
            rol=?,
            password=?
        WHERE id_usuario=?";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $nombre,
            $username,
            $rol,
            $password,
            $id
        ]);

    } else {

        $sql = "
        UPDATE usuarios
        SET nombre=?,
            username=?,
            rol=?
        WHERE id_usuario=?";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $nombre,
            $username,
            $rol,
            $id
        ]);
    }

    registrarLog(
        $pdo,
        $_SESSION['usuario_id'],
        'EDITAR',
        'usuarios',
        "Usuario editado ID: $id"
    );

    header("Location: usuarios.php");
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">

<h2>Editar Usuario</h2>

<form method="POST" class="filter-box">

<div class="form-group">
<label>Nombre</label>
<input
type="text"
name="nombre"
value="<?= htmlspecialchars($usuario['nombre']) ?>"
required
class="form-control">
</div>

<div class="form-group">
<label>Username</label>
<input
type="text"
name="username"
value="<?= htmlspecialchars($usuario['username']) ?>"
required
class="form-control">
</div>

<div class="form-group">
<label>Nueva Contraseña</label>
<input
type="password"
name="password"
class="form-control">
</div>

<div class="form-group">
<label>Rol</label>

<select
name="rol"
class="form-control">

<option
value="Administrador"
<?= $usuario['rol']=='Administrador'?'selected':'' ?>>
Administrador
</option>

<option
value="Recepcionista"
<?= $usuario['rol']=='Recepcionista'?'selected':'' ?>>
Recepcionista
</option>

<option
value="Medico"
<?= $usuario['rol']=='Medico'?'selected':'' ?>>
Médico
</option>

</select>

</div>

<button
type="submit"
class="btn"
style="width:auto;">

Actualizar Usuario

</button>

</form>

</div>