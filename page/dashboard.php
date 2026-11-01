<?php
// File: page/dashboard.php
// Pastikan variabel $conn (koneksi database) sudah tersedia dari index.php
function getLatestSensorValue($conn, $sensorName)
{
    $sql = "SELECT value
            FROM data_sensor
            WHERE name = ?
            ORDER BY time DESC
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return '-';
    }

    mysqli_stmt_bind_param($stmt, "s", $sensorName);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $row ? $row['value'] : '-';
}

$suhu_tanah            = getLatestSensorValue($conn, 'suhu_tanah');
$kelembapan_tanah      = getLatestSensorValue($conn, 'kelembapan_tanah');
$ph                    = getLatestSensorValue($conn, 'ph');
$ec                    = getLatestSensorValue($conn, 'ec');

$nitrogen              = getLatestSensorValue($conn, 'nitrogen');
$fosfor                = getLatestSensorValue($conn, 'fosfor');
$kalium                = getLatestSensorValue($conn, 'kalium');

$suhu_lingkungan       = getLatestSensorValue($conn, 'suhu_lingkungan');
$kelembapan_lingkungan = getLatestSensorValue($conn, 'kelembapan_lingkungan');

$air                   = getLatestSensorValue($conn, 'air');
$kompos                = getLatestSensorValue($conn, 'kompos');
$kimia                 = getLatestSensorValue($conn, 'kimia');

$sql_devices = "SELECT serial_number, location, latitude, longitude 
FROM devices 
WHERE active = 'Yes' 
ORDER BY created_time DESC";

$result_devices = mysqli_query($conn, $sql_devices);

$devices_list = [];
$error_load_devices = null;

if (!$result_devices) {
    $error_load_devices = '<div class="alert alert-danger mx-3">
    Gagal memuat daftar perangkat: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
} else {
    $devices_list = mysqli_fetch_all($result_devices, MYSQLI_ASSOC);
}
?>

<div class="content-header">
    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">
                <h1 class="m-0"><b>Dashboard Pemantauan</b></h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Dashboard
                    </li>
                </ol>
            </div>

        </div>

    </div>
</div>


<div class="content">
<div class="container-fluid">

<div id="mqtt-disconnect-alert-placeholder" class="mb-3"></div>

<?php
if ($error_load_devices) {
    echo $error_load_devices;
}
?>

<div class="row d-flex align-items-stretch">

<!-- ================== KOLOM KIRI ================== -->

<div class="col-lg-6 col-md-12 d-flex">

<div class="card card-info card-outline mb-4 w-100">

<div class="card-header">

<h3 class="card-title">
<i class="fas fa-water mr-1"></i>
Kualitas Tanah & Lingkungan
</h3>

<div class="card-tools">
<button type="button" class="btn btn-tool" data-card-widget="collapse">
<i class="fas fa-minus"></i>
</button>
</div>

</div>


<div class="card-body">
<div class="row">

<div class="col-md-6 col-sm-12">
<div class="small-box bg-primary">
<div class="inner">
<h3><?php echo $suhu_tanah; ?> °C</h3>
<p>Suhu Tanah</p>
</div>
<div class="icon">
<i class="fas fa-thermometer-half"></i>
</div>
</div>
</div>


<div class="col-md-6 col-sm-12">
<div class="small-box bg-primary">
<div class="inner">
<h3><?php echo $kelembapan_tanah; ?> %</h3>
<p>Kelembapan Tanah</p>
</div>
<div class="icon">
<i class="fas fa-tint"></i>
</div>
</div>
</div>


<div class="col-md-6 col-sm-12">
<div class="small-box bg-success">
<div class="inner">
<h3><?php echo $nitrogen; ?> ppm</h3>
<p>Nitrogen</p>
</div>
<div class="icon">
<i class="fas fa-lungs"></i>
</div>
</div>
</div>


<div class="col-md-6 col-sm-12">
<div class="small-box bg-secondary">
<div class="inner">
<h3><?php echo $fosfor; ?> ppm</h3>
<p>Fosfor</p>
</div>
<div class="icon">
<i class="fas fa-cloud"></i>
</div>
</div>
</div>


<div class="col-md-6 col-sm-12">
<div class="small-box bg-warning text-white">
<div class="inner">
<h3><?php echo $kalium; ?> ppm</h3>
<p>Kalium</p>
</div>
<div class="icon">
<i class="fas fa-exclamation-triangle"></i>
</div>
</div>
</div>


<div class="col-md-6 col-sm-12">
<div class="small-box bg-danger">
<div class="inner">
<h3><?php echo $ph; ?>pH</h3>
<p>pH Tanah</p>
</div>
<div class="icon">
<i class="fas fa-flask"></i>
</div>
</div>
</div>


<div class="col-md-6 col-sm-12">
<div class="small-box bg-teal">
<div class="inner">
<h3><?php echo $ec; ?> µS/cm</h3>
<p>Konduktivitas Tanah (EC)</p>
</div>
<div class="icon">
<i class="fas fa-bolt"></i>
</div>
</div>
</div>


<div class="col-md-6 col-sm-12">
<div class="small-box bg-indigo">
<div class="inner">
<h3><?php echo $suhu_lingkungan; ?> °C</h3>
<p>Suhu Lingkungan</p>
</div>
<div class="icon">
<i class="fas fa-thermometer-half"></i>
</div>
</div>
</div>


<div class="col-md-6 col-sm-12">
<div class="small-box bg-primary">
<div class="inner">
<h3><?php echo $kelembapan_lingkungan; ?> %</h3>
<p>Kelembapan Lingkungan</p>
</div>
<div class="icon">
<i class="fas fa-tint"></i>
</div>
</div>
</div>

</div>
</div>

</div>
</div>


<!-- ================== KOLOM KANAN ================== -->

<div class="col-lg-6 col-md-12 d-flex flex-column">

<!-- CARD POMPA -->

<div class="card card-info card-outline mb-4 w-100">

<div class="card-header">

<h3 class="card-title">
<i class="fas fa-faucet mr-1"></i>
Otomatisasi Penyiraman Vitamin
</h3>

</div>

<div class="card-body">

<div class="row align-items-center mb-3">

<div class="col-5">
<strong>Kontrol:</strong>
</div>

<div class="col-7 text-right">

<div class="btn-group btn-group-toggle" data-toggle="buttons">

<label class="btn btn-outline-success" id="label-pompa-nyala">
<input type="radio" name="pompa" onchange="publishPompaState('manual')" id="pompa-nyala"> ON
</label>

<label class="btn btn-outline-danger active" id="label-pompa-mati">
<input type="radio" name="pompa" onchange="publishPompaState('manual')" id="pompa-mati" checked> OFF
</label>

</div>

</div>

</div>

</div>
</div>


<!-- MONITORING GALON -->
<div class="card card-info card-outline mb-4 w-100">

<div class="card-header">
<h3 class="card-title">
<i class="fas fa-database mr-1"></i>
Monitoring Level Galon
</h3>
</div>

<div class="card-body">

<div class="row">

<div class="col-md-4 col-sm-12">
<div class="small-box bg-info">
<div class="inner">
<h3><?php echo $air; ?> %</h3>
<p>Galon Air</p>
</div>
<div class="icon"><i class="fas fa-tint"></i></div>
</div>
</div>

<div class="col-md-4 col-sm-12">
<div class="small-box bg-success">
<div class="inner">
<h3><?php echo $kompos; ?> %</h3>
<p>Galon Kompos</p>
</div>
<div class="icon"><i class="fas fa-leaf"></i></div>
</div>
</div>

<div class="col-md-4 col-sm-12">
<div class="small-box bg-warning text-white">
<div class="inner">
<h3><?php echo $kimia; ?> %</h3>
<p>Galon Kimia</p>
</div>
<div class="icon"><i class="fas fa-flask"></i></div>
</div>
</div>

</div> <!-- row galon -->

</div> <!-- card-body -->
</div> <!-- card -->

</div> <!-- col kanan -->

</div> <!-- row utama -->

</div> <!-- container-fluid -->
</div> <!-- content -->