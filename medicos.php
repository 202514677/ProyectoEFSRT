<?php
require_once 'config/conexion.php';
$mensaje_alerta = ""; $tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_medico'])) {
    $dni = trim($_POST['dni']);
    $cmp = trim($_POST['cmp']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $especialidad = trim($_POST['especialidad']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);

    if (strlen($dni) !== 8 || !is_numeric($dni)) {

        $mensaje_alerta = "El DNI debe tener 8 dígitos.";
        $tipo_alerta = "error";

    } else if (strlen($cmp) < 5 || strlen($cmp) > 6 || !is_numeric($cmp)) {

        $mensaje_alerta = "El CMP debe contener entre 5 y 6 números.";
        $tipo_alerta = "error";

    } else {

        $stmtValidar = $pdo->prepare(
            "SELECT COUNT(*) FROM medicos WHERE dni = ? OR cmp = ?"
        );

        $stmtValidar->execute([
            $dni,
            $cmp
        ]);

        if ($stmtValidar->fetchColumn() > 0) {

            $mensaje_alerta = "El DNI o número de CMP ya se encuentra registrado.";
            $tipo_alerta = "error";

        } else {

            $stmt = $pdo->prepare("INSERT INTO medicos (dni, cmp, nombre, apellido, especialidad, telefono, email) VALUES (?, ?, ?, ?, ?, ?, ?)");

            if ($stmt->execute([$dni, $cmp, $nombre, $apellido, $especialidad, $telefono, $email])) {

                registrarLog(
                    $pdo,
                    $_SESSION['usuario_id'],
                    'CREAR',
                    'medicos',
                    "Se registró al médico con CMP: $cmp"
                );

                $mensaje_alerta = "Médico guardado con éxito.";
                $tipo_alerta = "exito";
            }
        }
    }
}

if (isset($_GET['eliminar_id'])) {
    $id_m = (int)$_GET['eliminar_id'];

    try {

        $stmt = $pdo->prepare("DELETE FROM medicos WHERE id_medico = ?");

        if ($stmt->execute([$id_m])) {

            registrarLog(
                $pdo,
                $_SESSION['usuario_id'],
                'ELIMINAR',
                'medicos',
                "Se eliminó al médico ID: $id_m"
            );
        }

    } catch (PDOException $e) {

        header("Location: medicos.php?error=fk");
        exit;
    }

    header("Location: medicos.php");
    exit;
}

$medicos = $pdo->query("SELECT * FROM medicos ORDER BY apellido ASC")->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <h2>Gestión del Cuerpo Médico</h2>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'fk'): ?>
        <div style="background:#f8d7da; color:#721c24; padding:1rem; border-radius:4px; margin-bottom:1rem;">
            <strong>Error:</strong> El especialista posee citas asignadas, no se puede borrar.
        </div>
    <?php endif; ?>

    <?php if(!empty($mensaje_alerta)): ?>
        <div style="background:<?= $tipo_alerta==='error'?'#f8d7da':'#d4edda'; ?>; color:<?= $tipo_alerta==='error'?'#721c24':'#155724'; ?>; padding:1rem; border-radius:5px; margin-bottom:1rem;">
            <?= $mensaje_alerta ?>
        </div>
    <?php endif; ?>

    <h3>Inscribir Nuevo Especialista</h3>

    <form action="medicos.php" method="POST" class="filter-box" style="background:#fef9e7;">

        <input type="hidden" name="registrar_medico" value="1">

        <div class="form-group">
            <label>DNI</label>
            <input type="text" name="dni" maxlength="8" pattern="\d{8}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>N° CMP</label>
            <input type="text" name="cmp" minlength="5" maxlength="6" pattern="\d{5,6}" class="form-control" required placeholder="Ej. 45892">
        </div>

        <div class="form-group">
            <label>Nombres</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Apellidos</label>
            <input type="text" name="apellido" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Especialidad</label>
            <input type="text" name="especialidad" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>

        <button type="submit" class="btn" style="width:auto; background:#f39c12;">
            Guardar Médico
        </button>

    </form>

    <div class="table-responsive">

        <table>

            <thead>
            <tr>
                <th>ID</th>
                <th>DNI</th>
                <th>CMP</th>
                <th>Médico</th>
                <th>Especialidad</th>
                <th>Acciones</th>
            </tr>
            </thead>

            <tbody>

            <?php foreach($medicos as $m): ?>

                <tr>

                    <td><?= $m['id_medico']; ?></td>
                    <td><?= $m['dni']; ?></td>
                    <td><strong><?= $m['cmp']; ?></strong></td>

                    <td><?= htmlspecialchars($m['apellido'] . ", " . $m['nombre']); ?></td>

                    <td><?= htmlspecialchars($m['especialidad']); ?></td>

                    <td>

                        <a href="editar_medico.php?id=<?= $m['id_medico']; ?>"
                           style="color:#3498db; font-weight:bold; margin-right:15px;">
                            [ Editar ]
                        </a>

                        <a href="medicos.php?eliminar_id=<?= $m['id_medico']; ?>"
                           class="btn-delete"
                           style="color:#e74c3c; font-weight:bold;">
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