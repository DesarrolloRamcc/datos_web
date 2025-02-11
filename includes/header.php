<?php
// Determine the current page
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

        <a href="index.php" class="logo d-flex align-items-center me-auto">
            <img src="assets/img/ramcc.png" alt="Logo RAMCC" class="img-fluid">
            <!-- <h1 class="sitename"><b>Sistema de carga</b></h1> -->
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] && $_SESSION['id_municipio'] == 1): ?>
                    <li><a href="admin.php" class="<?php echo ($current_page == 'admin') ? 'active' : ''; ?>">Usuarios</a></li>
                    <li><a href="admin-municipios.php" class="<?php echo ($current_page == 'admin-municipios') ? 'active' : ''; ?>">Municipios</a></li>
                <?php endif; ?>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <?php if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']): ?>
            <a class="btn-getstarted" href="InicioDeSesion">Ingresar</a>
        <?php else: ?>
            <a href="logout.php" class="btn-logout">
                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
            </a>
        <?php endif; ?>

    </div>
</header>