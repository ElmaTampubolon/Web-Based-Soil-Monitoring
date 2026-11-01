<?php
// api/get_sensor_data.php

// 1. Atur Error Reporting (SANGAT PENTING untuk output JSON bersih)
// Untuk Development: Aktifkan E_ALL dan display_errors (sementara saat debugging)
// error_reporting(E_ALL);
// ini_set('display_errors', 1); 

// Untuk Produksi: Laporkan semua error ke log, jangan tampilkan ke klien
error_reporting(E_ALL); 
ini_set('display_errors', 1); // WAJIB 0 agar error PHP tidak merusak output JSON
ini_set('log_errors', 1);     // Pastikan error dicatat di file log server
// Pastikan error_log di php.ini dikonfigurasi dengan benar
// error_log("--- API: get_sensor_data.php accessed at " . date("Y-m-d H:i:s") . " ---"); // Debug awal akses

// 2. Set Header untuk Output JSON (Panggil SEDINI MUNGKIN)
// Pastikan tidak ada spasi, baris baru, atau output lain SEBELUM tag <?php dan baris ini.
if (!headers_sent()) { 
    header('Content-Type: application/json');
    // Izinkan request dari domain lain jika API ini akan diakses dari frontend yang berbeda domain (CORS)
    // header('Access-Control-Allow-Origin: *'); // Hati-hati dengan wildcard '*' di produksi. Batasi ke domain spesifik.
}

// 3. Sertakan File Konfigurasi Database
// Jadi, __DIR__ . "/../config/database.php" adalah path absolut yang robust
// require_once __DIR__ . "/../config/database.php"; 
// Jika __DIR__ tidak bekerja atau XAMPP/setup Anda berbeda, coba path relatif:
require_once __DIR__ . "/../config/database.php";

// 4. Inisialisasi Variabel Respon Default
$response = [
    'status' => 'error',
    'message' => 'Terjadi kesalahan internal pada server.', // Pesan default lebih umum
    'data' => []
];
// Kode status HTTP akan diatur nanti berdasarkan kondisi (default 200 jika tidak diubah)

// --- PERBAIKAN: Pengecekan koneksi database SETELAH include ---
// Jika include GAGAL, $conn akan menjadi null/false
if (!$conn) {
    $response['message'] = 'Koneksi ke database gagal. Server tidak siap.';
    // Hanya set kode HTTP jika header belum terkirim
    if (!headers_sent()) { http_response_code(503); } // Service Unavailable
    // Error detail sudah dicatat oleh mysqli_connect_error() di config/database.php jika koneksi gagal
    echo json_encode($response); // Kirim respon error
    exit; // Hentikan skrip
}
// Jika sampai di sini, $conn PASTI objek mysqli yang valid

// 5. Ambil dan Validasi Parameter dari GET Request
$sensor_name = isset($_GET['sensor']) ? trim(htmlspecialchars($_GET['sensor'], ENT_QUOTES, 'UTF-8')) : null;
$device_sn = isset($_GET['device']) ? trim(htmlspecialchars($_GET['device'], ENT_QUOTES, 'UTF-8')) : null;
$time_range = isset($_GET['range']) ? trim(htmlspecialchars($_GET['range'], ENT_QUOTES, 'UTF-8')) : '24h';

// Validasi parameter dasar yang wajib ada
if (empty($sensor_name) || empty($device_sn)) {
    $response['message'] = 'Parameter "sensor" dan "device" wajib diisi.';
    if (!headers_sent()) { http_response_code(400); } // Bad Request
    error_log("API Get Sensor Data - Validasi gagal (parameter kosong): Sensor='".($sensor_name ?? 'NULL')."', Device='".($device_sn ?? 'NULL')."'. Input GET: " . print_r($_GET, true));
} elseif (strlen($device_sn) > 20) { // Sesuaikan batasan panjang SN jika perlu (VARCHAR di DB Anda 8)
    $response['message'] = 'Parameter "device" (Serial Number) terlalu panjang.';
    if (!headers_sent()) { http_response_code(400); }
    error_log("API Get Sensor Data - Validasi gagal (SN terlalu panjang): SN='$device_sn'");
} else {
    // Jika parameter dasar valid, lanjutkan proses
    // Set default error 500 untuk blok ini, akan diubah jika sukses
    if (!headers_sent()) { http_response_code(500); } 

    // 7. Buat Query SQL dengan Prepared Statements
    // --- PERBAIKAN: Gunakan NAMA KOLOM TIMESTAMP YANG BENAR 'time' ---
    // Gunakan alias 'timestamp' di hasil SELECT untuk konsisten dengan JavaScript
   $column_map = [
    'suhu_tanah' => 'temp_tanah',
    'kelembapan_tanah' => 'hum_tanah',
    'nitrogen' => 'n',
    'fosfor' => 'p',
    'kalium' => 'k',
    'ph' => 'ph',
    'ec' => 'ec',
    'suhu_lingkungan' => 'temp_udara',
    'kelembapan_lingkungan' => 'hum_udara'
];

$column = $column_map[$sensor_name];

$sql = "
SELECT
    DATE_FORMAT(created_at,'%Y-%m-%d %H:%i:%s') AS timestamp,
    $column AS value
FROM sensor_data
ORDER BY created_at DESC
LIMIT 500";

    // error_log("API Get Sensor Data - SQL Query: " . $sql); // Debug SQL
    // error_log("API Get Sensor Data - Params: Sensor='$sensor_name', Device='$device_sn'");


    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
       // mysqli_stmt_bind_param($stmt, "ss", $sensor_name, $device_sn);

        // === DEBUG: Log sebelum execute ===
        // error_log("API Get Sensor Data - Preparing to execute query with params: sensor='$sensor_name', device='$device_sn'");

        if (mysqli_stmt_execute($stmt)) {
            // === DEBUG: Log setelah execute ===
            // error_log("API Get Sensor Data - Query executed successfully.");

            $result = mysqli_stmt_get_result($stmt);
            $data_points = [];

            // === DEBUG: Log jumlah hasil ===
            // if ($result) { error_log("API Get Sensor Data - Query returned " . mysqli_num_rows($result) . " rows."); }

            while ($row = mysqli_fetch_assoc($result)) {
                // Pastikan nilai dikonversi ke float untuk Chart.js
                $row['value'] = floatval($row['value']);
                $data_points[] = $row;
            }
            // === DEBUG: Log data points setelah loop ===
            // error_log("API Get Sensor Data - Data points collected: " . count($data_points) . " rows.");

            if ($result) { mysqli_free_result($result); } // Bebaskan memori hasil query

            if (!empty($data_points)) {
                $response['status'] = 'success';
                $response['message'] = 'Data berhasil diambil.';
                $response['data'] = $data_points;
                if (!headers_sent()) { http_response_code(200); } // OK
            } else {
                $response['status'] = 'success'; // Status sukses karena query dieksekusi tanpa error DB
                $response['message'] = 'Tidak ada data ditemukan untuk kriteria yang dipilih.';
                $response['data'] = []; // Pastikan data adalah array kosong
                if (!headers_sent()) { http_response_code(200); } // OK (atau 204 No Content lebih tepat tapi 200 umum)
            }
        } else {
            // === DEBUG: Log Error Eksekusi Query ===
            $db_error = mysqli_stmt_error($stmt);
            $response['message'] = 'Gagal mengeksekusi query database: ' . $db_error; // Kirim detail error DB ke log server
            // http_response_code(500) sudah diset default di atas blok ini
            error_log("API Get Sensor Data - Execute Error: " . $db_error . " | Sensor: '$sensor_name', Device: '$device_sn'");
        }
        mysqli_stmt_close($stmt); // Tutup statement
    } else {
        // === DEBUG: Log Error Prepare Query ===
        $db_error = mysqli_error($conn);
        $response['message'] = 'Gagal mempersiapkan statement SQL: ' . $db_error; // Kirim detail error DB ke log server
        // http_response_code(500) sudah diset default di atas blok ini
        error_log("API Get Sensor Data - Prepare Error: " . $db_error . " | SQL: " . $sql); // Log juga SQL-nya
    }
}

// 8. Tutup Koneksi Database (jika $conn valid dan belum ditutup)
if (isset($conn) && $conn instanceof mysqli) {
    mysqli_close($conn);
}

// 9. Kirim Respon JSON Kembali
// Pastikan tidak ada output lain sebelum header ini
// Header sudah di-set di awal jika display_errors=0
echo json_encode($response);
exit; // Pastikan skrip berhenti di sini
?>