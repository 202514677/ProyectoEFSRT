<?php
require_once 'config/conexion.php';
$mensaje_alerta = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_paciente'])) {

    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $fecha_nac = $_POST['fecha_nacimiento'];
    $sexo = $_POST['sexo'];

    if (strlen($dni) !== 8 || !is_numeric($dni)) {

        $mensaje_alerta = "El DNI debe contener exactamente 8 números.";
        $tipo_alerta = "error";

    } else {

        $stmtValidar = $pdo->prepare(
            "SELECT COUNT(*) FROM pacientes WHERE dni = ?"
        );

        $stmtValidar->execute([$dni]);

        if ($stmtValidar->fetchColumn() > 0) {

            $mensaje_alerta = "El paciente con DNI $dni ya se encuentra registrado.";
            $tipo_alerta = "error";

        } else {

            $stmt = $pdo->prepare(
                "INSERT INTO pacientes
                (dni, nombre, apellido, telefono, email, fecha_nacimiento, sexo)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            if ($stmt->execute([
                $dni,
                $nombre,
                $apellido,
                $telefono,
                $email,
                $fecha_nac,
                $sexo
            ])) {

                registrarLog(
                    $pdo,
                    $_SESSION['usuario_id'],
                    'CREAR',
                    'pacientes',
                    "Se registró al paciente DNI: $dni"
                );

                $mensaje_alerta = "Paciente registrado correctamente.";
                $tipo_alerta = "exito";
            }
        }
    }
}

if (isset($_GET['eliminar_id'])) {

    $id_p = (int)$_GET['eliminar_id'];

    try {

        $stmt = $pdo->prepare(
            "DELETE FROM pacientes WHERE id_paciente = ?"
        );

        if ($stmt->execute([$id_p])) {

            registrarLog(
                $pdo,
                $_SESSION['usuario_id'],
                'ELIMINAR',
                'pacientes',
                "Se eliminó al paciente ID: $id_p"
            );
        }

    } catch (PDOException $e) {

        header("Location: pacientes.php?error=fk");
        exit;
    }

    header("Location: pacientes.php");
    exit;
}

$buscar = $_GET['buscar'] ?? '';

$sql = "SELECT * FROM pacientes WHERE 1=1";

$params = [];

if (!empty($buscar)) {

    $sql .= " AND (dni LIKE ? OR nombre LIKE ? OR apellido LIKE ?)";

    $params = [
        "%$buscar%",
        "%$buscar%",
        "%$buscar%"
    ];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$pacientes = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <h2>Gestión de Pacientes</h2>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'fk'): ?>
        <div style="background:#f8d7da; color:#721c24; padding:1rem; border-radius:4px; margin-bottom:1rem;">
            <strong>Error:</strong> El paciente posee citas agendadas, no se puede eliminar.
        </div>
    <?php endif; ?>

    <?php if(!empty($mensaje_alerta)): ?>
        <div style="background:<?= $tipo_alerta==='error'?'#f8d7da':'#d4edda'; ?>; color:<?= $tipo_alerta==='error'?'#721c24':'#155724'; ?>; padding:1rem; border-radius:5px; margin-bottom:1rem;">
            <?= $mensaje_alerta ?>
        </div>
    <?php endif; ?>

    <form method="GET" class="filter-box">
        <div class="form-group">
            <input type="text"
                   name="buscar"
                   class="form-control"
                   placeholder="DNI, Nombre o Apellido..."
                   value="<?= htmlspecialchars($buscar); ?>">
        </div>

        <button type="submit" class="btn" style="width:auto;">
            Buscar
        </button>
    </form>

    <h3>Registrar Nuevo Paciente</h3>

    <form action="pacientes.php" method="POST" class="filter-box" style="background:#e8f8f5;">

        <input type="hidden" name="registrar_paciente" value="1">

        <div class="form-group">
            <label>DNI (8 dígitos)</label>
            <input type="text"
                   name="dni"
                   maxlength="8"
                   pattern="\d{8}"
                   class="form-control"
                   required>
        </div>

        <div class="form-group">
            <label>Nombres</label>
            <input type="text"
                   name="nombre"
                   class="form-control"
                   required>
        </div>

        <div class="form-group">
            <label>Apellidos</label>
            <input type="text"
                   name="apellido"
                   class="form-control"
                   required>
        </div>

        <div class="form-group">
            <label>Teléfono</label>
            <input type="text"
                   name="telefono"
                   class="form-control">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email"
                   name="email"
                   class="form-control">
        </div>

        <div class="form-group">
            <label>F. Nacimiento</label>
            <input type="date"
                   name="fecha_nacimiento"
                   class="form-control"
                   required>
        </div>

        <div class="form-group">
            <label>Sexo</label>

            <select name="sexo" class="form-control" required>
                <option value="M">M</option>
                <option value="F">F</option>
            </select>
        </div>

        <button type="submit" class="btn" style="width:auto;">
            Guardar Paciente
        </button>

    </form>

    <div class="table-responsive">

        <table>

            <thead>
            <tr>
                <th>ID</th>
                <th>DNI</th>
                <th>Paciente</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
            </thead>

            <tbody>

            <?php foreach($pacientes as $p): ?>

                <tr>

                    <td><?= $p['id_paciente']; ?></td>

                    <td><?= $p['dni']; ?></td>

                    <td><?= htmlspecialchars($p['apellido'] . ", " . $p['nombre']); ?></td>

                    <td><?= htmlspecialchars($p['telefono'] ?? '-'); ?></td>

                    <td>

                        <a href="editar_paciente.php?id=<?= $p['id_paciente']; ?>"
                           style="color:#3498db; font-weight:bold; margin-right:15px;">
                            [ Editar ]
                        </a>

                        <a href="pacientes.php?eliminar_id=<?= $p['id_paciente']; ?>"
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