<?php

require_once 'config/conexion.php';

if (!isset($_GET['id'])) {
    header("Location: consultorios.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("
SELECT *
FROM consultorios
WHERE id_consultorio = ?
");

$stmt->execute([$id]);

$consultorio = $stmt->fetch();

if (!$consultorio) {
    header("Location: consultorios.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $piso = trim($_POST['piso']);
    $ubicacion = trim($_POST['ubicacion']);
    $capacidad = (int)$_POST['capacidad'];
    $estado = (int)$_POST['estado'];

    $stmt = $pdo->prepare("
    UPDATE consultorios
    SET
        nombre=?,
        piso=?,
        ubicacion=?,
        capacidad=?,
        estado=?
    WHERE id_consultorio=?
    ");

    $stmt->execute([
        $nombre,
        $piso,
        $ubicacion,
        $capacidad,
        $estado,
        $id
    ]);

    registrarLog(
        $pdo,
        $_SESSION['usuario_id'],
        'EDITAR',
        'consultorios',
        "Se editó consultorio ID: $id"
    );

    header("Location: consultorios.php");
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">

<h2>Editar Consultorio</h2>

<form method="POST"
      class="filter-box"
      style="background:#eef7ff;">

<div class="form-group">

<label>Nombre</label>

<input
type="text"
name="nombre"
class="form-control"
required
value="<?= htmlspecialchars($consultorio['nombre']); ?>">

</div>

<div class="form-group">

<label>Piso</label>

<input
type="text"
name="piso"
class="form-control"
value="<?= htmlspecialchars($consultorio['piso']); ?>">

</div>

<div class="form-group">

<label>Ubicación</label>

<input
type="text"
name="ubicacion"
class="form-control"
value="<?= htmlspecialchars($consultorio['ubicacion']); ?>">

</div>

<div class="form-group">

<label>Capacidad</label>

<input
type="number"
name="capacidad"
class="form-control"
min="1"
value="<?= $consultorio['capacidad']; ?>">

</div>

<div class="form-group">

<label>Estado</label>

<select
name="estado"
class="form-control">

<option value="1"
<?= $consultorio['estado']==1 ? 'selected' : ''; ?>>
Activo
</option>

<option value="0"
<?= $consultorio['estado']==0 ? 'selected' : ''; ?>>
Inactivo
</option>

</select>

</div>

<div class="form-group">

<button
type="submit"
class="btn"
style="width:auto;">

Actualizar

</button>

<a
href="consultorios.php"
class="btn"
style="
width:auto;
background:#7f8c8d;">

Cancelar

</a>

</div>

</form>

</div>

<script src="js/main.js"></script>

</body>
</html>