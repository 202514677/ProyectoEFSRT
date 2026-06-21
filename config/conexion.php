<?php
$host = "localhost:3306";
$user = "root";
$pass = "";
$db   = "clinica_erp";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {

    die("Error de conexión: " . $e->getMessage());

}


/* SEGURIDAD DE SESIONES */

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

if (session_status() == PHP_SESSION_NONE) {

    session_start();

}


function registrarLog(
    $pdo,
    $id_usuario,
    $accion,
    $tabla,
    $detalles
)
{
    try {

        $ip = $_SERVER['REMOTE_ADDR'];

        $stmt = $pdo->prepare("
            INSERT INTO logs_sistema
            (
                id_usuario,
                accion,
                tabla_afectada,
                detalles,
                ip_direccion
            )
            VALUES
            (
                ?,?,?,?,?
            )
        ");

        $stmt->execute([
            $id_usuario,
            $accion,
            $tabla,
            $detalles,
            $ip
        ]);

    } catch (Exception $e) {

        // Fallback silencioso

    }
}
?>