<?php
// Determine the current page
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

        <a href="index.php" class="logo d-flex align-items-center">
            <img src="assets/img/ramcc.png" alt="Logo RAMCC" class="img-fluid">
            <!-- <h1 class="sitename"><b>Sistema de carga</b></h1> -->
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] && $_SESSION['super_admin'] == 1): ?>
                    <li><a href="admin.php" class="<?php echo ($current_page == 'admin') ? 'active' : ''; ?>">Usuarios</a></li>
                    <li><a href="admin-municipios.php" class="<?php echo ($current_page == 'admin-municipios') ? 'active' : ''; ?>">Municipios</a></li>
                <?php endif; ?>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                <?php if (isset($_SESSION['id_municipio']) && $_SESSION['id_municipio'] != null && $_SESSION['id_municipio'] != 0): ?>
                    <span class="me-3 fw-bold">Hola, <span class="text-success"><?php echo htmlspecialchars($_SESSION['nombre_municipio']); ?></span></span>
                <?php endif; ?>
                <a href="logout.php" class="btn-logout">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                </a>
            <?php else: ?>
                <a class="btn-getstarted" href="InicioDeSesion">Ingresar</a>
            <?php endif; ?>
        </div>

    </div>
</header>