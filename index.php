<?php
require_once 'config/conexion.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ? AND estado = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        
		if ($user && md5($password) === $user['password']) {
			$_SESSION['usuario_id'] = $user['id_usuario'];
			$_SESSION['usuario_nom'] = $user['nombre'];
			$_SESSION['usuario_rol'] = $user['rol'];

			registrarLog($pdo, $user['id_usuario'], 'LOGIN', 'usuarios', 'Inicio de sesión exitoso.');
			header("Location: dashboard.php");
			exit;
		} else {
			$error = "Credenciales incorrectas o usuario inactivo.";
		}
    } else {
        $error = "Por favor, complete todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ERP Clínica - Login</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2 style="text-align:center; margin-bottom:1.5rem;">Clínica ERP</h2>
            <?php if(!empty($error)): ?>
                <div style="color:red; margin-bottom:1rem; text-align:center;"><?= $error; ?></div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Usuario</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn">Ingresar al Sistema</button>
            </form>
        </div>
    </div>
</body>
</html>