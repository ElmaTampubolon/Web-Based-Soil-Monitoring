<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Sonit - Soil Monitoring</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/icons.jpg" rel="icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div
      class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="landing.php" class="logo d-flex align-items-center me-auto me-xl-0">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="assets/img/logos.jpg" alt="">
        <h1 class="sitename"><b>Sonit</b></h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Beranda</a></li>
          <li><a href="#about">Tentang</a></li>
          <li><a href="#features">Fitur</a></li>
          <li><a href="#services">Servis</a></li>
          <li><a href="#contact">Kontak</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-signup" href="login.php">Sign Up</a>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row align-items-center">
          <div class="col-lg-6">
            <div class="hero-content" data-aos="fade-up" data-aos-delay="200">
              <div class="company-badge mb-4">
                <i class="bi bi-gear-fill me-2"></i>
                Data Akurat untuk Keputusan yang Tepat
              </div>

              <h1 class="mb-4">
                Monitoring Kualitas Tanah dan Otomatisasi Penyiraman Vitamin <br>
                <span class="accent-text">Secara Real-Time</span>
              </h1>

              <p class="mb-4 mb-md-5">
                Kami memantau kualitas tanah secara komprehensif dan penyiraman vitamin untuk memberikan Anda data yang akurat dan
                terpercaya.
                Dengan informasi ini, Anda dapat memahami kondisi dan berkontribusi dalam menjaga kelestariannya.
              </p>

              <div class="hero-buttons">
                <a href="login.php" class="btn btn-primary me-0 me-sm-2 mx-1">Sign In</a>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="hero-image" data-aos="zoom-out" data-aos-delay="300">
              <img src="assets/img/illustration-1.webp" alt="Hero Image" class="img-fluid">
            </div>
          </div>
        </div>

        <div class="row stats-row gy-4 mt-5" data-aos="fade-up" data-aos-delay="500">
          <div class="col-lg-3 col-md-6">
            <div class="stat-item">
              <div class="stat-icon">
                <i class="bi bi-globe-americas"></i>
              </div>
              <div class="stat-content">
                <h4>Diakui Global</h4>
                <p class="mb-0">Menunjukkan Standar</p>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-item">
              <div class="stat-icon">
                <i class="bi bi-droplet-half"></i>
              </div>
              <div class="stat-content">
                <h4>Sampel Tanah</h4>
                <p class="mb-0">Organik dan Anorganik</p>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-item">
              <div class="stat-icon">
                <i class="bi bi-wifi"></i>
              </div>
              <div class="stat-content">
                <h4>Data Real-time</h4>
                <p class="mb-0">Konektivitas dan Asli</p>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-item">
              <div class="stat-icon">
                <i class="bi bi-shield-check"></i>
              </div>
              <div class="stat-content">
                <h4>Keamanan Data</h4>
                <p class="mb-0">Perlindungan Data</p>
              </div>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4 align-items-center justify-content-between">

          <div class="col-xl-5" data-aos="fade-up" data-aos-delay="200">
            <span class="about-meta">Tentang Kami</span>
            <h2 class="about-title">Informasi Kualitas Tanah dan Otomatisasi Penyiraman Vitamin Terkini</h2>
            <p class="about-description">Dapatkan data real-time dan analisis mendalam tentang kondisi tanah dan otomatisasi penyiraman vitamin untuk
              membantu pengambilan keputusan dan upaya pelestarian.</p>

            <div class="row feature-list-wrapper">
              <div class="col-md-6">
                <ul class="feature-list">
                  <li><i class="bi bi-check-circle-fill"></i> Data Real-time</li>
                  <li><i class="bi bi-check-circle-fill"></i> Analisis Komprehensif</li>
                  <li><i class="bi bi-check-circle-fill"></i> Akses Mudah</li>
                </ul>
              </div>
              <div class="col-md-6">
                <ul class="feature-list">
                  <li><i class="bi bi-check-circle-fill"></i> Laporan Terperinci</li>
                  <li><i class="bi bi-check-circle-fill"></i> Visualisasi Data</li>
                  <li><i class="bi bi-check-circle-fill"></i> Dukungan Teknis</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-xl-6" data-aos="fade-up" data-aos-delay="300">
            <div class="image-wrapper">
              <div class="images position-relative" data-aos="zoom-out" data-aos-delay="500">
                <img src="assets/img/tanah-1.jpg" alt="Business Meeting" class="img-fluid main-image rounded-4">
                <img src="assets/img/about-2.jpg" alt="Team Discussion" class="img-fluid small-image rounded-4">
              </div>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Fitur</h2>
        <p>Informasi lengkap dan alat yang Anda butuhkan untuk memantau kualitas tanah dan otomatisasi penyiraman vitamin.</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="d-flex justify-content-center">

          <ul class="nav nav-tabs" data-aos="fade-up" data-aos-delay="100">

            <li class="nav-item">
              <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#features-tab-1">
                <h4>Data Real-Time</h4>
              </a>
            </li><!-- End tab nav item -->

            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-2">
                <h4>Analisis Mendalam</h4>
              </a><!-- End tab nav item -->

            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-3">
                <h4>Laporan & Visualisasi</h4>
              </a>
            </li><!-- End tab nav item -->

          </ul>

        </div>

        <div class="tab-content" data-aos="fade-up" data-aos-delay="200">

          <div class="tab-pane fade active show" id="features-tab-1">
            <div class="row">
              <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0 d-flex flex-column justify-content-center">
                <h3>Pantau Kondisi Tanah Secara Langsung</h3>
                <p class="fst-italic">
                  Dapatkan data kualitas tanah terbaru dari 1 titik pemantauan di suatu wilayah yang dideteksi.
                </p>
                <ul>
                  <li><i class="bi bi-check2-all"></i> <span>Data diperbarui setiap waktu.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Notifikasi peringatan dini.</span></li>
                </ul>
              </div>
              <div class="col-lg-6 order-1 order-lg-2 text-center">
                <img src="assets/img/features-illustration-1.webp" alt="" class="img-fluid">
              </div>
            </div>
          </div><!-- End tab content item -->

          <div class="tab-pane fade" id="features-tab-2">
            <div class="row">
              <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0 d-flex flex-column justify-content-center">
                <h3>Identifikasi Tren dan Potensi Masalah</h3>
                <p class="fst-italic">
                  Alat analisis canggih untuk memahami pola, mendeteksi anomali, dan memprediksi perubahan kualitas tanah.
                </p>
                <ul>
                  <li><i class="bi bi-check2-all"></i> <span>Analisis tren historis.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Deteksi anomali otomatis.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Pemodelan prediktif.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Identifikasi</span></li>
                </ul>
              </div>
              <div class="col-lg-6 order-1 order-lg-2 text-center">
                <img src="assets/img/features-illustration-2.webp" alt="" class="img-fluid">
              </div>
            </div>
          </div><!-- End tab content item -->

          <div class="tab-pane fade" id="features-tab-3">
            <div class="row">
              <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0 d-flex flex-column justify-content-center">
                <h3>Informasi yang Mudah Dipahami</h3>
                <ul>
                  <li><i class="bi bi-check2-all"></i> <span>Laporan kualitas tanah yang komprehensif.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Laporan otomatisasi penyiraman vitamin.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Visualisasi data interaktif.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Data dapat diunduh dalam berbagai format.</span></li>
                </ul>
                <p class="fst-italic">
                  Sajikan data kualitas tanah dan otomatisasi penyiraman vitamin dalam format yang jelas dan mudah diakses untuk semua pengguna.
                </p>
              </div>
              <div class="col-lg-6 order-1 order-lg-2 text-center">
                <img src="assets/img/features-illustration-3.webp" alt="" class="img-fluid">
              </div>
            </div>
          </div><!-- End tab content item -->

        </div>

      </div>

    </section><!-- /Features Section -->

    <!-- Features Cards Section -->
    <section id="features-cards" class="features-cards section">

      <div class="container">

        <div class="row gy-4">

          <div class="col-xl-3 col-md-6" data-aos="zoom-in" data-aos-delay="100">
            <div class="feature-box orange">
              <i class="bi bi-award"></i>
              <h4>Standar Mutu Tinggi</h4>
              <p>Data kualitas tanah teruji dan divalidasi sesuai standar pemantauan lingkungan.</p>
            </div>
          </div><!-- End Feature Borx-->

          <div class="col-xl-3 col-md-6" data-aos="zoom-in" data-aos-delay="200">
            <div class="feature-box blue">
              <i class="bi bi-patch-check"></i>
              <h4>Data Terverifikasi</h4>
              <p>Informasi akurat dan terverifikasi untuk mendukung keputusan strategis Anda.</p>
            </div>
          </div><!-- End Feature Borx-->

          <div class="col-xl-3 col-md-6" data-aos="zoom-in" data-aos-delay="300">
            <div class="feature-box green">
              <i class="bi bi-sunrise"></i>
              <h4>Peringatan Dini</h4>
              <p>Deteksi perubahan kualitas tanah dan otomatisasi penyiraman vitamin secara cepat untuk tindakan pencegahan.</p>
            </div>
          </div><!-- End Feature Borx-->

          <div class="col-xl-3 col-md-6" data-aos="zoom-in" data-aos-delay="400">
            <div class="feature-box red">
              <i class="bi bi-shield-check"></i>
              <h4>Keamanan Data</h4>
              <p>Platform aman dan andal untuk melindungi integritas data kualitas tanah Anda.</p>
            </div>
          </div><!-- End Feature Borx-->

        </div>

      </div>

    </section><!-- /Features Cards Section -->

    <!-- Features 2 Section -->
    <section id="features-2" class="features-2 section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row align-items-center">

          <div class="col-lg-4">

            <div class="feature-item text-end mb-5" data-aos="fade-right" data-aos-delay="200">
              <div class="d-flex align-items-center justify-content-end gap-4">
                <div class="feature-content">
                  <h3>Soil Sensor (NPKPHTHEC)</h3>
                  <p>Mengukur Natrium, Fosfor, Kalium, pH, Suhu, Kelembapan dan EC Tanah </p>
                </div>
                <div class="feature-icon flex-shrink-0">
                  <i class="bi bi-segmented-nav"></i>
                </div>
              </div>
            </div><!-- End .feature-item -->

            <div class="feature-item text-end mb-5" data-aos="fade-right" data-aos-delay="300">
              <div class="d-flex align-items-center justify-content-end gap-4">
                <div class="feature-content">
                  <h3>Sensor SHT20</h3>
                  <p>Pemantauan Suhu dan Kelembapan Lingkungan Sekitar</p>
                </div>
                <div class="feature-icon flex-shrink-0">
                  <i class="bi bi-thermometer-half"></i>
                </div>
              </div>
            </div><!-- End .feature-item -->

            <div class="feature-item text-end" data-aos="fade-right" data-aos-delay="600">
              <div class="d-flex align-items-center justify-content-end gap-4">
                <div class="feature-content">
                  <h3>Sensor HC-SR04 Ultrasonik</h3>
                  <p>Mengukur level cairan dalam galon untuk memantau ketersediaan.</p>
                </div>
                <div class="feature-icon flex-shrink-0">
                  <i class="bi bi-cloud-sun"></i>
                </div>
              </div>
            </div><!-- End .feature-item -->

          </div>

          <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="2000">
            <div class="phone-mockup text-center">
              <img src="assets/img/desainjadi.png" alt="Alat Monitoring Kualitas Tanah" class="img-fluid">
            </div>
          </div><!-- End Phone Mockup -->

          <div class="col-lg-4">

            <div class="feature-item mb-5" data-aos="fade-left" data-aos-delay="200">
              <div class="d-flex align-items-center gap-4">
                <div class="feature-icon flex-shrink-0">
                  <i class="bi bi-wind"></i>
                </div>
                <div class="feature-content">
                  <h3>Sensor Tekanan Air</h3>
                  <p>Memantau tekanan aliran larutan vitamin di dalam selang penyiraman</p>
                </div>
              </div>
            </div><!-- End .feature-item -->

            <div class="feature-item mb-5" data-aos="fade-left" data-aos-delay="300">
              <div class="d-flex align-items-center gap-4">
                <div class="feature-icon flex-shrink-0">
                  <i class="bi bi-grid-3x3-gap-fill"></i>
                </div>
                  <div class="feature-content">
                  <h3>LCD</h3>
                  <p>Menampilkan Output Perameter Pengukuran</p>
                </div>
              </div>
            </div><!-- End .feature-item -->

          </div>
        </div>

      </div>

    </section><!-- /Features 2 Section -->

    <!-- Services Section -->
    <section id="services" class="services section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Servis</h2>
        <p>Solusi lengkap untuk pemantauan, analisis, dan pelaporan kualitas tanah dan penyiraman otomatis.</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-4">

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-card d-flex">
              <div class="icon flex-shrink-0">
                <i class="bi bi-activity"></i>
              </div>
              <div>
                <h3>Pemantauan Real-Time</h3>
                <p>Akses data kualitas tanah terkini dari sensor kami yang terpasang di lokasi.</p>
              </div>
            </div>
          </div><!-- End Service Card -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-card d-flex">
              <div class="icon flex-shrink-0">
                <i class="bi bi-diagram-3"></i>
              </div>
              <div>
                <h3>Analisis Data Lanjutan</h3>
                <p>Pahami tren, deteksi anomali, dan dapatkan wawasan mendalam dari data kualitas tanah historis.</p>
              </div>
            </div>
          </div><!-- End Service Card -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-card d-flex">
              <div class="icon flex-shrink-0">
                <i class="bi bi-easel"></i>
              </div>
              <div>
                <h3>Laporan & Visualisasi</h3>
                <p>Dapatkan laporan komprehensif dan visualisasi data yang mudah dipahami untuk pengambilan keputusan.
                </p>
              </div>
            </div>
          </div><!-- End Service Card -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-card d-flex">
              <div class="icon flex-shrink-0">
                <i class="bi bi-clipboard-data"></i>
              </div>
              <div>
                <h3>Akses Data Fleksibel</h3>
                <p>Unduh data mentah atau terolah dalam berbagai format melalui platform web atau API kami.</p>
              </div>
            </div>
          </div><!-- End Service Card -->

        </div>

      </div>

    </section><!-- /Services Section -->

    <!-- Faq Section -->
    <section class="faq-9 faq section light-background" id="faq">

      <div class="container">
        <div class="row">

          <div class="col-lg-5" data-aos="fade-up">
            <h2 class="faq-title">Punya Pertanyaan? Cek FAQ Kami</h2>
            <p class="faq-description">Temukan jawaban cepat untuk pertanyaan umum seputar platform monitoring kualitas
              tanah kami.</p>
            <div class="faq-arrow d-none d-lg-block" data-aos="fade-up" data-aos-delay="200">
              <svg class="faq-arrow" width="200" height="211" viewBox="0 0 200 211" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M198.804 194.488C189.279 189.596 179.529 185.52 169.407 182.07L169.384 182.049C169.227 181.994 169.07 181.939 168.912 181.884C166.669 181.139 165.906 184.546 167.669 185.615C174.053 189.473 182.761 191.837 189.146 195.695C156.603 195.912 119.781 196.591 91.266 179.049C62.5221 161.368 48.1094 130.695 56.934 98.891C84.5539 98.7247 112.556 84.0176 129.508 62.667C136.396 53.9724 146.193 35.1448 129.773 30.2717C114.292 25.6624 93.7109 41.8875 83.1971 51.3147C70.1109 63.039 59.63 78.433 54.2039 95.0087C52.1221 94.9842 50.0776 94.8683 48.0703 94.6608C30.1803 92.8027 11.2197 83.6338 5.44902 65.1074C-1.88449 41.5699 14.4994 19.0183 27.9202 1.56641C28.6411 0.625793 27.2862 -0.561638 26.5419 0.358501C13.4588 16.4098 -0.221091 34.5242 0.896608 56.5659C1.8218 74.6941 14.221 87.9401 30.4121 94.2058C37.7076 97.0203 45.3454 98.5003 53.0334 98.8449C47.8679 117.532 49.2961 137.487 60.7729 155.283C87.7615 197.081 139.616 201.147 184.786 201.155L174.332 206.827C172.119 208.033 174.345 211.287 176.537 210.105C182.06 207.125 187.582 204.122 193.084 201.144C193.346 201.147 195.161 199.887 195.423 199.868C197.08 198.548 193.084 201.144 195.528 199.81C196.688 199.192 197.846 198.552 199.006 197.935C200.397 197.167 200.007 195.087 198.804 194.488ZM60.8213 88.0427C67.6894 72.648 78.8538 59.1566 92.1207 49.0388C98.8475 43.9065 106.334 39.2953 114.188 36.1439C117.295 34.8947 120.798 33.6609 124.168 33.635C134.365 33.5511 136.354 42.9911 132.638 51.031C120.47 77.4222 86.8639 93.9837 58.0983 94.9666C58.8971 92.6666 59.783 90.3603 60.8213 88.0427Z"
                  fill="currentColor"></path>
              </svg>
            </div>
          </div>

          <div class="col-lg-7" data-aos="fade-up" data-aos-delay="300">
            <div class="faq-container">

              <div class="faq-item faq-active">
                <h3>Apa saja parameter kualitas tanah yang dipantau?</h3>
                <div class="faq-content">
                  <p>Kami memantau parameter kunci seperti NPK, pH, suhu, kelembapan, Otomatisasi Penyiraman Vitamin.
                     Detail lengkap tersedia di bagian Metodologi.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Seberapa sering data kualitas tanah diperbarui?</h3>
                <div class="faq-content">
                  <p>Data diperbarui secara real-time dari sensor kami, biasanya setiap 15 menit hingga 1 jam,
                    tergantung lokasi dan kondisi jaringan.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Bagaimana cara mengakses data kualitas tanah?</h3>
                <div class="faq-content">
                  <p>Anda dapat melihat data melalui dashboard interaktif di website ini. Pengguna terdaftar juga dapat
                    mengunduh data atau mengakses melalui API.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Apakah data yang ditampilkan akurat dan terpercaya?</h3>
                <div class="faq-content">
                  <p>Ya, kami menggunakan sensor terkalibrasi dan melakukan validasi data berkala untuk memastikan
                    akurasi dan keandalan informasi yang disajikan.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Siapa saja yang bisa memanfaatkan data ini?</h3>
                <div class="faq-content">
                  <p>Data ini bermanfaat bagi pemerintah, peneliti, industri, komunitas peduli lingkungan, dan
                    masyarakat umum yang ingin mengetahui kondisi tanah.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Bagaimana jika saya butuh bantuan atau menemukan masalah?</h3>
                <div class="faq-content">
                  <p>Silakan hubungi tim dukungan kami melalui halaman Kontak Kami atau kirim email ke [alamat email
                    dukungan]. Kami siap membantu Anda.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

            </div>
          </div>

        </div>
      </div>
    </section><!-- /Faq Section -->

    <!-- Call To Action 2 Section -->
    <section id="call-to-action-2" class="call-to-action-2 section dark-background">

      <div class="container">
        <div class="row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
          <div class="col-xl-10">
            <div class="text-center">
              <h3>Jadilah Bagian dari Solusi</h3>
              <p>Dapatkan akses ke data kualitas tanah terkini dan berkontribusi dalam menjaga kelestarian
                lingkungan kita.</p>
              <a class="cta-btn" href="login.php">Daftar Sekarang</a>
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Call To Action 2 Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Kontak</h2>
        <p>Hubungi kami jika Anda memiliki pertanyaan atau membutuhkan informasi lebih lanjut.</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-4 g-lg-5">
          <div class="col-lg-5">
            <div class="info-box" data-aos="fade-up" data-aos-delay="200">
              <h3>Informasi Kontak</h3>
              <p>Silakan hubungi kami melalui detail di bawah ini untuk informasi lebih lanjut.</p>

              <div class="info-item" data-aos="fade-up" data-aos-delay="300">
                <div class="icon-box">
                  <i class="bi bi-geo-alt"></i>
                </div>
                <div class="content">
                  <h4>Lokasi Kami</h4>
                  <p>Jl. Jepun 1</p>
                  <p>Bali, Indonesia</p>
                </div>
              </div>

              <div class="info-item" data-aos="fade-up" data-aos-delay="400">
                <div class="icon-box">
                  <i class="bi bi-telephone"></i>
                </div>
                <div class="content">
                  <h4>Nomor Telepon</h4>
                  <p>+62 82283728441</p>
                </div>
              </div>

              <div class="info-item" data-aos="fade-up" data-aos-delay="500">
                <div class="icon-box">
                  <i class="bi bi-envelope"></i>
                </div>
                <div class="content">
                  <h4>Alamat Email</h4>
                  <p>aeyelma@gmail.com</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-7">
            <div class="contact-form" data-aos="fade-up" data-aos-delay="300">
              <h3>Hubungi Kami</h3>
              <p>Gunakan formulir di bawah ini untuk mengirimkan pertanyaan atau masukan Anda.</p>
              <!-- Lanjutan kode formulir -->

              <form action="forms/contact.php" method="post" class="php-email-form" data-aos="fade-up"
                data-aos-delay="200">
                <div class="row gy-4">

                  <div class="col-md-6">
                    <input type="text" name="name" class="form-control" placeholder="Your Name" required="">
                  </div>

                  <div class="col-md-6 ">
                    <input type="email" class="form-control" name="email" placeholder="Your Email" required="">
                  </div>

                  <div class="col-12">
                    <input type="text" class="form-control" name="subject" placeholder="Subject" required="">
                  </div>

                  <div class="col-12">
                    <textarea class="form-control" name="message" rows="6" placeholder="Message" required=""></textarea>
                  </div>

                  <div class="col-12 text-center">
                    <div class="loading">Loading</div>
                    <div class="error-message"></div>
                    <div class="sent-message">Your message has been sent. Thank you!</div>

                    <button type="submit" class="btn">Send Message</button>
                  </div>

                </div>
              </form>

            </div>
          </div>

        </div>

      </div>

    </section><!-- /Contact Section -->

  </main>

  <footer id="footer" class="footer">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="landing.php" class="logo d-flex align-items-center">
            <span class="sitename">Sonit</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Jl. Jepun 1</p>
            <p>Bali, Indonesia</p>
            <p class="mt-3"><strong>Telpon:</strong> <span>+62 82283728441</span></p>
            <p><strong>Email:</strong> <span>aeyelma@gmail.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Link Menu</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">Features</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Contacts</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Layanan Kami</h4>
          <ul>
            <li><a href="#">Pemantauan Real-Time</a></li>
            <li><a href="#">Analisis Data</a></li>
            <li><a href="#">Laporan Kualitas Air</a></li>
            <li><a href="#">Peta Interaktif</a></li>
            <li><a href="#">Peringatan Dini</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <!-- Kolom Informasi -->
          <h4>Informasi Penting</h4>
          <ul>
            <li><a href="#">Tentang Kami</a></li>
            <li><a href="#">Metodologi</a></li>
            <li><a href="#">Sumber Data</a></li>
            <li><a href="#">Berita Terkini</a></li>
            <li><a href="#">Studi Kasus</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <!-- Kolom Bantuan/Legal -->
          <h4>Bantuan & Legal</h4>
          <ul>
            <li><a href="#">FAQ</a></li>
            <li><a href="#">Kontak Kami</a></li>
            <li><a href="#">Kebijakan Privasi</a></li>
            <li><a href="#">Syarat & Ketentuan</a></li>
          </ul>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Sonit</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        Designed by <a href="#">Elma Ruis</a>
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>