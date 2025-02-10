<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

        <a href="index.php" class="logo d-flex align-items-center me-auto">
            <!-- <img src="./assets/img/aula-logo.png" alt="E-Learning Logo"> -->
            <h1 class="sitename"><b>Sistema de carga - RAMCC</b></h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
            </ul>
            <!-- <i class="mobile-nav-toggle d-xl-none bi bi-list"></i> -->
        </nav>

        <a class="btn-getstarted" href="InicioDeSesion">Ingresar</a>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
            <a href="logout.php" class="btn-logout">
                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
            </a>
        <?php endif; ?>

    </div>
</header>