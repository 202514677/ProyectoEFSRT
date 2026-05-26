<?php
require_once '../config/conexion.php';
if (!isset($_SESSION['usuario_id'])) { die("Acceso denegado."); }

$tipo = $_GET['tipo'] ?? 'csv'; $rango = $_GET['rango'] ?? ''; $fecha_filtro = $_GET['fecha'] ?? '';

$sql = "SELECT c.id_cita, CONCAT(p.nombre, ' ', p.apellido) AS paciente, CONCAT(m.nombre, ' ', m.apellido) AS medico, c.fecha_cita, c.hora_cita, c.estado FROM citas c JOIN pacientes p ON c.id_paciente = p.id_paciente JOIN medicos m ON c.id_medico = m.id_medico WHERE 1=1";
if (!empty($fecha_filtro)) { $sql .= " AND c.fecha_cita = '$fecha_filtro'"; } 
else if (!empty($rango)) {
    if ($rango === 'dia') $sql .= " AND c.fecha_cita = CURDATE()";
    if ($rango === 'semana') $sql .= " AND YEARWEEK(c.fecha_cita, 1) = YEARWEEK(CURDATE(), 1)";
    if ($rango === 'mes') $sql .= " AND MONTH(c.fecha_cita) = MONTH(CURDATE()) AND YEAR(c.fecha_cita) = YEAR(CURDATE())";
}
$data = $pdo->query($sql)->fetchAll();
$filename = "Reporte_Citas_" . date('Ymd_His');

if ($tipo === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=$filename.xls");
} else {
    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=$filename.csv");
}

$salida = fopen('php://output', 'w');
fputcsv($salida, ['ID Cita', 'Paciente', 'Medico', 'Fecha', 'Hora', 'Estado'], ";");
foreach ($data as $row) {
    fputcsv($salida, [$row['id_cita'], utf8_decode($row['paciente']), utf8_decode($row['medico']), $row['fecha_cita'], $row['hora_cita'], $row['estado']], ";");
}
fclose($salida);
registrarLog($pdo, $_SESSION['usuario_id'], 'EXPORTAR', 'citas', "Exportó reporte de citas.");
exit;
?>