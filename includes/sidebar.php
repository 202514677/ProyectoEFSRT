<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>Menu Principal</h3>
        <small><?= htmlspecialchars($_SESSION['usuario_rol']); ?></small>
    </div>
    <ul class="sidebar-menu">

    <li><a href="dashboard.php">Dashboard</a></li>

    <li><a href="pacientes.php">Pacientes</a></li>

    <li><a href="medicos.php">Médicos</a></li>

    <li>
        <a href="consultorios.php"
           style="border-left:4px solid #3498db;">
            Consultorios
        </a>
    </li>

    <li><a href="citas.php">Citas Médicas</a></li>
	
	<li><a href="reportes_pacientes.php">Reporte Pacientes</a></li>

	<li><a href="reportes_citas.php">Reporte Citas</a></li>

    <?php if ($_SESSION['usuario_rol'] === 'Administrador'): ?>

        <li>
            <a href="usuarios.php"
               style="background:#34495e;
               border-left:4px solid var(--secondary);">

               Gestionar Usuarios

            </a>
        </li>

        <li>
            <a href="auditoria.php"
               style="border-left:4px solid #f39c12;">

               Auditoría

            </a>
        </li>

    <?php endif; ?>

    <li>
        <a href="logout.php"
           style="color:#e74c3c;">
            Cerrar Sesión
        </a>
    </li>

</ul>
</div>