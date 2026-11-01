<?php
// index.php
session_start(); // Mulai sesi di awal

// --- Koneksi Database ---
require_once "config/database.php"; // Koneksi DB selalu di awal

// === PENGECEKAN KONEKSI DATABASE GLOBAL ===
// Jika koneksi gagal (sesuai logika di config/database.php), tampilkan pesan error dan hentikan
if (!$conn) {
    http_response_code(503); // Service Unavailable
    // Output HTML dasar jika koneksi DB gagal fatal
    echo "<!DOCTYPE html><html><head><title>Error Koneksi Database</title></head><body>";
    echo "<h1>Tidak Dapat Terhubung ke Database</h1>";
    echo "<p>Mohon maaf, terjadi masalah saat menghubungkan ke database. Silakan coba lagi nanti atau hubungi administrator.</p>";
    echo "</body></html>";
    // Error sudah dicatat di config/database.php, jadi tidak perlu error_log() lagi di sini.
    exit; // Hentikan semua eksekusi lebih lanjut
}

// --- Inisialisasi variabel global atau pesan feedback dari session ---
$pesan_feedback_global = null;
if (isset($_SESSION['pesan_feedback'])) {
    $pesan_feedback_global = $_SESSION['pesan_feedback'];
    unset($_SESSION['pesan_feedback']); // Hapus dari session setelah diambil
}

// Tentukan halaman yang akan ditampilkan berdasarkan parameter GET 'page'
$page_saat_ini = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'dashboard';

// ======================================================================
// === PENANGANAN AKSI GET (SEPERTI DELETE) SEBELUM OUTPUT HTML ===
// ======================================================================

// --- AKSI DELETE PERANGKAT ---
if (isset($_GET['action']) && $_GET['action'] === 'delete_device' && isset($_GET['sn'])) {
    // Pastikan aksi delete hanya untuk halaman device dan hanya jika pengguna login
    if ($page_saat_ini === 'device' && isset($_SESSION['username'])) {
        $sn_to_delete = htmlspecialchars(trim($_GET['sn'])); // Sanitasi input GET SN

        // === PENGECEKAN ROLE ADMIN UNTUK DELETE PERANGKAT ===
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin") {
            $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Akses Ditolak!', 'isi' => 'Anda tidak memiliki izin untuk menghapus perangkat. Hanya Admin yang diizinkan.'];
        } else {
            $sql_delete_device = "DELETE FROM devices WHERE serial_number = ?";
            $stmt_delete_device = mysqli_prepare($conn, $sql_delete_device);

            if ($stmt_delete_device) {
                mysqli_stmt_bind_param($stmt_delete_device, "s", $sn_to_delete);
                if (mysqli_stmt_execute($stmt_delete_device)) {
                    if (mysqli_stmt_affected_rows($stmt_delete_device) > 0) {
                        $sql_delete_data = "DELETE FROM data WHERE serial_number = ?";
                        $stmt_delete_data = mysqli_prepare($conn, $sql_delete_data);
                        if ($stmt_delete_data) {
                            mysqli_stmt_bind_param($stmt_delete_data, "s", $sn_to_delete);
                            mysqli_stmt_execute($stmt_delete_data);
                            mysqli_stmt_close($stmt_delete_data);
                             // error_log("Data for SN $sn_to_delete deleted from 'data' table.");
                        } else {
                            error_log("Failed to prepare delete data query for SN $sn_to_delete: " . mysqli_error($conn));
                        }

                         // --- Opsional: Hapus juga sensor-sensor yang didaftarkan untuk perangkat ini ---
                        $sql_delete_sensors = "DELETE FROM device_sensors WHERE device_serial_number = ?";
                        $stmt_delete_sensors = mysqli_prepare($conn, $sql_delete_sensors);
                        if ($stmt_delete_sensors) {
                            mysqli_stmt_bind_param($stmt_delete_sensors, "s", $sn_to_delete);
                            mysqli_stmt_execute($stmt_delete_sensors);
                            mysqli_stmt_close($stmt_delete_sensors);
                              // error_log("Sensors for SN $sn_to_delete deleted from 'device_sensors' table.");
                        } else {
                            error_log("Failed to prepare delete sensors query for SN $sn_to_delete: " . mysqli_error($conn));
                        }
                         // --- Akhir Optional Deletion ---

                        $_SESSION['pesan_feedback'] = ['tipe' => 'success', 'judul' => 'Sukses!', 'isi' => "Perangkat dengan SN '" . $sn_to_delete . "' berhasil dihapus."];
                    } else {
                        $_SESSION['pesan_feedback'] = ['tipe' => 'warning', 'judul' => 'Info!', 'isi' => "Perangkat dengan SN '" . $sn_to_delete . "' tidak ditemukan atau sudah dihapus."];
                    }
                } else {
                    $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Database!', 'isi' => 'Gagal menghapus perangkat: ' . mysqli_stmt_error($stmt_delete_device)];
                    error_log("Device Delete Execute Error: " . mysqli_stmt_error($stmt_delete_device) . " | SN: " . $sn_to_delete);
                }
                mysqli_stmt_close($stmt_delete_device);
            } else {
                $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Server!', 'isi' => 'Gagal mempersiapkan statement hapus perangkat: ' . mysqli_error($conn)];
                error_log("Device Delete Prepare Error: " . mysqli_error($conn));
            }
        }
        // Redirect kembali ke halaman list device setelah aksi (sukses atau gagal)
        // header() harus dipanggil SEBELUM output HTML
        header("Location: ?page=device");
        exit; // Hentikan eksekusi setelah redirect

    } else {
        // Jika parameter 'action' delete_device ada tapi bukan halaman 'device' atau tidak login
        $_SESSION['pesan_feedback'] = ['tipe' => 'warning', 'judul' => 'Aksi Tidak Valid!', 'isi' => 'Aksi penghapusan perangkat tidak diizinkan dari halaman ini atau Anda belum login.'];
        header("Location: ?page=dashboard"); // Redirect ke dashboard
        exit;
    }
}

// --- AKSI DELETE USER ---
elseif (isset($_GET['action']) && $_GET['action'] === 'delete_user' && isset($_GET['username'])) {
    // Pastikan aksi delete hanya untuk halaman user dan hanya jika pengguna login
    if ($page_saat_ini === 'user' && isset($_SESSION['username'])) {
        $username_to_delete = htmlspecialchars(trim($_GET['username']));

        // 1. Pengecekan Role Admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin") {
            $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Akses Ditolak!', 'isi' => 'Anda tidak memiliki izin untuk menghapus pengguna.'];
        } else {
            // 2. Pengecekan agar Admin tidak menghapus dirinya sendiri
            if ($_SESSION['username'] === $username_to_delete) {
                $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Aksi Ditolak!', 'isi' => 'Anda tidak dapat menghapus akun Anda sendiri.'];
            } else {
                // Lanjutkan dengan proses delete jika valid
                $sql_delete_user = "DELETE FROM user WHERE username = ?";
                $stmt_delete_user = mysqli_prepare($conn, $sql_delete_user);

                if ($stmt_delete_user) {
                    mysqli_stmt_bind_param($stmt_delete_user, "s", $username_to_delete);
                    if (mysqli_stmt_execute($stmt_delete_user)) {
                        if (mysqli_stmt_affected_rows($stmt_delete_user) > 0) {
                            $_SESSION['pesan_feedback'] = ['tipe' => 'success', 'judul' => 'Sukses!', 'isi' => "Pengguna '" . $username_to_delete . "' berhasil dihapus."];
                        } else {
                            $_SESSION['pesan_feedback'] = ['tipe' => 'warning', 'judul' => 'Info!', 'isi' => "Pengguna '" . $username_to_delete . "' tidak ditemukan atau sudah dihapus."];
                        }
                    } else {
                        $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Database!', 'isi' => 'Gagal menghapus pengguna: ' . mysqli_stmt_error($stmt_delete_user)];
                        error_log("User Delete Execute Error: " . mysqli_stmt_error($stmt_delete_user) . " | Username: " . $username_to_delete);
                    }
                    mysqli_stmt_close($stmt_delete_user);
                } else {
                    $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Server!', 'isi' => 'Gagal mempersiapkan statement hapus pengguna: ' . mysqli_error($conn)];
                    error_log("User Delete Prepare Error: " . mysqli_error($conn));
                }
            }
        }
        // Redirect kembali ke halaman user setelah aksi
        header("Location: ?page=user");
        exit; // Hentikan eksekusi setelah redirect

    } else {
        // Jika parameter 'action' delete_user ada tapi bukan halaman 'user' atau tidak login
        $_SESSION['pesan_feedback'] = ['tipe' => 'warning', 'judul' => 'Aksi Tidak Valid!', 'isi' => 'Aksi penghapusan pengguna tidak diizinkan dari halaman ini atau Anda belum login.'];
        header("Location: ?page=dashboard"); // Redirect ke dashboard
        exit;
    }
}

// === TAMBAHKAN INI: AKSI DELETE SENSOR DARI PAGE DEVICE_SENSORS ===
elseif ($page_saat_ini === 'device_sensors' && isset($_GET['action']) && $_GET['action'] === 'delete_sensor' && isset($_GET['id']) && isset($_GET['device_sn'])) {
     // Pastikan pengguna login
    if (isset($_SESSION['username'])) {
        $sensor_id_to_delete = intval($_GET['id']); // ID sensor adalah integer
        $device_sn_parent = htmlspecialchars(trim($_GET['device_sn'])); // SN perangkat induk

        // === PENGECEKAN ROLE ADMIN (jika diperlukan untuk delete sensor) ===
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin") {
            $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Akses Ditolak!', 'isi' => 'Anda tidak memiliki izin untuk menghapus sensor. Hanya Admin yang diizinkan.'];
        } else {
             // Query DELETE ke tabel device_sensors
             // Menggunakan $conn yang sudah dipastikan valid di atas
            $sql_delete_sensor = "DELETE FROM device_sensors WHERE id = ? AND device_serial_number = ?";
            $stmt_delete_sensor = mysqli_prepare($conn, $sql_delete_sensor);

            if ($stmt_delete_sensor) {
                // Bind parameter: 'i' untuk integer ID, 's' untuk string SN
                mysqli_stmt_bind_param($stmt_delete_sensor, "is", $sensor_id_to_delete, $device_sn_parent);
                if (mysqli_stmt_execute($stmt_delete_sensor)) {
                    if (mysqli_stmt_affected_rows($stmt_delete_sensor) > 0) {
                        $_SESSION['pesan_feedback'] = ['tipe' => 'success', 'judul' => 'Sukses!', 'isi' => "Sensor berhasil dihapus."];
                    } else {
                        $_SESSION['pesan_feedback'] = ['tipe' => 'warning', 'judul' => 'Info!', 'isi' => "Sensor tidak ditemukan atau sudah dihapus."];
                    }
                } else {
                    $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Database!', 'isi' => 'Gagal menghapus sensor: ' . mysqli_stmt_error($stmt_delete_sensor)];
                    error_log("Sensor Delete Execute Error: " . mysqli_stmt_error($stmt_delete_sensor) . " | ID: $sensor_id_to_delete, SN: $device_sn_parent");
                }
                mysqli_stmt_close($stmt_delete_sensor);
            } else {
                $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Server!', 'isi' => 'Gagal mempersiapkan statement hapus sensor: ' . mysqli_error($conn)];
                error_log("Sensor Delete Prepare Error: " . mysqli_error($conn));
            }
        }
         // Redirect kembali ke halaman kelola sensor setelah aksi (sukses atau gagal)
        header("Location: ?page=device_sensors&device_sn=" . urlencode($device_sn_parent));
         exit; // Hentikan eksekusi setelah redirect

    } else {
         // Jika parameter 'action' delete_sensor ada tapi tidak login
        $_SESSION['pesan_feedback'] = ['tipe' => 'warning', 'judul' => 'Aksi Tidak Valid!', 'isi' => 'Anda belum login untuk melakukan aksi ini.'];
         header("Location: login.php"); exit; // Redirect ke login
    }
}

// ======================================================================
// === BAGIAN PROSES SEMUA AKSI FORM (SEBELUM OUTPUT HTML DIMULAI) ===
// ======================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

     // --- PROSES FORM DARI PAGE DEVICE ---
    if ($page_saat_ini === 'device') {
        // === Proses Tambah Perangkat ===
        if (isset($_POST['tambah_perangkat_action'])) {
            $serial_number = htmlspecialchars(trim($_POST['serial_number']));
            $controller_type = htmlspecialchars(trim($_POST['controller_type']));
            $location_text = htmlspecialchars(trim($_POST['location'])); // Deskripsi lokasi teks (opsional)
            // Ambil latitude dan longitude, konversi ke float, set null jika kosong. Ganti koma ke titik jika formatnya koma.
            $latitude = !empty(trim($_POST['latitude'])) ? floatval(str_replace(',', '.', trim($_POST['latitude']))) : null;
            $longitude = !empty(trim($_POST['longitude'])) ? floatval(str_replace(',', '.', trim($_POST['longitude']))) : null;
            $active = isset($_POST['active']) ? $_POST['active'] : 'Yes'; // Default 'Yes'
            $role = isset($_POST['role']) ? htmlspecialchars(trim($_POST['role'])) : null; // Ambil peran
            $protocol = isset($_POST['protocol']) ? htmlspecialchars(trim($_POST['protocol'])) : null; // Ambil protokol

            // Validasi Input
            // Validasi dasar untuk field wajib
            if (empty($serial_number) || empty($controller_type)) {
                $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Validasi!', 'isi' => 'Serial Number dan Jenis Kontroler wajib diisi.'];
            } elseif (strlen($serial_number) > 8) { // Sesuaikan dengan batasan di DB Anda
                $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Validasi!', 'isi' => 'Serial Number perangkat tidak boleh lebih dari 8 karakter.'];
            } else {
                // Periksa duplikat Serial Number sebelum INSERT
                $sql_check = "SELECT serial_number FROM devices WHERE serial_number = ?";
                $stmt_check = mysqli_prepare($conn, $sql_check);
                mysqli_stmt_bind_param($stmt_check, "s", $serial_number);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_store_result($stmt_check);

                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Duplikat!', 'isi' => "Gagal menambah: Serial Number '" . $serial_number . "' sudah terdaftar."];
                } else {
                    // Lakukan INSERT dengan kolom latitude dan longitude, role, dan protocol
                    $sql_insert = "INSERT INTO devices (serial_number, mcu_type, location, latitude, longitude, active, role, protocol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_insert = mysqli_prepare($conn, $sql_insert);
                    // Tipe data: s=string, d=double. Jumlah tipe harus 8.
                    mysqli_stmt_bind_param($stmt_insert, "sssddsss", $serial_number, $controller_type, $location_text, $latitude, $longitude, $active, $role, $protocol);
                    if (mysqli_stmt_execute($stmt_insert)) {
                        $_SESSION['pesan_feedback'] = ['tipe' => 'success', 'judul' => 'Sukses!', 'isi' => 'Data perangkat baru berhasil ditambahkan.'];
                    } else {
                        $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Database!', 'isi' => 'Gagal menambah data perangkat: ' . mysqli_stmt_error($stmt_insert)];
                        error_log("Device Insert Error: " . mysqli_stmt_error($stmt_insert) . " | SN: " . $serial_number);
                    }
                    mysqli_stmt_close($stmt_insert);
                }
                mysqli_stmt_close($stmt_check);
            }
            // Redirect setelah proses tambah (baik sukses maupun gagal validasi/insert)
            // Jika gagal validasi, redirect kembali ke form tambah
            $redirect_url = "?page=device";
            if (isset($_SESSION['pesan_feedback']['tipe']) && $_SESSION['pesan_feedback']['tipe'] === 'danger') {
                 $redirect_url .= "&add=true"; // Kembali ke form tambah jika gagal
                 // Anda bisa tambahkan parameter lain untuk mengisi ulang form, tapi ini lebih kompleks
                 // $_SESSION['form_data'] = $_POST; // Simpan data POST ke session jika ingin diisi ulang di form tambah
            }
            header("Location: " . $redirect_url);
            exit;

        // === Proses Edit Perangkat ===
        } elseif (isset($_POST['edit_perangkat_action']) && isset($_POST['original_serial_number'])) {
            $original_serial_number = $_POST['original_serial_number']; // SN lama (dari hidden input)
            $serial_number = htmlspecialchars(trim($_POST['serial_number'])); // SN baru
            $controller_type = htmlspecialchars(trim($_POST['controller_type']));
            $location_text = htmlspecialchars(trim($_POST['location'])); // Deskripsi lokasi teks
            $latitude = !empty(trim($_POST['latitude'])) ? floatval(str_replace(',', '.', trim($_POST['latitude']))) : null;
            $longitude = !empty(trim($_POST['longitude'])) ? floatval(str_replace(',', '.', trim($_POST['longitude']))) : null;
            $active = $_POST['active']; // Dari select, pasti ada 'Yes' atau 'No'
            $role = isset($_POST['role']) ? htmlspecialchars(trim($_POST['role'])) : null;
            $protocol = isset($_POST['protocol']) ? htmlspecialchars(trim($_POST['protocol'])) : null;

            // Validasi Input
            if (empty($serial_number) || empty($controller_type) /* || $latitude === null || $longitude === null */ ) {
                $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Validasi!', 'isi' => 'Serial Number dan Jenis Kontroler wajib diisi saat mengubah data perangkat.'];
            } elseif (strlen($serial_number) > 8) {
                $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Validasi!', 'isi' => 'Serial Number perangkat tidak boleh lebih dari 8 karakter.'];
            } else {
                $sn_konflik = false;
                // Cek apakah serial number baru sudah ada (HANYA jika diubah dan berbeda dari yang lama)
                if ($serial_number !== $original_serial_number) {
                    $sql_check_edit = "SELECT serial_number FROM devices WHERE serial_number = ?"; // Cek jika SN baru sudah ada
                    $stmt_check_edit = mysqli_prepare($conn, $sql_check_edit);
                    mysqli_stmt_bind_param($stmt_check_edit, "s", $serial_number);
                    mysqli_stmt_execute($stmt_check_edit);
                    mysqli_stmt_store_result($stmt_check_edit);
                    if (mysqli_stmt_num_rows($stmt_check_edit) > 0) {
                        $sn_konflik = true; // Konflik jika SN baru sudah terdaftar DAN BUKAN SN lama
                    }
                    mysqli_stmt_close($stmt_check_edit);
                }

                if ($sn_konflik) {
                    $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Duplikat!', 'isi' => "Gagal mengubah: Serial Number '" . $serial_number . "' sudah digunakan perangkat lain."];
                } else {
                    // Lakukan UPDATE dengan semua kolom yang bisa diubah
                    $sql_edit = "UPDATE devices SET serial_number = ?, mcu_type = ?, location = ?, latitude = ?, longitude = ?, active = ?, role = ?, protocol = ? WHERE serial_number = ?";
                    $stmt_edit = mysqli_prepare($conn, $sql_edit);
                    // Tipe: s s s d d s s s s (9 tipe)
                    mysqli_stmt_bind_param($stmt_edit, "sssddssss", $serial_number, $controller_type, $location_text, $latitude, $longitude, $active, $role, $protocol, $original_serial_number);
                    if (mysqli_stmt_execute($stmt_edit)) {
                        // mysqli_stmt_affected_rows bisa 0 jika tidak ada yang berubah
                        if (mysqli_stmt_affected_rows($stmt_edit) > 0) {
                            $_SESSION['pesan_feedback'] = ['tipe' => 'success', 'judul' => 'Sukses!', 'isi' => 'Data perangkat berhasil diperbarui.'];
                        } else {
                             // Jika affected rows 0, mungkin tidak ada yang diubah, atau SN lama tidak ditemukan (jarang terjadi di sini)
                            $_SESSION['pesan_feedback'] = ['tipe' => 'info', 'judul' => 'Info!', 'isi' => 'Data perangkat berhasil diperbarui (tidak ada perubahan terdeteksi).'];
                        }

                         // Redirect ke halaman edit dengan SN yang mungkin baru
                        $redirect_sn = ($serial_number !== $original_serial_number) ? $serial_number : $original_serial_number;
                         header("Location: ?page=device&edit=" . urlencode($redirect_sn) . "&updated=1"); // Tambah parameter updated
                         exit; // Hentikan eksekusi setelah redirect

                    } else {
                        $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Database!', 'isi' => 'Gagal memperbarui data perangkat: ' . mysqli_stmt_error($stmt_edit)];
                        error_log("Device Update Execute Error: " . mysqli_stmt_error($stmt_edit) . " | OriginalSN: " . $original_serial_number);
                    }
                    mysqli_stmt_close($stmt_edit);
                }
            }
            // Redirect kembali ke form edit jika validasi gagal atau update gagal tanpa redirect di atas
            header("Location: ?page=device&edit=" . urlencode($original_serial_number));
            exit;
        }
    }
    
    // --- PROSES FORM DARI PAGE USER ---
    elseif ($page_saat_ini === 'user') {
        // Pastikan hanya Admin yang bisa melakukan aksi ini
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin") {
        $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Akses Ditolak!', 'isi' => 'Anda tidak memiliki izin untuk mengelola pengguna.'];
          header("Location: ?page=" . urlencode($page_saat_ini)); // Redirect kembali ke halaman user (yang akan menampilkan pesan)
        exit;
        }

        // === Proses Tambah User ===
        if (isset($_POST['tambah_user_action'])) {
        $username = trim($_POST['username']);
        $password_plain = $_POST['password']; // Password tidak di-trim, validasi panjang bisa ditambahkan
        $fullname = trim($_POST['fullname']);
        $role = $_POST['role']; // Diambil dari select, seharusnya 'Admin' atau 'User'
        $active = isset($_POST['active']) ? $_POST['active'] : 'Yes'; // Diambil dari select

          // Validasi Input
        if (empty($username) || empty($password_plain) || empty($fullname) || !in_array($role, ['Admin', 'User']) || !in_array($active, ['Yes', 'No'])) {
            $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Validasi!', 'isi' => 'Semua field wajib diisi dengan benar saat menambah pengguna.'];
          } elseif (strlen($password_plain) < 6) { // Contoh validasi panjang password
            $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Validasi!', 'isi' => 'Password minimal harus 6 karakter.'];
        } else {
              // Cek duplikat username
            $sql_check_user = "SELECT username FROM user WHERE username = ?";
            $stmt_check_user = mysqli_prepare($conn, $sql_check_user);
            mysqli_stmt_bind_param($stmt_check_user, "s", $username);
            mysqli_stmt_execute($stmt_check_user);
            mysqli_stmt_store_result($stmt_check_user);

            if (mysqli_stmt_num_rows($stmt_check_user) > 0) {
                $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Duplikat!', 'isi' => "Username '$username' sudah digunakan."];
            } else {
                $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);
                $sql_insert_user = "INSERT INTO user (username, password, fullname, role, active) VALUES (?, ?, ?, ?, ?)";
                $stmt_insert_user = mysqli_prepare($conn, $sql_insert_user);
                mysqli_stmt_bind_param($stmt_insert_user, "sssss", $username, $password_hashed, $fullname, $role, $active);
                if (mysqli_stmt_execute($stmt_insert_user)) {
                    $_SESSION['pesan_feedback'] = ['tipe' => 'success', 'judul' => 'Sukses!', 'isi' => 'Pengguna baru berhasil ditambahkan.'];
                } else {
                    $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Database!', 'isi' => 'Gagal menambah pengguna: ' . mysqli_stmt_error($stmt_insert_user)];
                    error_log("User Insert Error: " . mysqli_stmt_error($stmt_insert_user));
                }
                mysqli_stmt_close($stmt_insert_user);
            }
            mysqli_stmt_close($stmt_check_user);
        }
          // Redirect setelah proses tambah
        header("Location: ?page=user" . (isset($_SESSION['pesan_feedback']['tipe']) && $_SESSION['pesan_feedback']['tipe'] === 'danger' ? '&add=true' : ''));
        exit;
        }
        // === Proses Edit User ===
        elseif (isset($_POST['edit_user_action']) && isset($_POST['original_username'])) {
        $original_username = $_POST['original_username'];
        $username_baru = trim($_POST['username']);
        $fullname_baru = trim($_POST['fullname']);
        $role_baru = $_POST['role'];
        $active_baru = $_POST['active'];
        $password_baru_plain = $_POST['password']; // Password baru (jika diisi)

          // Validasi Input
        if (empty($username_baru) || empty($fullname_baru) || !in_array($role_baru, ['Admin', 'User']) || !in_array($active_baru, ['Yes', 'No'])) {
            $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Validasi!', 'isi' => 'Semua field (kecuali password baru) wajib diisi dengan benar.'];
          } elseif (!empty($password_baru_plain) && strlen($password_baru_plain) < 6) { // Validasi panjang password baru jika diisi
            $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Validasi!', 'isi' => 'Password baru minimal harus 6 karakter.'];
        } else {
            $username_konflik = false;
              // Cek konflik username baru (HANYA jika username diubah)
            if ($username_baru !== $original_username) {
                $sql_check_konflik = "SELECT username FROM user WHERE username = ? AND username != ?"; // Pastikan username != original
                $stmt_check_konflik = mysqli_prepare($conn, $sql_check_konflik);
                  mysqli_stmt_bind_param($stmt_check_konflik, "ss", $username_baru, $original_username); // Bind original username juga
                mysqli_stmt_execute($stmt_check_konflik);
                mysqli_stmt_store_result($stmt_check_konflik);
                if (mysqli_stmt_num_rows($stmt_check_konflik) > 0) {
                    $username_konflik = true;
                }
                mysqli_stmt_close($stmt_check_konflik);
            }

            if ($username_konflik) {
                $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Duplikat!', 'isi' => "Username '$username_baru' sudah digunakan oleh pengguna lain."];
            } else {
                  // Siapkan Query Update
                if (!empty($password_baru_plain)) {
                    $password_baru_hashed = password_hash($password_baru_plain, PASSWORD_DEFAULT);
                    $sql_update_user = "UPDATE user SET username = ?, password = ?, fullname = ?, role = ?, active = ? WHERE username = ?";
                    $stmt_update_user = mysqli_prepare($conn, $sql_update_user);
                    mysqli_stmt_bind_param($stmt_update_user, "ssssss", $username_baru, $password_baru_hashed, $fullname_baru, $role_baru, $active_baru, $original_username);
                } else {
                      // Update tanpa mengubah password
                    $sql_update_user = "UPDATE user SET username = ?, fullname = ?, role = ?, active = ? WHERE username = ?";
                    $stmt_update_user = mysqli_prepare($conn, $sql_update_user);
                    mysqli_stmt_bind_param($stmt_update_user, "sssss", $username_baru, $fullname_baru, $role_baru, $active_baru, $original_username);
                }

                if ($stmt_update_user && mysqli_stmt_execute($stmt_update_user)) {
                    $_SESSION['pesan_feedback'] = ['tipe' => 'success', 'judul' => 'Sukses!', 'isi' => 'Data pengguna berhasil diperbarui.'];

                      // Jika admin mengedit profilnya sendiri dan mengubah username atau fullname, update session
                    if ($_SESSION['username'] === $original_username) {
                        if ($username_baru !== $original_username) {
                              $_SESSION['username'] = $username_baru; // Update session username
                        }
                          // Selalu update fullname di session jika user yang diedit adalah user yang login
                        $_SESSION['fullname'] = $fullname_baru;
                    }
                    header("Location: ?page=user" . ($username_baru !== $original_username ? "&edit=" . urlencode($username_baru) : "&edit=" . urlencode($original_username)) . "&updated=1");
                    exit;
                } else {
                    $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Database!', 'isi' => 'Gagal memperbarui data pengguna: ' . ($stmt_update_user ? mysqli_stmt_error($stmt_update_user) : mysqli_error($conn))];
                    error_log("User Update Error: " . ($stmt_update_user ? mysqli_stmt_error($stmt_update_user) : mysqli_error($conn)));
                }
                if($stmt_update_user) mysqli_stmt_close($stmt_update_user);
            }
          }          // Redirect kembali ke form edit jika validasi gagal atau update gagal tanpa redirect di atas
        header("Location: ?page=user&edit=" . urlencode($original_username));
        exit;
        }
    }

    elseif ($page_saat_ini === 'device_sensors' && isset($_POST['device_serial_number'])) {
         // Pastikan pengguna login
        if (isset($_SESSION['username'])) {
            $device_sn_parent = htmlspecialchars(trim($_POST['device_serial_number']));
            
            // === PENGECEKAN ROLE ADMIN (jika diperlukan) ===
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin") {
                $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Akses Ditolak!', 'isi' => 'Anda tidak memiliki izin untuk mengelola sensor.'];
            } else {
                 // === Proses Tambah Sensor ===
                if (isset($_POST['tambah_sensor_action'])) {
                    $sensor_name = htmlspecialchars(trim($_POST['sensor_name']));
                    $sensor_display_name = htmlspecialchars(trim($_POST['sensor_display_name']));
                    $sensor_unit = htmlspecialchars(trim($_POST['sensor_unit']));
                    $data_source = $_POST['data_source']; // Tidak perlu htmlspecialchars jika dari select
                    $is_active = isset($_POST['is_active']) ? 1 : 0;
                    $sensor_category = isset($_POST['sensor_category']) ? htmlspecialchars(trim($_POST['sensor_category'])) : null;
                    $sensor_type = isset($_POST['sensor_type']) ? htmlspecialchars(trim($_POST['sensor_type'])) : null;
                    $communication_protocol = isset($_POST['communication_protocol']) ? htmlspecialchars(trim($_POST['communication_protocol'])) : null;

                    // Validasi Input
                    if (empty($sensor_name) || empty($sensor_display_name) || empty($data_source)) {
                        $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Validasi!', 'isi' => 'Nama Sensor Internal, Nama Tampilan, dan Sumber Data wajib diisi.'];
                    } else {
                        // Cek duplikat sensor_name untuk perangkat ini
                        $sql_check_duplicate = "SELECT id FROM device_sensors WHERE device_serial_number = ? AND sensor_name = ?";
                        $stmt_check_duplicate = mysqli_prepare($conn, $sql_check_duplicate);
                        mysqli_stmt_bind_param($stmt_check_duplicate, "ss", $device_sn_parent, $sensor_name);
                        mysqli_stmt_execute($stmt_check_duplicate);
                        mysqli_stmt_store_result($stmt_check_duplicate);
                        if(mysqli_stmt_num_rows($stmt_check_duplicate) > 0) {
                            $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Duplikat!', 'isi' => "Nama Sensor Internal '" . $sensor_name . "' sudah terdaftar untuk perangkat ini."];
                        } else {
                             // Query INSERT ke tabel device_sensors
                            $sql_insert_sensor = "INSERT INTO device_sensors (device_serial_number, sensor_name, sensor_display_name, sensor_unit, sensor_category, sensor_type, communication_protocol, data_source, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt_insert_sensor = mysqli_prepare($conn, $sql_insert_sensor);
                            // Bind parameter: s=string, i=integer (untuk is_active)
                            mysqli_stmt_bind_param($stmt_insert_sensor, "ssssssssi", $device_sn_parent, $sensor_name, $sensor_display_name, $sensor_unit, $sensor_category, $sensor_type, $communication_protocol, $data_source, $is_active);

                            if (mysqli_stmt_execute($stmt_insert_sensor)) {
                                $_SESSION['pesan_feedback'] = ['tipe' => 'success', 'judul' => 'Sukses!', 'isi' => "Sensor '" . $sensor_display_name . "' berhasil ditambahkan."];
                            } else {
                                $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Database!', 'isi' => 'Gagal menambah sensor: ' . mysqli_stmt_error($stmt_insert_sensor)];
                                error_log("Sensor Insert Error: " . mysqli_stmt_error($stmt_insert_sensor) . " | SN: $device_sn_parent, Name: $sensor_name");
                            }
                            mysqli_stmt_close($stmt_insert_sensor);
                        }
                        mysqli_stmt_close($stmt_check_duplicate);
                    }
                     // Redirect kembali ke form tambah jika validasi gagal
                    if (isset($_SESSION['pesan_feedback']['tipe']) && $_SESSION['pesan_feedback']['tipe'] === 'danger') {
                        header("Location: ?page=device_sensors&device_sn=" . urlencode($device_sn_parent) . "&add=1"); exit;
                    }

                } // === Akhir Proses Tambah Sensor ===

                // === Proses Edit Sensor ===
                elseif (isset($_POST['edit_sensor_action']) && isset($_POST['sensor_id'])) {
                    $sensor_display_name = htmlspecialchars(trim($_POST['sensor_display_name'])); // sensor_name readonly
                    $sensor_unit = htmlspecialchars(trim($_POST['sensor_unit']));
                    $sensor_category = isset($_POST['sensor_category']) ? htmlspecialchars(trim($_POST['sensor_category'])) : null;
                    $sensor_type = isset($_POST['sensor_type']) ? htmlspecialchars(trim($_POST['sensor_type'])) : null;
                    $communication_protocol = isset($_POST['communication_protocol']) ? htmlspecialchars(trim($_POST['communication_protocol'])) : null;
                    $data_source = $_POST['data_source'];
                    $is_active = isset($_POST['is_active']) ? 1 : 0;
                    $sensor_id = intval($_POST['sensor_id']); // Ambil ID
                    // device_serial_number diambil dari hidden input di form

                    // Validasi Input
                    if (empty($sensor_display_name) || empty($data_source)) {
                        $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Validasi!', 'isi' => 'Nama Tampilan dan Sumber Data wajib diisi.'];
                    } else {
                         // Query UPDATE tabel device_sensors
                        $sql_update_sensor = "UPDATE device_sensors SET sensor_display_name = ?, sensor_unit = ?, sensor_category = ?, sensor_type = ?, communication_protocol = ?, data_source = ?, is_active = ? WHERE id = ? AND device_serial_number = ?";
                        $stmt_update_sensor = mysqli_prepare($conn, $sql_update_sensor);
                        // Bind parameter: s s s i i s (display_name, unit, source, is_active, id, SN)
                        mysqli_stmt_bind_param($stmt_update_sensor, "ssssssiis", $sensor_display_name, $sensor_unit, $sensor_category, $sensor_type, $communication_protocol, $data_source, $is_active, $sensor_id, $device_sn_parent);

                        if (mysqli_stmt_execute($stmt_update_sensor)) {
                            // mysqli_stmt_affected_rows bisa 0 jika tidak ada yang berubah
                            if (mysqli_stmt_affected_rows($stmt_update_sensor) > 0) {
                                $_SESSION['pesan_feedback'] = ['tipe' => 'success', 'judul' => 'Sukses!', 'isi' => 'Data sensor berhasil diperbarui.'];
                            } else {
                                $_SESSION['pesan_feedback'] = ['tipe' => 'info', 'judul' => 'Info!', 'isi' => 'Data sensor berhasil diperbarui (tidak ada perubahan terdeteksi).'];
                            }
                        } else {
                            $_SESSION['pesen_feedback'] = ['tipe' => 'danger', 'judul' => 'Error Database!', 'isi' => 'Gagal memperbarui sensor: ' . mysqli_stmt_error($stmt_update_sensor)];
                            error_log("Sensor Update Error: " . mysqli_stmt_error($stmt_update_sensor) . " | ID: $sensor_id, SN: $device_sn_parent");
                        }
                        mysqli_stmt_close($stmt_update_sensor);
                    }
                     // Redirect kembali ke form edit jika validasi gagal atau update gagal tanpa redirect di atas
                    if (isset($_SESSION['pesan_feedback']['tipe']) && $_SESSION['pesan_feedback']['tipe'] === 'danger') {
                        header("Location: ?page=device_sensors&device_sn=" . urlencode($device_sn_parent) . "&edit=" . $sensor_id); exit;
                    }

                } // === Akhir Proses Edit Sensor ===
            }

             // Redirect kembali ke halaman kelola sensor setelah proses POST (jika belum di-redirect oleh validasi form)
            header("Location: ?page=device_sensors&device_sn=" . urlencode($device_sn_parent));
             exit; // Hentikan eksekusi setelah redirect

        } else {
             // Jika tidak login atau bukan Admin
            $_SESSION['pesan_feedback'] = ['tipe' => 'warning', 'judul' => 'Aksi Tidak Valid!', 'isi' => 'Anda belum login atau tidak memiliki izin untuk mengelola sensor.'];
             header("Location: login.php"); exit; // Redirect ke login
        }
    }
    // --- Tambahkan else if untuk halaman lain yang memiliki form ---
}

// =================================================
// === LOGIN CHECK (Setelah semua potensi redirect dari proses form) ===
// =================================================
if (!isset($_SESSION['username']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header("Location: login.php");
    exit;
}

// =================================================
// === MULAI OUTPUT HTML ===
// =================================================
include "inc/header.php";
include "inc/navbar.php";
include "inc/sidebar.php";

echo '<div class="content-wrapper">';

// --- Tampilkan Pesan Feedback Global dari Session ---
if ($pesan_feedback_global !== null) {
    $tipe_alert = $pesan_feedback_global['tipe'] === 'success' ? 'success' : ($pesan_feedback_global['tipe'] === 'warning' ? 'warning' : 'danger');
    $ikon_alert = $pesan_feedback_global['tipe'] === 'success' ? 'check' : ($pesan_feedback_global['tipe'] === 'warning' ? 'exclamation-triangle' : 'ban');

        echo '<section class="content pt-3" id="global-alert-section">'; // Bagian konten untuk alert
        echo '  <div class="container-fluid">';
        echo '    <div class="row">';
        echo '      <div class="col-md-12">';
        echo '        <div id="global-alert-wrapper">'; // Tanpa margin/padding khusus di sini
        echo '          <div class="alert alert-' . $tipe_alert . ' alert-dismissible fade show" role="alert" id="main-global-alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h5><i class="icon fas fa-' . $ikon_alert . '"></i> ' . htmlspecialchars($pesan_feedback_global['judul']) . '</h5>' . htmlspecialchars($pesan_feedback_global['isi']) . '
                        </div>';
        echo '        </div>';
        echo '      </div>';
        echo '    </div>';
        echo '  </div>';
        echo '</section>'; // Tutup section alert

    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        const mainGlobalAlertElement = document.getElementById("main-global-alert");
        const alertWrapperElement = document.getElementById("global-alert-wrapper"); // Gunakan ID wrapper alert
        let autoCloseTimer; // Variabel untuk menyimpan ID timer

        // Gunakan alertWrapperElement sebagai elemen yang akan di-hide total jika alert hilang
        const containerToHide = alertWrapperElement ? alertWrapperElement.closest(".container-fluid") : null; // Cari container terdekat

        // Cek jika elemen alert utama ditemukan
        if (mainGlobalAlertElement) {

            function hideAlertContainerCompletely() {
                // Sembunyikan elemen wrapper atau container jika alert sudah close
                if (alertWrapperElement) {
                    alertWrapperElement.style.display = "none"; // Sembunyikan wrapper
                     if (containerToHide) containerToHide.style.display = "none"; // Sembunyikan container-fluid jika ada
                }
                // console.log("Alert container completely hidden.");
            }

            // Event listener saat alert sudah selesai transisi fade-out
            mainGlobalAlertElement.addEventListener("closed.bs.alert", function () {
                hideAlertContainerCompletely();
                if (autoCloseTimer) { 
                    clearTimeout(autoCloseTimer);
                }
            });

            // Mulai timer untuk auto-close
            autoCloseTimer = setTimeout(function() {
                const currentAlertToClose = document.getElementById("main-global-alert");
                if (currentAlertToClose) {
                    // Cek jika Bootstrap Alert JS tersedia
                    if (typeof bootstrap !== "undefined" && bootstrap.Alert) {
                        var bsAlertInstance = bootstrap.Alert.getInstance(currentAlertToClose);
                        if (!bsAlertInstance) {
                             // Jika instance belum ada (misal, diinisialisasi manual), buat instance baru
                            bsAlertInstance = new bootstrap.Alert(currentAlertToClose);
                        }
                         // Tutup instance Bootstrap Alert
                        bsAlertInstance.close();
                    } else {
                         // Fallback sederhana jika Bootstrap JS tidak tersedia atau versi lama
                        currentAlertToClose.style.display = "none"; // Sembunyikan elemen alert
                        hideAlertContainerCompletely(); // Sembunyikan wrapper/container
                    }
                } else {
                     // Jika elemen alert utama entah kenapa tidak ditemukan lagi, tetap sembunyikan container
                    hideAlertContainerCompletely();
                }
            }, 5000); // Alert hilang setelah 5 detik (SESUAIKAN DURASI)

                // Event listener untuk tombol close manual
                const closeButton = mainGlobalAlertElement.querySelector(\'[data-dismiss="alert"]\');
                if (closeButton) {
                    closeButton.addEventListener("click", function(event) {
                        if (autoCloseTimer) {
                            clearTimeout(autoCloseTimer);
                        } else {
                            setTimeout(hideAlertContainerCompletely, 500);
                        }
                    });
                }

            } else {
                hideAlertContainerCompletely();
            }
        });
    </script>';
}


// --- Include Konten Halaman Dinamis ---
$allowed_pages = ['dashboard', 'datasensor', 'dataactuator', 'device', 'user', 'grafik_sensor', 'device_sensors'];
if (in_array($page_saat_ini, $allowed_pages) && file_exists("page/" . $page_saat_ini . ".php")) {
    include "page/" . $page_saat_ini . ".php"; // Include konten halaman
} else {
    include "page/dashboard.php"; // Default jika halaman tidak diizinkan atau tidak ditemukan
}

echo '</div>'; // Tutup wrapper konten

// --- Tutup Koneksi Database Setelah Semua Query Selesai ---
if (isset($conn) && $conn instanceof mysqli) {
    mysqli_close($conn);
}

include "inc/footer.php";
?>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables & Plugins -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Ion Slider -->
<script src="plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(function () {
    if ($("#example1").length) {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "buttons": ["csv", "excel", "colvis"], // "copy", "pdf", "print"
            "paging": true, // Mengaktifkan paginasi
            "pagingType": "full", // Opsi: "simple", "simple_numbers", "full", "full_numbers"
            "info": false, // Opsional: Set ke false jika tidak ingin "Showing X to Y of Z entries"
            "searching": false // Opsional: Set ke false jika tidak ingin fitur search DataTables
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    }
});
</script>
</body>
</html>