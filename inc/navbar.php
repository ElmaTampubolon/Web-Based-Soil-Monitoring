<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

        <!-- Link Kontak Anda -->
        <li class="nav-item d-none d-sm-inline-block">
            <a href="https://wa.me/082283728441" class="nav-link">Kontak</a>
        </li>

        <!-- ===== TAMBAHKAN INDIKATOR STATUS MQTT DI SINI ===== -->
        <li class="nav-item d-none d-sm-inline-block"> <!-- Tetap sembunyikan di layar xs -->
            <span id="mqtt-nav-status-container" class="nav-link" style="font-size: 0.8rem; padding-top: 0.7rem; padding-bottom: 0.7rem; cursor: default;" title="Status Koneksi Data Real-time">
                <i id="mqtt-nav-status-icon" class="fas fa-circle text-secondary mr-1"></i> <!-- Icon Awal: Abu-abu -->
                <span id="mqtt-nav-status-text">Menghubungkan...</span> <!-- Teks Awal -->
            </span>
        </li>
        <!-- ================================================== -->

    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- ... (sisa kode navbar kanan Anda tetap sama) ... -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <!-- ... form search ... -->
            </div>
        </li>
        <li class="nav-item dropdown">
            <!-- ... dropdown notifikasi ... -->
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
    </ul>
</nav>