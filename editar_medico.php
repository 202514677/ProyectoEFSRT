<?php
require_once 'config/conexion.php';

if (!isset($_GET['id'])) {
    header("Location: medicos.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM medicos WHERE id_medico = ?");
$stmt->execute([$id]);
$medico = $stmt->fetch();

if (!$medico) {
    header("Location: medicos.php");
    exit;
}

$mensaje_alerta = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dni = trim($_POST['dni']);
    $cmp = trim($_POST['cmp']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $especialidad = trim($_POST['especialidad']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);

    if (strlen($dni) != 8 || !is_numeric($dni)) {

        $mensaje_alerta = "El DNI debe tener 8 dígitos.";
        $tipo_alerta = "error";

    } else {

        $sql = "UPDATE medicos
                SET dni=?,
                    cmp=?,
                    nombre=?,
                    apellido=?,
                    especialidad=?,
                    telefono=?,
                    email=?
                WHERE id_medico=?";

        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([
            $dni,
            $cmp,
            $nombre,
            $apellido,
            $especialidad,
            $telefono,
            $email,
            $id
        ])) {

            registrarLog(
                $pdo,
                $_SESSION['usuario_id'],
                'EDITAR',
                'medicos',
                "Se editó el médico ID $id"
            );

            header("Location: medicos.php");
            exit;
        }
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">

<h2>Editar Médico</h2>

<?php if(!empty($mensaje_alerta)): ?>
<div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:5px;margin-bottom:15px;">
<?= $mensaje_alerta ?>
</div>
<?php endif; ?>

<form method="POST" class="filter-box">

<div class="form-group">
<label>DNI</label>
<input type="text"
name="dni"
value="<?= htmlspecialchars($medico['dni']) ?>"
maxlength="8"
pattern="\d{8}"
required
class="form-control">
</div>

<div class="form-group">
<label>CMP</label>
<input type="text"
name="cmp"
value="<?= htmlspecialchars($medico['cmp']) ?>"
required
class="form-control">
</div>

<div class="form-group">
<label>Nombres</label>
<input type="text"
name="nombre"
value="<?= htmlspecialchars($medico['nombre']) ?>"
required
class="form-control">
</div>

<div class="form-group">
<label>Apellidos</label>
<input type="text"
name="apellido"
value="<?= htmlspecialchars($medico['apellido']) ?>"
required
class="form-control">
</div>

<div class="form-group">
<label>Especialidad</label>
<input type="text"
name="especialidad"
value="<?= htmlspecialchars($medico['especialidad']) ?>"
required
class="form-control">
</div>

<div class="form-group">
<label>Teléfono</label>
<input type="text"
name="telefono"
value="<?= htmlspecialchars($medico['telefono']) ?>"
class="form-control">
</div>

<div class="form-group">
<label>Email</label>
<input type="email"
name="email"
value="<?= htmlspecialchars($medico['email']) ?>"
class="form-control">
</div>

<button type="submit" class="btn" style="width:auto;">
Actualizar Médico
</button>

<a href="medicos.php" class="btn"
style="width:auto;background:#7f8c8d;">
Cancelar
</a>

</form>

</div>

</body>
</html>