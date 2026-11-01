<?php
// File: page/device_sensors.php
// Pastikan variabel $conn (koneksi database) sudah tersedia dari index.php

// Cek apakah parameter device_sn ada di URL, jika tidak, set pesan dan biarkan index.php redirect
if (!isset($_GET['device_sn']) || empty(trim($_GET['device_sn']))) {
    $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Error', 'isi' => 'Serial number perangkat tidak ditentukan.'];
    // Jangan panggil header() di sini, biarkan index.php yang menanganinya
}

$device_sn_to_manage = isset($_GET['device_sn']) ? htmlspecialchars(trim($_GET['device_sn'])) : null;

// --- Inisialisasi Variabel ---
$page_current_sensor = 'device_sensors';
$device_details = null;
$sensors_list = [];
$sensor_to_edit = null;
$pesan_lokal_sensor = null; // Pesan error spesifik di halaman ini (query SELECT)
$list_potensial_sources = []; // Daftar perangkat lain yang bisa jadi sumber data

// --- Ambil Detail Perangkat Utama (untuk judul, dll.) ---
if ($device_sn_to_manage) {
    $sql_device = "SELECT serial_number, location FROM devices WHERE serial_number = ? LIMIT 1";
    $stmt_device = mysqli_prepare($conn, $sql_device);
    if ($stmt_device) {
        mysqli_stmt_bind_param($stmt_device, "s", $device_sn_to_manage);
        mysqli_stmt_execute($stmt_device);
        $result_device = mysqli_stmt_get_result($stmt_device);
        if ($result_device && mysqli_num_rows($result_device) > 0) {
            $device_details = mysqli_fetch_assoc($result_device);

            // --- Ambil Daftar Perangkat Lain (yang bisa jadi Sumber Data) ---
            $sql_potential_sources = "SELECT serial_number, location, role FROM devices WHERE serial_number != ? ORDER BY serial_number ASC";
            $stmt_sources = mysqli_prepare($conn, $sql_potential_sources);
            if ($stmt_sources) {
                mysqli_stmt_bind_param($stmt_sources, "s", $device_sn_to_manage);
                mysqli_stmt_execute($stmt_sources);
                $result_sources = mysqli_stmt_get_result($stmt_sources);
                if ($result_sources) {
                    while ($row_source = mysqli_fetch_assoc($result_sources)) {
                        $list_potensial_sources[] = $row_source;
                    }
                } else {
                    error_log("Sensor Page - Failed to get potential sources result: " . mysqli_stmt_error($stmt_sources));
                }
                mysqli_stmt_close($stmt_sources);
            } else {
                error_log("Sensor Page - Failed to prepare potential sources query: " . mysqli_error($conn));
            }

        } else {
            // Jika perangkat tidak ditemukan di database (walaupun SN ada di URL), set pesan dan biarkan index.php redirect
            $_SESSION['pesan_feedback'] = ['tipe' => 'warning', 'judul' => 'Info', 'isi' => "Perangkat dengan SN '" . htmlspecialchars($device_sn_to_manage) . "' tidak ditemukan di database."];
            $device_sn_to_manage = null; // Set SN menjadi null agar bagian lain tidak mencoba query dengannya
        }
        mysqli_stmt_close($stmt_device);
    } else {
        $pesan_lokal_sensor = ['tipe' => 'danger', 'judul' => 'Error Database', 'isi' => 'Gagal mempersiapkan query detail perangkat: ' . mysqli_error($conn)];
        $device_sn_to_manage = null;
    }
} else {
    // Jika device_sn memang tidak ada di URL, pesan sudah di-set di awal oleh index.php
}


// --- Logika untuk Mode Edit (Ambil data sensor yang akan diedit) ---
if ($device_sn_to_manage && isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $sensor_id_to_edit = intval($_GET['edit']);
    $sql_edit = "SELECT id, device_serial_number, sensor_name, sensor_display_name, sensor_unit, sensor_category, sensor_type, communication_protocol, data_source, is_active, last_update, created_at FROM device_sensors WHERE id = ? AND device_serial_number = ?";
    $stmt_edit = mysqli_prepare($conn, $sql_edit);
    if ($stmt_edit) {
        mysqli_stmt_bind_param($stmt_edit, "is", $sensor_id_to_edit, $device_sn_to_manage);
        mysqli_stmt_execute($stmt_edit);
        $result_edit = mysqli_stmt_get_result($stmt_edit);
        if ($result_edit && mysqli_num_rows($result_edit) > 0) {
            $sensor_to_edit = mysqli_fetch_assoc($result_edit);
        } else {
            // Jika sensor dengan ID tersebut tidak ditemukan untuk perangkat ini
            $_SESSION['pesan_feedback'] = ['tipe' => 'warning', 'judul' => 'Info', 'isi' => "Sensor dengan ID '" . $sensor_id_to_edit . "' tidak ditemukan untuk perangkat ini."];
            // Tidak perlu redirect, biarkan index.php handle jika $sensor_to_edit null
        }
        mysqli_stmt_close($stmt_edit);
    } else {
        $pesan_lokal_sensor = ['tipe' => 'danger', 'judul' => 'Error Database', 'isi' => 'Gagal mempersiapkan query edit sensor: ' . mysqli_error($conn)];
    }
}

// --- AMBIL SEMUA SENSOR untuk Perangkat Ini (Hanya jika tidak sedang add atau edit) ---
// Hanya jalankan query list jika device_sn_to_manage ada DAN tidak dalam mode tambah/edit DAN tidak ada pesan error lokal sebelumnya
if ($device_sn_to_manage && !isset($_GET['edit']) && !isset($_GET['add']) && !$pesan_lokal_sensor) {
    $sql_sensors = "SELECT id, device_serial_number, sensor_name, sensor_display_name, sensor_unit, sensor_category, sensor_type, communication_protocol, data_source, is_active, last_update, created_at FROM device_sensors WHERE device_serial_number = ? ORDER BY sensor_name ASC";
    $stmt_sensors = mysqli_prepare($conn, $sql_sensors);
    if ($stmt_sensors) {
        mysqli_stmt_bind_param($stmt_sensors, "s", $device_sn_to_manage);
        if (mysqli_stmt_execute($stmt_sensors)) {
            $result_sensors = mysqli_stmt_get_result($stmt_sensors);
            if ($result_sensors) {
                $sensors_list = mysqli_fetch_all($result_sensors, MYSQLI_ASSOC);
            } else {
                $pesan_lokal_sensor = ['tipe' => 'danger', 'judul' => 'Error Database', 'isi' => 'Gagal mengambil hasil query daftar sensor: ' . mysqli_stmt_error($stmt_sensors)];
            }
        } else {
            $pesan_lokal_sensor = ['tipe' => 'danger', 'judul' => 'Error Database', 'isi' => 'Gagal mengeksekusi query daftar sensor: ' . mysqli_stmt_error($stmt_sensors)];
        }
        mysqli_stmt_close($stmt_sensors);
    } else {
        $pesan_lokal_sensor = ['tipe' => 'danger', 'judul' => 'Error Database', 'isi' => 'Gagal mempersiapkan query daftar sensor: ' . mysqli_error($conn)];
    }
}
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><b>Kelola Sensor</b></h1>
                <?php if($device_details): ?>
                    <h5 class="text-muted">Untuk Perangkat SN: <strong><?php echo htmlspecialchars($device_details['serial_number']); ?></strong></h5>
                <?php endif; ?>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php?page=device">Perangkat</a></li>
                    <li class="breadcrumb-item active">Kelola Sensor</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?php
        // Tampilkan pesan lokal dari halaman ini jika ada (dari error query SELECT)
        if (!empty($pesan_lokal_sensor)) {
            $tipe_alert_lokal = $pesan_lokal_sensor['tipe'];
            $ikon_alert_lokal = $tipe_alert_lokal === 'success' ? 'check' : ($tipe_alert_lokal === 'warning' ? 'exclamation-triangle' : 'ban');
            echo '<div class="alert alert-' . $tipe_alert_lokal . ' alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-' . $ikon_alert_lokal . '"></i> ' . htmlspecialchars($pesan_lokal_sensor['judul']) . '</h5>' . htmlspecialchars($pesan_lokal_sensor['isi']) . '
                </div>';
        }
        ?>
        <div class="row">
            <div class="col-lg-12">

                <!-- Tombol Kembali ke Halaman Perangkat -->
                <div class="mb-3">
                    <a href="?page=device" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Perangkat
                    </a>
                </div>
                <!-- Akhir Tombol Kembali -->

                <?php // --- TAMPILKAN TABEL LIST SENSOR ---
                // Tampilkan tabel list hanya jika device_sn valid DAN tidak dalam mode add/edit DAN tidak ada pesan error lokal
                if ($device_sn_to_manage && !isset($_GET['edit']) && !isset($_GET['add']) && !$pesan_lokal_sensor) : ?>
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Sensor Terdaftar</h3>
                            <a href="?page=<?php echo $page_current_sensor; ?>&device_sn=<?php echo urlencode($device_sn_to_manage); ?>&add=1" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah Sensor</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($sensors_list)): ?>
                        <table id="example1" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama (Internal)</th>
                                    <th>Nama Tampilan</th>
                                    <th>Satuan</th>
                                    <th>Kategori</th>
                                    <th>Tipe</th>
                                    <th>Komunikasi</th>
                                    <th>Sumber Data</th>
                                    <th>Status Sensor</th>
                                    <th>Update Terakhir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sensors_list as $sensor) { ?>
                                <tr>
                                    <td><?php echo $sensor['id']; ?></td>
                                    <td><code><?php echo htmlspecialchars($sensor['sensor_name']); ?></code></td>
                                    <td><?php echo htmlspecialchars($sensor['sensor_display_name']); ?></td>
                                    <td><?php echo htmlspecialchars($sensor['sensor_unit']); ?></td>
                                    <td><?php echo htmlspecialchars($sensor['sensor_category'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($sensor['sensor_type'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($sensor['communication_protocol'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($sensor['data_source']); ?></td>
                                    
                                    <!-- Kolom Status Sensor -->
                                    <td>
                                        <?php
                                        $last_update_formatted = 'Belum ada data'; // Inisialisasi untuk setiap sensor
                                        $status_text = "Tidak Diketahui";
                                        $status_class = "badge-secondary";

                                        if ($sensor['is_active']) {
                                            $status_text = "Aktif"; // Default jika aktif
                                            $status_class = "badge-success";

                                            if (!empty($sensor['last_update'])) {
                                                $last_update_timestamp = strtotime($sensor['last_update']);
                                                $last_update_formatted = htmlspecialchars(date('d M Y, H:i:s', $last_update_timestamp));

                                                if ((time() - $last_update_timestamp) > 600) { // > 10 menit
                                                    $status_text = "Aktif (Tidak Mengirim)";
                                                    $status_class = "badge-warning";
                                                } else {
                                                    $status_text = "Aktif & Mengirim";
                                                    $status_class = "badge-success";
                                                }
                                            } else { // Jika is_active true tapi last_update kosong/NULL
                                                $status_text = "Aktif (Belum Ada Data)";
                                                $status_class = "badge-secondary";
                                            }
                                            echo '<span class="badge ' . $status_class . '" title="Terakhir terlihat: ' . $last_update_formatted . '">' . $status_text . '</span>';
                                        } else { // Jika is_active false
                                            $status_text = "Tidak Aktif";
                                            $status_class = "badge-danger";
                                            if (!empty($sensor['last_update'])) {
                                                $last_update_timestamp = strtotime($sensor['last_update']);
                                                $last_update_formatted = htmlspecialchars(date('d M Y, H:i:s', $last_update_timestamp));
                                            }
                                            echo '<span class="badge ' . $status_class . '">' . $status_text . '</span>';
                                            // Tampilkan waktu terakhir update di bawah badge jika tidak aktif
                                            echo !empty($sensor['last_update']) ? '<br><small class="text-muted">Terakhir: ' . $last_update_formatted . '</small>' : '';
                                        }
                                        ?>
                                    </td>
                                    
                                    <!-- Kolom Update Terakhir -->
                                    <td>
                                        <?php echo $last_update_formatted; ?>
                                    </td>
                                    
                                    <!-- Kolom Aksi -->
                                    <td>
                                        <a href="?page=<?php echo $page_current_sensor; ?>&device_sn=<?php echo urlencode($device_sn_to_manage); ?>&edit=<?php echo $sensor['id']; ?>" class="btn btn-xs btn-warning" title="Edit Sensor"><i class="fas fa-edit"></i></a>
                                        <a href="?page=<?php echo $page_current_sensor; ?>&action=delete_sensor&id=<?php echo $sensor['id']; ?>&device_sn=<?php echo urlencode($device_sn_to_manage); ?>" class="btn btn-xs btn-danger ml-1" title="Hapus Sensor" onclick="return confirm('Yakin hapus sensor <?php echo htmlspecialchars($sensor['sensor_display_name']);?>?');"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <div class="callout callout-info">
                                <h5>Belum Ada Sensor</h5>
                                <p>Belum ada sensor yang didaftarkan untuk perangkat ini. Silakan tambahkan sensor baru menggunakan tombol "Tambah Sensor".</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php // --- FORM TAMBAH/EDIT SENSOR ---
                // Tampilkan form jika parameter 'add' ada ATAU parameter 'edit' ada DAN data sensor untuk diedit ditemukan
                if (isset($_GET['add']) || (isset($_GET['edit']) && $sensor_to_edit)) :
                    $isEditMode = isset($_GET['edit']);
                    $form_data = $isEditMode ? $sensor_to_edit : []; // Menggunakan data dari DB jika edit, atau array kosong jika tambah
                ?>
                <div class="card <?php echo $isEditMode ? 'card-warning' : 'card-primary'; ?>">
                    <div class="card-header"><h3 class="card-title"><?php echo $isEditMode ? 'Ubah Sensor' : 'Tambah Sensor Baru'; ?></h3></div>
                    <form method="post" action="?page=<?php echo $page_current_sensor; ?>">
                        <?php if ($isEditMode): ?>
                            <input type="hidden" name="edit_sensor_action" value="1">
                            <input type="hidden" name="sensor_id" value="<?php echo htmlspecialchars($form_data['id']); ?>">
                        <?php else: ?>
                            <input type="hidden" name="tambah_sensor_action" value="1">
                        <?php endif; ?>
                        <input type="hidden" name="device_serial_number" value="<?php echo htmlspecialchars($device_sn_to_manage); ?>"> <!-- Pastikan SN Perangkat Terkirim -->

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sensor_name">Nama Sensor (Internal/Unik)</label>
                                        <input type="text" class="form-control" id="sensor_name" name="sensor_name" value="<?php echo htmlspecialchars($form_data['sensor_name'] ?? ''); ?>" placeholder="misal: suhu_udara, ph_air" required <?php if ($isEditMode) echo 'readonly'; ?>>
                                        <small class="form-text text-muted">Gunakan nama yang sama dengan di topik MQTT. Hanya huruf, angka, dan underscore.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sensor_display_name">Nama Tampilan</label>
                                        <input type="text" class="form-control" id="sensor_display_name" name="sensor_display_name" value="<?php echo htmlspecialchars($form_data['sensor_display_name'] ?? ''); ?>" placeholder="misal: Suhu Udara, pH Air" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sensor_unit">Satuan</label>
                                        <input type="text" class="form-control" id="sensor_unit" name="sensor_unit" value="<?php echo htmlspecialchars($form_data['sensor_unit'] ?? ''); ?>" placeholder="misal: °C, %, ppm">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sensor_category">Kategori/Jenis Sensor</label>
                                        <select class="form-control" id="sensor_category" name="sensor_category">
                                            <option value="" <?php if (!($form_data['sensor_category'] ?? '')) echo 'selected'; ?>>- Pilih Kategori -</option>
                                            <option value="Kualitas Air" <?php if (($form_data['sensor_category'] ?? '') == 'Kualitas Air') echo 'selected'; ?>>Kualitas Air</option>
                                            <option value="Lingkungan" <?php if (($form_data['sensor_category'] ?? '') == 'Lingkungan') echo 'selected'; ?>>Lingkungan</option>
                                            <option value="Aktuator" <?php if (($form_data['sensor_category'] ?? '') == 'Aktuator') echo 'selected'; ?>>Aktuator</option>
                                            <option value="Lainnya" <?php if (($form_data['sensor_category'] ?? '') == 'Lainnya') echo 'selected'; ?>>Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sensor_type">Tipe Interfacing</label>
                                        <select class="form-control" id="sensor_type" name="sensor_type">
                                            <option value="" <?php if (!($form_data['sensor_type'] ?? '')) echo 'selected'; ?>>- Pilih Tipe -</option>
                                            <option value="Analog" <?php if (($form_data['sensor_type'] ?? '') == 'Analog') echo 'selected'; ?>>Analog</option>
                                            <option value="Digital" <?php if (($form_data['sensor_type'] ?? '') == 'Digital') echo 'selected'; ?>>Digital</option>
                                            <option value="I2C" <?php if (($form_data['sensor_type'] ?? '') == 'I2C') echo 'selected'; ?>>I2C</option>
                                            <option value="UART" <?php if (($form_data['sensor_type'] ?? '') == 'UART') echo 'selected'; ?>>UART</option>
                                            <option value="OneWire" <?php if (($form_data['sensor_type'] ?? '') == 'OneWire') echo 'selected'; ?>>OneWire</option>
                                            <option value="Pulse" <?php if (($form_data['sensor_type'] ?? '') == 'Pulse') echo 'selected'; ?>>Pulse (Interrupt)</option>
                                            <option value="Relay" <?php if (($form_data['sensor_type'] ?? '') == 'Relay') echo 'selected'; ?>>Relay/Output</option>
                                            <option value="Input" <?php if (($form_data['sensor_type'] ?? '') == 'Input') echo 'selected'; ?>>Input (Tombol)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="communication_protocol">Protokol Komunikasi</label>
                                        <input type="text" class="form-control" id="communication_protocol" name="communication_protocol" value="<?php echo htmlspecialchars($form_data['communication_protocol'] ?? ''); ?>" placeholder="misal: I2C, UART, Analog Pin">
                                    </div>
                                </div>
                                <div class="col-md-6"> 
                                    <div class="form-group">
                                        <label for="data_source">Sumber Data</label>
                                        <select class="form-control" id="data_source" name="data_source">
                                            <option value="Master" <?php if (($form_data['data_source'] ?? 'Master') == 'Master') echo 'selected'; ?>>Master (ESP32)</option>
                                            <option value="Slave" <?php if (($form_data['data_source'] ?? '') == 'Slave') echo 'selected'; ?>>Slave (Arduino Mega)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active_sensor" name="is_active" value="1" <?php if ($isEditMode ? ($form_data['is_active'] == 1) : true) echo 'checked'; ?>>
                                    <label class="custom-control-label" for="is_active_sensor">Sensor Aktif</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn <?php echo $isEditMode ? 'btn-warning' : 'btn-primary'; ?>">
                                <i class="fas fa-save mr-1"></i> <?php echo $isEditMode ? 'Simpan Perubahan' : 'Simpan Sensor'; ?>
                            </button>
                            <a href="?page=<?php echo $page_current_sensor; ?>&device_sn=<?php echo urlencode($device_sn_to_manage); ?>" class="btn btn-secondary ml-2">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div> 
    </div> 
</section>
