<?php
require_once 'config/conexion.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$citasHoy = $pdo->query("SELECT COUNT(*) FROM citas WHERE fecha_cita = CURDATE()")->fetchColumn();
$totalPacientes = $pdo->query("SELECT COUNT(*) FROM pacientes")->fetchColumn();
$totalMedicos = $pdo->query("SELECT COUNT(*) FROM medicos")->fetchColumn();
?>
<div class="main-content">
    <h2>Bienvenido al ERP, <?= htmlspecialchars($_SESSION['usuario_nom']); ?></h2>
    <p>Resumen operacional de la miniclínica.</p>

    <div class="cards-grid">
        <div class="card" style="border-left: 5px solid #18bc9c;">
            <h4>Citas para Hoy</h4>
            <p style="font-size: 2rem; font-weight: bold;"><?= $citasHoy; ?></p>
        </div>
        <div class="card" style="border-left: 5px solid #3498db;">
            <h4>Pacientes Registrados</h4>
            <p style="font-size: 2rem; font-weight: bold;"><?= $totalPacientes; ?></p>
        </div>
        <div class="card" style="border-left: 5px solid #f39c12;">
            <h4>Cuerpo Médico</h4>
            <p style="font-size: 2rem; font-weight: bold;"><?= $totalMedicos; ?></p>
        </div>
    </div>

    <div class="table-responsive" style="margin-top: 2rem;">
        <h3>Últimos Accesos y Movimientos del Sistema (Logs de Auditoría)</h3>
        <table>
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Tabla</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $logs = $pdo->query("SELECT l.*, u.username FROM logs_sistema l LEFT JOIN usuarios u ON l.id_usuario = u.id_usuario ORDER BY l.id_log DESC LIMIT 5")->fetchAll();
                foreach($logs as $log):
                ?>
                <tr>
                    <td><?= $log['fecha_registro']; ?></td>
                    <td><?= htmlspecialchars($log['username'] ?? 'Sistema'); ?></td>
                    <td><strong><?= htmlspecialchars($log['accion']); ?></strong></td>
                    <td><?= htmlspecialchars($log['tabla_afectada'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($log['detalles']); ?></td>
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