<?php
require_once "config/database.php"; // Mengarah ke folder config di atas folder api

// 2. Atur Error Reporting untuk Development (Di Produksi, log ke file, jangan tampilkan)
error_reporting(E_ALL);
// ini_set('display_errors', 1); // AKTIFKAN HANYA SAAT DEVELOPMENT
ini_set('display_errors', 0);    // NONAKTIFKAN DI PRODUKSI
ini_set('log_errors', 1);        // SELALU AKTIFKAN LOGGING ERROR KE FILE

// --- Inisialisasi Variabel untuk Respon ---
$response = ['status' => 'error', 'message' => 'Permintaan awal tidak valid.'];
// Default HTTP status code akan 200 jika tidak diubah, kita akan set nanti.

// 3. Periksa apakah Metode Request adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 4. Ambil RAW POST Body (karena Shiftr.io mengirim JSON)
    $json_input = file_get_contents('php://input');
    if ($json_input === false || empty($json_input)) {
        $response['message'] = 'Gagal membaca input stream atau input kosong.';
        http_response_code(400); // Bad Request
        error_log("save_data.php: Gagal membaca php://input atau input kosong.");
    } else {

        $webhook_data = json_decode($json_input, true); // true untuk associative array

        if ($webhook_data === null && json_last_error() !== JSON_ERROR_NONE) {
            $response['message'] = 'Format JSON tidak valid: ' . json_last_error_msg();
            http_response_code(400);
            error_log("save_data.php: JSON decode error: " . json_last_error_msg() . " | Input: " . $json_input);
        } elseif (isset($webhook_data['topic']) && isset($webhook_data['payload'])) {
            $topic_received = trim($webhook_data['topic']);
            $payload_received = trim($webhook_data['payload']); // Ini adalah nilai sensor (string)

            // 5. Parsing Topik untuk mendapatkan serial_number dan name (nama sensor/aktuator)
            $topic_parts = explode('/', $topic_received);
            $serial_number = null;
            $name = null;

            if (count($topic_parts) >= 3) {
                // $base_from_topic = $topic_parts[0]; // bisa diabaikan atau divalidasi jika base_topic_mqtt Anda juga ada di sini
                if ($topic_parts[1] === 'status') { // Jika ini topik status
                    $serial_number = $topic_parts[2]; // SN ada di bagian ketiga
                    $name = 'device_status';          // Nama khusus untuk data status
                } else { // Asumsi topik data sensor/aktuator biasa
                    $serial_number = $topic_parts[1]; // SN ada di bagian kedua
                    $name = $topic_parts[2];          // Nama sensor/aktuator ada di bagian ketiga
                }
            }

            // --- Validasi Input Dasar setelah parsing ---
            if (empty($serial_number) || empty($name) || $payload_received === "" || $payload_received === null) { // Payload bisa jadi string "0"
                $response['message'] = 'Data tidak lengkap setelah parsing topik: serial_number, name, dan value (payload) wajib ada.';
                http_response_code(400);
                error_log("save_data.php: Validasi gagal. SN: $serial_number, Name: $name, Payload: '$payload_received', Topic: $topic_received");
            } elseif (strlen($serial_number) > 20) { // Sesuaikan batasan panjang SN
                $response['message'] = 'Serial number terlalu panjang (maks 20 karakter).';
                http_response_code(400);
            } else {
                // Penentuan tipe 'sensor' atau 'actuator' atau 'status'
                $type = "unknown";
                $sensor_keywords = ["suhu_udara", "kelembaban", "ph", "tds", "turbidity", "dissolved", "ketinggian", "amonia", "angin", "ec", "suhu_air", "potentiometer"];
                $actuator_keywords = ["pompa", "led", "servo"];
                $status_keywords = ["device_status"]; // Dari parsing topik status

                $name_lower = strtolower($name);

                if (in_array($name_lower, $status_keywords)) {
                    $type = "status";
                } else {
                    foreach ($sensor_keywords as $keyword) {
                        // Gunakan pencocokan persis untuk $name jika $name adalah nama sensor/aktuator
                        if ($name_lower == $keyword) {
                            $type = "sensor";
                            break;
                        }
                    }
                    if ($type === "unknown") {
                        foreach ($actuator_keywords as $keyword) {
                            if ($name_lower == $keyword) {
                                $type = "actuator";
                                break;
                            }
                        }
                    }
                }
                
                if ($type === "unknown") {
                    error_log("save_data.php: Tipe tidak dikenal untuk name: '$name' dari topic: '$topic_received'. Disimpan sebagai 'unknown'.");
                     // Anda bisa memilih untuk tidak menyimpan jika tipe unknown, atau tetap simpan.
                }

                // 6. Siapkan dan Eksekusi Query dengan Prepared Statements
                $sql = "INSERT INTO data (serial_number, sensor_actuator, value, name, mqtt_topic) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);

                if ($stmt) {
                    // Semua data dari MQTT (topic, payload) adalah string.
                    // Jika kolom 'value' di DB Anda adalah numerik, PHP/MySQL akan mencoba konversi implisit.
                    // Untuk keamanan dan konsistensi, Anda bisa validasi atau konversi $payload_received di sini jika perlu.
                    mysqli_stmt_bind_param($stmt, "sssss", $serial_number, $type, $payload_received, $name, $topic_received);

                    if (mysqli_stmt_execute($stmt)) {
                        $response['status'] = 'success';
                        $response['message'] = 'Data berhasil disimpan.';
                        http_response_code(201); // Created

                        // --- UPDATE last_update di tabel device_sensors ---
                        $sql_update_last_update = "UPDATE device_sensors SET last_update = NOW() WHERE device_serial_number = ? AND sensor_name = ?";
                        $stmt_update_last_update = mysqli_prepare($conn, $sql_update_last_update);
                        if ($stmt_update_last_update) {
                            mysqli_stmt_bind_param($stmt_update_last_update, "ss", $serial_number, $name);
                            if (!mysqli_stmt_execute($stmt_update_last_update)) {
                                error_log("save_data.php: Gagal update last_update di device_sensors: " . mysqli_stmt_error($stmt_update_last_update) . " | SN: $serial_number, Name: $name");
                            }
                            mysqli_stmt_close($stmt_update_last_update);
                        } else {
                            error_log("save_data.php: Gagal mempersiapkan update last_update: " . mysqli_error($conn));
                        }
                        // --- AKHIR UPDATE last_update ---

                    } else {
                        // Periksa apakah error karena duplikat (jika ada unique key selain primary)
                        if (mysqli_errno($conn) == 1062) {
                            $response['message'] = 'Gagal menyimpan data: Kemungkinan data duplikat.';
                             http_response_code(409); // Conflict
                        } else {
                            $response['message'] = 'Gagal menyimpan data ke database.';
                            http_response_code(500); // Internal Server Error
                        }
                        error_log("save_data.php: DB Insert Error: " . mysqli_stmt_error($stmt) . " | Topic: $topic_received | Payload: $payload_received");
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $response['message'] = 'Gagal mempersiapkan statement SQL.';
                    http_response_code(500);
                    error_log("save_data.php: DB Prepare Statement Error: " . mysqli_error($conn));
                }
            }
        } else {
            $response['message'] = 'Format data webhook tidak sesuai atau kunci "topic"/"payload" tidak ditemukan.';
            http_response_code(400);
            error_log("save_data.php: JSON decode berhasil tapi kunci tidak lengkap. Input: " . $json_input);
        }
    }
} else {
    $response['message'] = 'Metode request tidak valid. Hanya POST yang diizinkan.';
    http_response_code(405); // Method Not Allowed
}

// 7. Tutup Koneksi Database (jika $conn valid dan belum ditutup)
if (isset($conn) && $conn instanceof mysqli) {
    mysqli_close($conn);
}

// 8. Kirim Respon JSON Kembali
// Pastikan tidak ada output lain sebelum header ini
if (!headers_sent()) {
    header('Content-Type: application/json');
}
echo json_encode($response);
exit; // Pastikan skrip berhenti di sini setelah mengirim respon
?>