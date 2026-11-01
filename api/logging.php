<?php
// Selalu sertakan ini di awal untuk penanganan error yang lebih baik
error_reporting(E_ALL);
ini_set('display_errors', 0); // Jangan tampilkan error ke output di produksi
ini_set('log_errors', 1);     // Log error ke file

include "../config/database.php"; // Pastikan path ini benar

// 1. Ambil dan Decode JSON dari Webhook
$input = file_get_contents('php://input');
if ($input === false) {
    http_response_code(400); // Bad Request
    error_log("Webhook Logging: Gagal membaca input stream.");
    exit("Error: Gagal membaca input.");
}

$webhookResponse = json_decode($input, true);

// 2. Validasi Input Dasar
if ($webhookResponse === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    error_log("Webhook Logging: Gagal decode JSON. Error: " . json_last_error_msg() . ". Input: " . $input);
    exit("Error: Format JSON tidak valid.");
}

if (!isset($webhookResponse["topic"]) || !isset($webhookResponse["payload"])) {
    http_response_code(400); // Bad Request
    error_log("Webhook Logging: 'topic' atau 'payload' tidak ditemukan dalam JSON. Input: " . $input);
    exit("Error: Data 'topic' atau 'payload' tidak lengkap.");
}

$topic = $webhookResponse["topic"];
$payload = $webhookResponse["payload"]; // Payload bisa jadi string, angka, atau JSON lagi. Perlu penanganan lebih lanjut jika kompleks.

// 3. Parsing Topik (dengan validasi)
$topicExplode = explode("/", $topic);

// Asumsi format topik adalah: base_topic/serial_number/name_sensor_actuator[/sub_topic_lain]
// Minimal harus ada 3 bagian: base, serial_number, name
if (count($topicExplode) < 3) {
    http_response_code(400); // Bad Request
    error_log("Webhook Logging: Format topik tidak sesuai harapan. Topik: " . $topic);
    exit("Error: Format topik tidak valid.");
}

// Ambil bagian topik dengan hati-hati
$baseTopic = $topicExplode[0];       // e.g., "coba-coba" (Anda mungkin ingin memvalidasi ini)
$serialNumber = $topicExplode[1];
$name = $topicExplode[2];

// 4. Penentuan Tipe (Lebih Baik jika bisa dari topik atau payload)
// Ini masih menggunakan logika Anda, tapi bisa diperbaiki
$sensor_names = ["suhu", "kelembaban", "ph", "tds", "turbidity", "dissolved", "ketinggian", "amonia", "angin"]; // Daftar sensor yang diketahui
$actuator_names = ["pompa", "led", "servo"]; // Daftar aktuator yang diketahui

$type = "unknown"; // Default

if (in_array(strtolower($name), $sensor_names)) {
    $type = "sensor";
} elseif (in_array(strtolower($name), $actuator_names)) {
    $type = "actuator";
}
// Jika Anda ingin semua yang tidak terdaftar sebagai sensor/aktuator menjadi "actuator" seperti logika lama:
// else {
//    $type = "actuator"; // Atau mungkin "other" / log error
// }

// 5. Siapkan dan Eksekusi Query dengan Prepared Statements (SANGAT PENTING)
$sql = "INSERT INTO data (serial_number, sensor_actuator, value, name, mqtt_topic)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    // Tipe data binding: s = string
    // Pastikan tipe data di bind_param cocok dengan kolom database Anda.
    // Jika 'value' bisa jadi angka, Anda mungkin perlu konversi atau ubah 's' jadi 'd' (double) atau 'i' (integer)
    // Untuk payload, seringkali disimpan sebagai string (VARCHAR/TEXT) karena bisa beragam.
    mysqli_stmt_bind_param($stmt, "sssss", $serialNumber, $type, $payload, $name, $topic);

    if (mysqli_stmt_execute($stmt)) {
        // Berhasil insert
        http_response_code(200); // OK
        // Anda bisa mencatat log sukses jika perlu, atau tidak sama sekali jika traffic tinggi
        // error_log("Webhook Logging: Data berhasil disimpan. Topik: " . $topic);
        echo "Data berhasil disimpan."; // Respon ke webhook (opsional)
    } else {
        // Gagal eksekusi statement
        http_response_code(500); // Internal Server Error
        error_log("Webhook Logging: Gagal mengeksekusi statement. Error: " . mysqli_stmt_error($stmt) . ". SQL: " . $sql . " Data: SN=$serialNumber, type=$type, payload=$payload, name=$name, topic=$topic");
        echo "Error: Gagal menyimpan data.";
    }
    mysqli_stmt_close($stmt);
} else {
    // Gagal mempersiapkan statement
    http_response_code(500); // Internal Server Error
    error_log("Webhook Logging: Gagal mempersiapkan statement. Error: " . mysqli_error($conn));
    echo "Error: Terjadi kesalahan pada server.";
}

mysqli_close($conn); // Tutup koneksi
?>