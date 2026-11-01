<?php
// =========================================================
// FINAL - dataactuator.php (SESUAI DB: data_actuator)
// =========================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan koneksi tersedia
if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Koneksi database tidak tersedia.");
}

// Query utama: ambil semua data aktuator
$sql = "
    SELECT id, serial_number, name, value, mqtt_topic, time
    FROM data_actuator
    ORDER BY time DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "<div class='alert alert-danger'>
            <b>Error Query:</b> " . htmlspecialchars(mysqli_error($conn)) . "
          </div>";
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>Riwayat Data Aktuator</b></h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Aktuator</h3>
                    </div>

                    <div class="card-body">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Serial Number</th>
                                        <th>Nama Aktuator</th>
                                        <th>Nilai</th>
                                        <th>Topic MQTT</th>
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
                                Belum ada data aktuator yang tersimpan di database.
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
