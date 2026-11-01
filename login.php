<?php
// 1. Mulai Session (Harus paling atas)
session_start();

// 2. Include koneksi database (Pastikan path ini benar)
include "config/database.php"; // Jika error, periksa lokasi file database.php

// 3. Inisialisasi pesan feedback
$login_message = ""; // Gunakan variabel berbeda agar tidak bentrok jika ada pesan registrasi nanti

// 4. Proses Login jika form di-submit (Method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_action'])) { // Tambahkan input tersembunyi untuk membedakan form

    // Ambil data dari form login (Gunakan filter/sanitasi dasar)
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validasi input dasar
    if (empty($username) || empty($password)) {
        $login_message = "<b style='color:red;'>Username dan Password wajib diisi.</b>";
    } else {
        // --- PERBAIKAN KEAMANAN: Gunakan Prepared Statements ---
        $sql = "SELECT username, password, fullname, role FROM user WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_assoc($result);

            if ($data) { // Jika user ditemukan
                // Verifikasi password
                if (password_verify($password, $data['password'])) {
                    // Password cocok - Set session
                    $_SESSION['username'] = $data['username'];
                    $_SESSION['fullname'] = $data['fullname'];
                    $_SESSION['role'] = $data['role'];

                    // Redirect ke halaman utama (index.php atau dashboard.php)
                    // Gunakan header() untuk redirect sisi server (lebih baik dari JS jika memungkinkan)
                    header("Location: index.php");
                    exit; // Hentikan eksekusi script setelah redirect

                    // Alternatif JS jika header tidak bekerja (misal sudah ada output sebelumnya)
                    // echo "<script> location.href='index.php' </script>";
                    // exit;

                } else {
                    // Password salah
                    $login_message = "<b style='color:red'>Password Salah</b>";
                }
            } else {
                // Username tidak terdaftar
                $login_message = "<b style='color:red;'>Username tidak terdaftar</b>";
            }
            mysqli_stmt_close($stmt); // Tutup statement
        } else {
            // Error saat mempersiapkan statement
            $login_message = "<b style='color:red;'>Terjadi kesalahan pada sistem. Coba lagi nanti.</b>";
            // Sebaiknya log error mysqli_error($conn) di sisi server
            error_log("Login prepare statement error: " . mysqli_error($conn));
        }
    }
}

// 5. Proses Registrasi
$register_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_action'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {

        $register_message = "<b style='color:red;'>Username dan Password wajib diisi.</b>";

    } else {

        // Cek username sudah ada atau belum
        $cek = mysqli_prepare($conn, "SELECT username FROM user WHERE username = ?");

        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $register_message = "<b style='color:red;'>Username sudah digunakan.</b>";

        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $fullname = $username;
            $role = "User";
            $active = "Yes";

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO user(username,password,fullname,role,active)
                 VALUES(?,?,?,?,?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $username,
                $hash,
                $fullname,
                $role,
                $active
            );

            if (mysqli_stmt_execute($stmt)) {

    die("REGISTRASI BERHASIL");

} else {

    die("ERROR : " . mysqli_error($conn));

}

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://kit.fontawesome.com/bb750d54aa.js" crossorigin="anonymous"></script>
    <!-- Pastikan path ke login.css benar -->
    <link rel="stylesheet" href="assets/css/login.css" />
    <title>Masuk / Daftar Akun - Sonit</title>

    <!-- Favicons (Pastikan path benar) -->
    <link href="assets/img/icons.jpg" rel="icon">

    <style>
        /* Style untuk pesan error/sukses login */
        .login-message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 0.9em;
            /* Warna akan diatur oleh inline style dari PHP */
            border: 1px solid transparent; /* Optional border */
        }
        .login-message b { /* Mengatur bold di dalam pesan */
            font-weight: 600;
        }
        /* Style untuk menyembunyikan label secara visual tapi tetap ada untuk screen reader */
        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
    </style>

</head>

<body>
    <!-- Class 'sign-in-mode' atau 'sign-up-mode' ditambahkan oleh JS bawaan template -->
    <div class="container">

        <!-- Tombol Kembali (Pastikan path landing.php benar) -->
        <a href="landing.php" class="back-to-home" aria-label="Kembali ke Halaman Utama">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>

        <div class="forms-container">
            <div class="signin-signup">
                <!-- ==== FORM LOGIN ==== -->
                <!-- action="" berarti submit ke halaman ini sendiri -->
                <form action="" method="POST" class="sign-in-form">
                    <input type="hidden" name="login_action" value="1"> <!-- Penanda form login -->
                    <h2 class="title">Masuk</h2>

                    <!-- Tampilkan Pesan Login Di Sini -->
                    <?php if (!empty($login_message)): ?>
                        <p class="login-message"><?php echo $login_message; ?></p>
                    <?php endif; ?>

                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <label for="signin-username" class="visually-hidden">Username</label>
                        <input type="text" placeholder="Username" id="signin-username" name="username" required
                            autocomplete="username" />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <label for="signin-password" class="visually-hidden">Password</label>
                        <input type="password" placeholder="Password" id="signin-password" name="password" required
                            autocomplete="current-password" />
                    </div>
                    <input type="submit" value="Login" class="btn solid" />

                    <!-- Opsi Lupa Password (jika ada) -->
                    <!-- <a href="lupa_password.php" style="margin-top: 10px; display: block; text-align: center;">Lupa Password?</a> -->

                    <p class="social-text">Atau masuk dengan platform sosial</p>
                    <div class="social-media">
                        <!-- Link sosial (belum fungsional) -->
                        <a href="#" class="social-icon" aria-label="Masuk dengan Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon" aria-label="Masuk dengan Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon" aria-label="Masuk dengan Google"><i class="fab fa-google"></i></a>
                        <a href="#" class="social-icon" aria-label="Masuk dengan LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </form>
                <!-- ==== AKHIR FORM LOGIN ==== -->


                <!-- ==== FORM REGISTRASI ==== -->
                <!-- action="" jika ingin proses di halaman ini juga, TAPI perlu logika PHP terpisah -->
                <form action="" method="POST" class="sign-up-form">
                    <input type="hidden" name="register_action" value="1"> <!-- Penanda form registrasi -->
                    <h2 class="title">Daftar</h2>

<?php if (!empty($register_message)): ?>
    <p class="login-message"><?php echo $register_message; ?></p>
<?php endif; ?>

                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <label for="signup-username" class="visually-hidden">Username</label>
                        <input type="text" placeholder="Username" id="signup-username" name="username" required autocomplete="username" />
                    </div>
                    <!-- Tambahkan input Fullname jika perlu -->
                    <!-- <div class="input-field">
                        <i class="fas fa-id-card"></i>
                        <label for="signup-fullname" class="visually-hidden">Nama Lengkap</label>
                        <input type="text" placeholder="Nama Lengkap" id="signup-fullname" name="fullname" required />
                    </div> -->
                    <!-- <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <label for="signup-email" class="visually-hidden">Email</label>
                        <input type="email" placeholder="Email" id="signup-email" name="email" required autocomplete="email" />
                    </div> -->
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <label for="signup-password" class="visually-hidden">Password</label>
                        <input type="password" placeholder="Password" id="signup-password" name="password" required autocomplete="new-password" />
                    </div>
                    <!-- Tambahkan konfirmasi password jika perlu -->
                    <!-- <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <label for="signup-password-confirm" class="visually-hidden">Konfirmasi Password</label>
                        <input type="password" placeholder="Konfirmasi Password" id="signup-password-confirm" name="password_confirm" required autocomplete="new-password" />
                    </div> -->
                    <input type="submit" class="btn" value="Daftar" />
                    <p class="social-text">Atau daftar dengan platform sosial</p>
                    <div class="social-media">
                        <!-- Link sosial (belum fungsional) -->
                        <a href="#" class="social-icon" aria-label="Daftar dengan Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon" aria-label="Daftar dengan Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon" aria-label="Daftar dengan Google"><i class="fab fa-google"></i></a>
                        <a href="#" class="social-icon" aria-label="Daftar dengan LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </form>
                <!-- ==== AKHIR FORM REGISTRASI ==== -->
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>Baru di Sini?</h3>
                    <p>Daftar sekarang untuk mulai memantau kualitas tanah dan otomatisasi penyiraman dengan data real-time kami.</p>
                    <button class="btn transparent" id="sign-up-btn">Daftar</button>
                </div>
                <!-- Pastikan path gambar benar -->
                <img src="assets/img/login.svg" class="image" alt="Ilustrasi Login" />
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <h3>Sudah Punya Akun?</h3>
                    <p>Masuk untuk mengakses dashboard pemantauan dan melihat data terbaru.</p>
                    <button class="btn transparent" id="sign-in-btn">Masuk</button>
                </div>
                <!-- Pastikan path gambar benar -->
                <img src="assets/img/reg.svg" class="image" alt="Ilustrasi Registrasi" />
            </div>
        </div>

    </div>

    <!-- Vendor JS Files (Pastikan path benar) -->
    <!-- <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script> -->

    <!-- Main JS File (PENTING untuk animasi panel - Pastikan path benar) -->
    <script src="assets/js/main.js"></script> <!-- Ganti nama jika file JS Anda berbeda -->

</body>

</html>