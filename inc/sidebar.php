<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link d-flex align-items-center">
        <!-- Hapus justify-content-center agar rata kiri default -->
        <img src="assets/img/abu.jpg" alt="Sonit Logo" class="brand-image"
            style="max-height: 33px; opacity: 1; margin-right: .5rem;">
        <span class="brand-text font-weight-bold">Sonit</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#"
                    class="d-block"><?php echo isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : 'Guest'; ?></a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="?page=dashboard"
                        class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="?page=grafik_sensor"
                        class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'grafik_sensor') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-chart-line"></i> <!-- Contoh ikon grafik garis -->
                        <p>Grafik Sensor</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="?page=datasensor"
                        class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'datasensor') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-database"></i>
                        <p>Data Sensor</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="?page=dataactuator"
                        class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'dataactuator') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-toggle-on"></i>
                        <p>Data Aktuator</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?page=device"
                        class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'device') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-server"></i>
                        <p>Perangkat</p>
                    </a>
                </li>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == "Admin"){ ?>
                <li class="nav-item">
                    <a href="?page=user"
                        class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'user') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Pengguna</p>
                    </a>
                </li>
                <?php } ?>

                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>