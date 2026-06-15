<?php

require_once 'config/conexion.php';

$mensaje_alerta = "";
$tipo_alerta = "";

/* ==========================
   REGISTRAR CONSULTORIO
========================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_consultorio'])) {

    $nombre = trim($_POST['nombre']);
    $piso = trim($_POST['piso']);
    $ubicacion = trim($_POST['ubicacion']);
    $capacidad = (int)$_POST['capacidad'];

    if (empty($nombre)) {

        $mensaje_alerta = "Debe ingresar el nombre del consultorio.";
        $tipo_alerta = "error";

    } else {

        $stmt = $pdo->prepare("
            INSERT INTO consultorios
            (
                nombre,
                piso,
                ubicacion,
                capacidad,
                estado
            )
            VALUES
            (
                ?, ?, ?, ?, 1
            )
        ");

        if ($stmt->execute([
            $nombre,
            $piso,
            $ubicacion,
            $capacidad
        ])) {

            registrarLog(
                $pdo,
                $_SESSION['usuario_id'],
                'CREAR',
                'consultorios',
                "Se registró el consultorio: $nombre"
            );

            $mensaje_alerta =
            "Consultorio registrado correctamente.";

            $tipo_alerta =
            "exito";
        }
    }
}

/* ==========================
   ELIMINAR
========================== */

if (isset($_GET['eliminar_id'])) {

    $id = (int)$_GET['eliminar_id'];

    try {

        $stmt = $pdo->prepare("
        DELETE FROM consultorios
        WHERE id_consultorio = ?
        ");

        if ($stmt->execute([$id])) {

            registrarLog(
                $pdo,
                $_SESSION['usuario_id'],
                'ELIMINAR',
                'consultorios',
                "Se eliminó consultorio ID: $id"
            );
        }

    } catch(PDOException $e){

        header("Location: consultorios.php?error=fk");
        exit;
    }

    header("Location: consultorios.php");
    exit;
}

/* ==========================
   BUSQUEDA
========================== */

$buscar = $_GET['buscar'] ?? '';

$sql = "
SELECT *
FROM consultorios
WHERE 1=1
";

$params = [];

if(!empty($buscar)){

    $sql .= "
    AND (
        nombre LIKE ?
        OR piso LIKE ?
        OR ubicacion LIKE ?
    )
    ";

    $params = [
        "%$buscar%",
        "%$buscar%",
        "%$buscar%"
    ];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$consultorios = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">

<h2>Gestión de Consultorios</h2>

<?php if(isset($_GET['error']) && $_GET['error']=='fk'): ?>

<div style="
background:#f8d7da;
color:#721c24;
padding:1rem;
border-radius:4px;
margin-bottom:1rem;">

<strong>Error:</strong>

El consultorio posee citas asociadas,
no puede eliminarse.

</div>

<?php endif; ?>

<?php if(!empty($mensaje_alerta)): ?>

<div style="
background:
<?= $tipo_alerta=='error'
? '#f8d7da'
: '#d4edda'; ?>;

color:
<?= $tipo_alerta=='error'
? '#721c24'
: '#155724'; ?>;

padding:1rem;
border-radius:5px;
margin-bottom:1rem;">

<?= $mensaje_alerta ?>

</div>

<?php endif; ?>

<!-- BUSCAR -->

<form method="GET" class="filter-box">

<div class="form-group">

<input
type="text"
name="buscar"
class="form-control"
placeholder="Buscar consultorio..."
value="<?= htmlspecialchars($buscar); ?>">

</div>

<button
type="submit"
class="btn"
style="width:auto;">

Buscar

</button>

</form>

<!-- REGISTRAR -->

<h3>Registrar Consultorio</h3>

<form
action="consultorios.php"
method="POST"
class="filter-box"
style="background:#eef7ff;">

<input
type="hidden"
name="registrar_consultorio"
value="1">

<div class="form-group">

<label>Nombre</label>

<input
type="text"
name="nombre"
class="form-control"
required>

</div>

<div class="form-group">

<label>Piso</label>

<input
type="text"
name="piso"
class="form-control">

</div>

<div class="form-group">

<label>Ubicación</label>

<input
type="text"
name="ubicacion"
class="form-control">

</div>

<div class="form-group">

<label>Capacidad</label>

<input
type="number"
name="capacidad"
class="form-control"
value="1"
min="1">

</div>

<button
type="submit"
class="btn"
style="width:auto;">

Guardar Consultorio

</button>

</form>

<!-- TABLA -->

<div class="table-responsive">

<table>

<thead>

<tr>

<th>ID</th>
<th>Nombre</th>
<th>Piso</th>
<th>Ubicación</th>
<th>Capacidad</th>
<th>Estado</th>
<th>Acciones</th>

</tr>

</thead>

<tbody>

<?php foreach($consultorios as $c): ?>

<tr>

<td><?= $c['id_consultorio']; ?></td>

<td><?= htmlspecialchars($c['nombre']); ?></td>

<td><?= htmlspecialchars($c['piso']); ?></td>

<td><?= htmlspecialchars($c['ubicacion']); ?></td>

<td><?= $c['capacidad']; ?></td>

<td>

<?= $c['estado']
? 'Activo'
: 'Inactivo'; ?>

</td>

<td>

<a
href="editar_consultorio.php?id=<?= $c['id_consultorio']; ?>"
style="
color:#3498db;
font-weight:bold;
margin-right:15px;">

[ Editar ]

</a>

<a
href="consultorios.php?eliminar_id=<?= $c['id_consultorio']; ?>"
class="btn-delete"
style="
color:#e74c3c;
font-weight:bold;">

[ Eliminar ]

</a>

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