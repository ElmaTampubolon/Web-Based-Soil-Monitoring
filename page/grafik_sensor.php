<?php
// File: page/grafik_sensor.php
// Pastikan variabel $conn (koneksi database) sudah tersedia dari index.php

$daftar_sensor_tersedia = [
    'suhu_tanah' => 'Suhu Tanah',
    'kelembaban_tanah' => 'Kelembaban Tanah',
    'nitrogen' => 'Nitrogen (N)',
    'fosfor' => 'Fosfor (P)',
    'kalium' => 'Kalium (K)',
    'ph' => 'pH Tanah',
    'ec' => 'Konduktivitas Listrik (EC)',
    'suhu_lingkungan' => 'Suhu Lingkungan',
    'kelembapan_lingkungan' => 'Kelembapan Lingkungan',
];

// Ambil parameter filter dari URL jika ada, atau gunakan default
$device_sn_filter = isset($_GET['device']) ? htmlspecialchars(trim($_GET['device'])) : '12345678'; // Default SN
$time_range_filter = isset($_GET['range']) ? htmlspecialchars(trim($_GET['range'])) : '24h';    // Default 24 jam
$page_current_grafik = 'grafik_sensor'; // Nama halaman ini untuk link reset

// Ambil daftar perangkat yang tersedia untuk filter dropdown
$daftar_perangkat_tersedia = [];
if (isset($conn) && $conn) {
    $sql_devices = "SELECT serial_number, location FROM devices WHERE active = 'Yes' ORDER BY serial_number ASC";
    $result_devices = mysqli_query($conn, $sql_devices);
    if ($result_devices) {
        if (mysqli_num_rows($result_devices) > 0) {
            while ($row_device = mysqli_fetch_assoc($result_devices)) {
                $device_display_name = "Device " . htmlspecialchars($row_device['serial_number']);
                if (!empty($row_device['location'])) {
                    $device_display_name .= " (" . htmlspecialchars($row_device['location']) . ")";
                }
                $daftar_perangkat_tersedia[htmlspecialchars($row_device['serial_number'])] = $device_display_name;
            }
            mysqli_free_result($result_devices);
        }
    } else {
        error_log("Grafik Sensor Page - Error loading devices for filter: " . mysqli_error($conn));
    }
}

// Fallback jika daftar perangkat kosong atau SN filter tidak ada di list
$fallback_device_sn = '12345678';
if (empty($daftar_perangkat_tersedia)) {
    $daftar_perangkat_tersedia = [$fallback_device_sn => 'Device ' . $fallback_device_sn];
    // $device_sn_filter = $fallback_device_sn; // Tidak perlu diubah, PHP akan membaca dari GET jika ada
} elseif (!array_key_exists($device_sn_filter, $daftar_perangkat_tersedia)) {
    // Jika SN filter dari URL tidak ada di list perangkat, gunakan SN pertama dari list sebagai default filter
    $device_sn_filter = key($daftar_perangkat_tersedia);
}

?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><b>Grafik Data Sensor</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                    <li class="breadcrumb-item active">Grafik Sensor</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Data</h3>
                    </div>
                    <div class="card-body">
                        <form id="filterFormGrafik" method="GET" action="index.php">
                            <input type="hidden" name="page"
                                value="<?php echo htmlspecialchars($page_current_grafik); ?>">
                            <div class="row align-items-end">
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 form-group">
                                    <label for="selectDeviceSN" class="mr-2">Perangkat:</label>
                                    <select class="form-control form-control-sm" id="selectDeviceSN" name="device">
                                        <?php foreach ($daftar_perangkat_tersedia as $sn => $nama_device) : ?>
                                        <option value="<?php echo $sn; ?>"
                                            <?php echo ($device_sn_filter == $sn) ? 'selected' : ''; ?>>
                                            <?php echo $nama_device; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 form-group">
                                    <label for="selectTimeRange" class="mr-2">Rentang Waktu:</label>
                                    <select class="form-control form-control-sm" id="selectTimeRange" name="range">
                                        <option value="1h"
                                            <?php echo ($time_range_filter == '1h') ? 'selected' : ''; ?>>1 Jam</option>
                                        <option value="3h"
                                            <?php echo ($time_range_filter == '3h') ? 'selected' : ''; ?>>3 Jam</option>
                                        <option value="6h"
                                            <?php echo ($time_range_filter == '6h') ? 'selected' : ''; ?>>6 Jam</option>
                                        <option value="12h"
                                            <?php echo ($time_range_filter == '12h') ? 'selected' : ''; ?>>12 Jam
                                        </option>
                                        <option value="24h"
                                            <?php echo ($time_range_filter == '24h') ? 'selected' : ''; ?>>24 Jam
                                        </option>
                                        <option value="3d"
                                            <?php echo ($time_range_filter == '3d') ? 'selected' : ''; ?>>3 Hari
                                        </option>
                                        <option value="7d"
                                            <?php echo ($time_range_filter == '7d') ? 'selected' : ''; ?>>7 Hari
                                        </option>
                                        <option value="30d"
                                            <?php echo ($time_range_filter == '30d') ? 'selected' : ''; ?>>30 Hari
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 form-group">
                                    <label> </label>
                                    <div>
                                        <a href="?page=grafik_sensor" class="btn btn-secondary btn-sm mr-1">
                                            <i class="fas fa-undo"></i> Reset
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-chart-line"></i> Terapkan
                                        </button>
                                    </div>
                                </div>
                                <div class="col-lg-3"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="grafikSensorContainer">
            <?php foreach ($daftar_sensor_tersedia as $nama_internal => $nama_tampilan) : ?>
            <div class="col-lg-6 col-md-12">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="far fa-chart-bar mr-1"></i> Grafik
                            <?php echo htmlspecialchars($nama_tampilan); ?></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove"><i
                                    class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:300px; width:100%">
                            <canvas id="chart-<?php echo htmlspecialchars($nama_internal); ?>"></canvas>
                            <div id="loading-<?php echo htmlspecialchars($nama_internal); ?>"
                                class="chart-loading-overlay"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); display: flex; align-items: center; justify-content: center; z-index:10; font-style: italic; color: #555;">
                                <i class="fas fa-spinner fa-spin fa-2x mr-2"></i><span>Memuat data...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($daftar_sensor_tersedia)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Info</h5>
                    Daftar sensor yang akan ditampilkan grafik masih kosong. Silakan tambahkan konfigurasi sensor di
                    file PHP.
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Chart.js sudah di-include di index.php -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ambil nilai filter saat ini dari PHP untuk digunakan JavaScript
    // Variabel ini hanya digunakan di dalam scope DOMContentLoaded ini
    const graphDeviceSN = '<?php echo $device_sn_filter; ?>'; // <<< Nama variabel diganti
    const graphTimeRange = '<?php echo $time_range_filter; ?>'; // <<< Nama variabel diganti

    const charts = {}; // Objek untuk menyimpan instance chart

    // Konfigurasi sensor dari PHP
    const sensorConfig = <?php echo json_encode($daftar_sensor_tersedia); ?>;
    const sensorSatuan = {
        'suhu_tanah': '°C',
        'kelembaban_tanah': '%',
        'natrium': 'ppm',
        'fosfor': 'm/s',
        'kalium': '°C',
        'pH_tanah': 'mg/L',
        'konduktivitas_tanah': 'µS/cm',
        'udara_suhu': 'NTU',
        'udara_kelembapan': '%'
    };

    // --- Fungsi Helper untuk UI ---
    function toggleLoading(sensorInternalName, isLoading) {
        const loadingOverlay = document.getElementById(`loading-${sensorInternalName}`);
        if (loadingOverlay) {
            loadingOverlay.style.display = isLoading ? 'flex' : 'none';
        }
    }

    // --- Fungsi Utama untuk Mengambil dan Merender Data per Sensor ---
    function fetchDataAndRender(sensorInternalName, sensorDisplayName, deviceSN, timeRange) {
        // Basic validation
        if (!sensorInternalName || !deviceSN || !timeRange) {
            console.error("Missing required parameters for fetchDataAndRender:", {
                sensorInternalName,
                deviceSN,
                timeRange
            });
            const canvasId = `chart-${sensorInternalName}`;
            const ctxElement = document.getElementById(canvasId);
            if (ctxElement) {
                const chartContext = ctxElement.getContext('2d');
                chartContext.clearRect(0, 0, ctxElement.width, ctxElement.height);
                chartContext.font = "14px Arial";
                chartContext.textAlign = "center";
                chartContext.fillStyle = "#dc3545";
                chartContext.fillText("Parameter tidak lengkap.", ctxElement.width / 2, ctxElement.height / 2);
            }
            return;
        }

        const canvasId = `chart-${sensorInternalName}`;
        const ctxElement = document.getElementById(canvasId);
        if (!ctxElement) {
            console.error(`Canvas with ID ${canvasId} not found.`);
            toggleLoading(sensorInternalName, false);
            return;
        }
        const chartContext = ctxElement.getContext('2d');
        toggleLoading(sensorInternalName, true);

        const apiUrl =
            `api/get_sensor_data.php?sensor=${encodeURIComponent(sensorInternalName)}&device=${encodeURIComponent(deviceSN)}&range=${encodeURIComponent(timeRange)}`;
        console.log("Fetching:", apiUrl); // Log URL API

        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    // Attempt to read response as text to see PHP errors
                    return response.text().then(text => {
                        // Log response body even if it's not error
                        console.error(`API Response (Non-OK Status ${response.status}):`, text);
                        throw new Error(
                            `HTTP error! status: ${response.status}, statusText: ${response.statusText}, responseBody: ${text}`
                            );
                    });
                }
                return response.json(); // Continue if OK
            })
            .then(apiResponse => {
                toggleLoading(sensorInternalName, false);

                // Destroy existing chart instance if it exists
                if (charts[sensorInternalName]) {
                    charts[sensorInternalName].destroy();
                    delete charts[sensorInternalName]; // Remove reference
                }

                // Check API response status and data
                if (apiResponse && apiResponse.status === 'success' && apiResponse
                    .data) { // Check apiResponse and data key
                    if (apiResponse.data.length > 0) {
                        console.log(`Data received for ${sensorDisplayName}:`, apiResponse.data.length,
                            "entries.");

                        // Map data for Chart.js
                        const labels = apiResponse.data.map(item => {
                            if (typeof item.timestamp !== 'string') return 'Invalid Date';
                            const date = new Date(item.timestamp.replace(' ', 'T') +
                            'Z'); // Assuming UTC from DB
                            if (isNaN(date.getTime()))
                        return 'Invalid Date'; // Check if date parsing failed
                            return date.toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                hour12: false
                            });
                        });

                        const values = apiResponse.data.map(item => parseFloat(item.value));
                        const satuanY = sensorSatuan[sensorInternalName] || 'Nilai';

                        // Render Chart
                        charts[sensorInternalName] = new Chart(chartContext, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: sensorDisplayName,
                                    data: values,
                                    borderColor: getRandomColor(),
                                    tension: 0.2,
                                    fill: false,
                                    pointRadius: 2,
                                    pointHoverRadius: 5,
                                    borderWidth: 1.5
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Waktu'
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: `${sensorDisplayName} (${satuanY})`
                                        },
                                        beginAtZero: false
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false,
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                if (context.parsed.y !== null) {
                                                    label += context.parsed.y.toLocaleString(
                                                        undefined, {
                                                            minimumFractionDigits: 0,
                                                            maximumFractionDigits: 2
                                                        }) + (sensorSatuan[
                                                            sensorInternalName] ? ' ' +
                                                        sensorSatuan[sensorInternalName] :
                                                        '');
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        // No data found
                        console.warn(
                            `Grafik ${sensorDisplayName}: ${apiResponse.message || 'Tidak ada data ditemukan untuk kriteria ini.'}`
                            );
                        chartContext.clearRect(0, 0, ctxElement.width, ctxElement.height);
                        chartContext.font = "14px Arial";
                        chartContext.textAlign = "center";
                        chartContext.fillStyle = "#666";
                        chartContext.fillText(apiResponse.message || "Tidak ada data untuk periode ini.",
                            ctxElement.width / 2, ctxElement.height / 2);
                    }
                } else {
                    // API response status is not 'success' or data key is missing
                    console.error(
                        `Grafik ${sensorDisplayName}: Respon API tidak sukses atau format tidak sesuai.`,
                        apiResponse);
                    chartContext.clearRect(0, 0, ctxElement.width, ctxElement.height);
                    chartContext.font = "14px Arial";
                    chartContext.textAlign = "center";
                    chartContext.fillStyle = "#dc3545";
                    chartContext.fillText("Gagal memuat data (respon tidak valid).", ctxElement.width / 2,
                        ctxElement.height / 2);
                }
            })
            .catch(error => {
                toggleLoading(sensorInternalName, false);
                console.error(`Error fetching/processing data untuk ${sensorDisplayName}:`, error);
                chartContext.clearRect(0, 0, ctxElement.width, ctxElement.height);
                chartContext.font = "14px Arial";
                chartContext.textAlign = "center";
                chartContext.fillStyle = "#dc3545";

                let errorMessageDisplay = "Gagal memuat data grafik.";
                if (error instanceof Error && error.message) {
                    // Try to parse the error message body for PHP error details
                    const match = error.message.match(/responseBody: ([\s\S]+)/);
                    if (match && match[1]) {
                        // If PHP error details are found, log the raw body
                        console.error("Raw API response body (likely PHP error):", match[1]);
                        errorMessageDisplay =
                        "Gagal memuat data grafik (API Error)."; // Generic error message for display
                    } else {
                        errorMessageDisplay = error.message; // Fallback to original error message
                    }
                }

                chartContext.fillText(errorMessageDisplay, ctxElement.width / 2, ctxElement.height / 2);
            });
    }

    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    // Load all charts function
    function loadAllCharts(deviceSN, timeRange) {
        // Basic validation of inputs
        if (!deviceSN || !timeRange) {
            console.warn("Parameter Device SN atau Time Range kosong saat loadAllCharts.", "Device:", deviceSN,
                "Range:", timeRange);
            // Display a message in the container
            const container = document.getElementById('grafikSensorContainer');
            if (container) {
                container.innerHTML =
                    '<div class="col-12"><div class="alert alert-warning">Filter tidak valid untuk memuat grafik.</div></div>';
                // Destroy any existing charts if parameters become invalid
                for (const chartKey in charts) {
                    if (charts[chartKey]) charts[chartKey].destroy();
                    delete charts[chartKey];
                }
            }
            return; // Stop execution if parameters are invalid
        }

        console.log(`Memuat semua grafik. Device: ${deviceSN}, Range: ${timeRange}`);

        // Destroy and clear existing charts before loading new ones
        for (const chartKey in charts) {
            if (charts[chartKey]) {
                charts[chartKey].destroy();
                delete charts[chartKey]; // Remove reference
            }
            // Clear canvas and show 'Loading...' message
            const oldCtx = document.getElementById(`chart-${chartKey}`);
            if (oldCtx) {
                const oldChartContext = oldCtx.getContext('2d');
                oldChartContext.clearRect(0, 0, oldCtx.width, oldCtx.height);
                oldChartContext.font = "14px Arial";
                oldChartContext.textAlign = "center";
                oldChartContext.fillStyle = "#666";
                oldChartContext.fillText("Memuat...", oldCtx.width / 2, oldCtx.height / 2);
            }
        }

        // Fetch data and render chart for each sensor
        for (const [internalName, displayName] of Object.entries(sensorConfig)) {
            fetchDataAndRender(internalName, displayName, deviceSN,
            timeRange); // Use deviceSN and timeRange from arguments
        }
    }

    // Ambil nilai filter saat ini saat DOMContentLoaded (dari PHP)
    // Variabel ini hanya digunakan di dalam scope DOMContentLoaded ini
    const pageLoadDeviceSN = '<?php echo $device_sn_filter; ?>'; // <<< Variabel dinamai ulang untuk kejelasan
    const pageLoadTimeRange = '<?php echo $time_range_filter; ?>'; // <<< Variabel dinamai ulang

    // Muat grafik awal saat halaman dimuat berdasarkan parameter URL (atau default dari PHP)
    loadAllCharts(pageLoadDeviceSN, pageLoadTimeRange); // <<< Menggunakan nama variabel yang dinamai ulang

    // Tombol Terapkan Filter (type="submit") akan menyebabkan halaman reload.
    // JavaScript untuk handle klik tombol ini tidak diperlukan lagi.
    // loadAllCharts akan berjalan otomatis saat DOMContentLoaded dengan parameter URL yang baru.

    // Jika Anda tetap ingin menggunakan tombol type="button" dan fetch via JS (tanpa reload):
    // const btnApplyFilterJS = document.getElementById('btnApplyFilterGrafikJS'); // Ganti type="submit" menjadi type="button" di HTML
    // const formFilter = document.getElementById('filterFormGrafik');
    // if (btnApplyFilterJS) {
    //     btnApplyFilterJS.addEventListener('click', function(event) {
    //         event.preventDefault(); // Mencegah submit form jika pakai type="button"
    //         const selectedDevice = document.getElementById('selectDeviceSN').value;
    //         const selectedRange = document.getElementById('selectTimeRange').value;
    //         loadAllCharts(selectedDevice, selectedRange);
    //     });
    // }
});
</script>