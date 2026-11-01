<?php
// =========================================================
//  FINAL - datasensor.php (SESUAI DB: data_sensor, devices)
// =========================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan $conn sudah ada dari include koneksi di index.php
if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Koneksi database (\$conn) tidak tersedia. Pastikan file koneksi sudah di-include sebelum datasensor.php.");
}

$stmt = null;

// -------------------------
// Ambil parameter filter
// -------------------------
$filter_tanggal_mulai  = isset($_GET['tanggal_mulai']) ? trim($_GET['tanggal_mulai']) : '';
$filter_tanggal_akhir  = isset($_GET['tanggal_akhir']) ? trim($_GET['tanggal_akhir']) : '';
$filter_nama_sensor    = isset($_GET['nama_sensor']) ? trim($_GET['nama_sensor']) : '';
$filter_serial_number  = isset($_GET['serial_number']) ? trim($_GET['serial_number']) : '';

// Basic sanitization untuk output HTML (bukan untuk SQL, karena kita pakai prepared statement)
$filter_tanggal_mulai_safe = htmlspecialchars($filter_tanggal_mulai);
$filter_tanggal_akhir_safe = htmlspecialchars($filter_tanggal_akhir);
$filter_nama_sensor_safe   = htmlspecialchars($filter_nama_sensor);
$filter_serial_number_safe = htmlspecialchars($filter_serial_number);

// -------------------------
// Bangun query utama
// (DB kamu: data_sensor)
// Kolom: id, serial_number, name, value, time, mqtt_topic, data_sensor
// -------------------------
$sql_conditions = [];
$bind_types = "";
$bind_values = [];

if ($filter_tanggal_mulai !== '') {
    $sql_conditions[] = "DATE(time) >= ?";
    $bind_types .= "s";
    $bind_values[] = $filter_tanggal_mulai;
}

if ($filter_tanggal_akhir !== '') {
    $sql_conditions[] = "DATE(time) <= ?";
    $bind_types .= "s";
    $bind_values[] = $filter_tanggal_akhir;
}

if ($filter_nama_sensor !== '') {
    $sql_conditions[] = "name = ?";
    $bind_types .= "s";
    $bind_values[] = $filter_nama_sensor;
}

if ($filter_serial_number !== '') {
    $sql_conditions[] = "serial_number = ?";
    $bind_types .= "s";
    $bind_values[] = $filter_serial_number;
}

$sql = "SELECT id, serial_number, name, value, mqtt_topic, time
        FROM data_sensor";

if (!empty($sql_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $sql_conditions);
}

$sql .= " ORDER BY time DESC";

// -------------------------
// Eksekusi query utama
// -------------------------
$result = false;

if ($bind_types !== "") {
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        echo "<div class='alert alert-danger'><b>Error</b>: Gagal prepare query. " . htmlspecialchars(mysqli_error($conn)) . "</div>";
    } else {
        // Bind param dinamis
        $bind_params = [];
        $bind_params[] = $bind_types;

        // IMPORTANT: bind_param butuh reference
        foreach ($bind_values as $k => $v) {
            $bind_params[] = &$bind_values[$k];
        }

        // Panggil bind_param
        call_user_func_array([$stmt, 'bind_param'], $bind_params);

        if (!mysqli_stmt_execute($stmt)) {
            echo "<div class='alert alert-danger'><b>Error</b>: Gagal execute query. " . htmlspecialchars(mysqli_stmt_error($stmt)) . "</div>";
        } else {
            $result = mysqli_stmt_get_result($stmt);
            if (!$result) {
                echo "<div class='alert alert-danger'><b>Error</b>: Gagal ambil hasil query. " . htmlspecialchars(mysqli_stmt_error($stmt)) . "</div>";
            }
        }
    }
} else {
    // Tanpa filter
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo "<div class='alert alert-danger'><b>Error</b>: Gagal menjalankan query. " . htmlspecialchars(mysqli_error($conn)) . "</div>";
    }
}

// -------------------------
// Dropdown: daftar unik sensor (ambil dari data_sensor.name)
// -------------------------
$unique_sensor_names = [];
$sql_sensor_names = "SELECT DISTINCT name FROM data_sensor ORDER BY name ASC";
$res_sensor_names = mysqli_query($conn, $sql_sensor_names);
if ($res_sensor_names) {
    while ($row = mysqli_fetch_assoc($res_sensor_names)) {
        $unique_sensor_names[] = $row['name'];
    }
}

// -------------------------
// Dropdown: daftar unik serial number (ambil dari devices.serial_number)
// -------------------------
$unique_serial_numbers = [];
$sql_serial_numbers = "SELECT DISTINCT serial_number FROM devices ORDER BY serial_number ASC";
$res_serial_numbers = mysqli_query($conn, $sql_serial_numbers);
if ($res_serial_numbers) {
    while ($row = mysqli_fetch_assoc($res_serial_numbers)) {
        $unique_serial_numbers[] = $row['serial_number'];
    }
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>Riwayat Data Sensor</b></h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- Form Filter -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Data Sensor</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <form method="GET" action="index.php">
                    <input type="hidden" name="page" value="datasensor">

                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="form-group">
                                <label for="filterTanggalMulai">Tanggal Mulai:</label>
                                <input type="date"
                                       class="form-control form-control-sm"
                                       id="filterTanggalMulai"
                                       name="tanggal_mulai"
                                       value="<?php echo $filter_tanggal_mulai_safe; ?>">
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="form-group">
                                <label for="filterTanggalAkhir">Tanggal Akhir:</label>
                                <input type="date"
                                       class="form-control form-control-sm"
                                       id="filterTanggalAkhir"
                                       name="tanggal_akhir"
                                       value="<?php echo $filter_tanggal_akhir_safe; ?>">
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="form-group">
                                <label for="filterNamaSensor">Nama Sensor:</label>
                                <select class="form-control form-control-sm" id="filterNamaSensor" name="nama_sensor">
                                    <option value="">Semua Sensor</option>
                                    <?php foreach ($unique_sensor_names as $name_internal): ?>
                                        <?php
                                            $display_name = ucwords(str_replace('_', ' ', $name_internal));
                                            $selected = ($filter_nama_sensor === $name_internal) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo htmlspecialchars($name_internal); ?>" <?php echo $selected; ?>>
                                            <?php echo htmlspecialchars($display_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="form-group">
                                <label for="filterSerialNumber">Serial Number:</label>
                                <select class="form-control form-control-sm" id="filterSerialNumber" name="serial_number">
                                    <option value="">Semua Perangkat</option>
                                    <?php foreach ($unique_serial_numbers as $sn): ?>
                                        <?php $selected = ($filter_serial_number === $sn) ? 'selected' : ''; ?>
                                        <option value="<?php echo htmlspecialchars($sn); ?>" <?php echo $selected; ?>>
                                            <?php echo htmlspecialchars($sn); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12 text-right">
                            <a href="?page=datasensor" class="btn btn-secondary btn-sm mb-2">Reset Filter</a>
                            <button type="submit" class="btn btn-primary btn-sm ml-2 mb-2">
                                <i class="fas fa-search mr-1"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Data Sensor -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Sensor</h3>
                    </div>

                    <div class="card-body">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Serial Number</th>
                                        <th>Name</th>
                                        <th>Nilai</th>
                                        <th>Topic</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo (int)$row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['serial_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['value']); ?></td>
                                            <td><?php echo htmlspecialchars($row['mqtt_topic']); ?></td>
                                            <td><?php echo htmlspecialchars(date('d M Y, H:i:s', strtotime($row['time']))); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Info</h5>
                                Tidak ada data sensor yang cocok dengan kriteria filter Anda, atau tabel <b>data_sensor</b> masih kosong.
                            </div>
                        <?php endif; ?>

                        <?php
                        if ($stmt && ($stmt instanceof mysqli_stmt)) {
                            mysqli_stmt_close($stmt);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
