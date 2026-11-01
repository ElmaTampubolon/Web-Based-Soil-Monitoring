<?php
// File: page/device.php

// --- Inisialisasi Variabel Lokal ---
$page_device = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'device';
$data_untuk_diedit_device = null;
$result_list_perangkat_device = null;
$pesan_lokal_device = null;

// Inisialisasi variabel koordinat default di awal (akan digunakan oleh JavaScript)
$current_latitude_php = -6.200000;  // Default Jakarta Latitude
$current_longitude_php = 106.816666; // Default Jakarta Longitude

// --- AMBIL DATA UNTUK EDIT (Jika parameter 'edit' ada di URL) ---
if (isset($_GET['edit'])) {
    $id_edit_device = $_GET['edit'];
    // Ambil juga latitude dan longitude dari database untuk form edit
    $sql_select_data_edit_device = "SELECT serial_number, mcu_type, location, active, latitude, longitude, role, protocol FROM devices WHERE serial_number = ? LIMIT 1";
    $stmt_select_edit_device = mysqli_prepare($conn, $sql_select_data_edit_device);

    if ($stmt_select_edit_device) {
        mysqli_stmt_bind_param($stmt_select_edit_device, "s", $id_edit_device);
        mysqli_stmt_execute($stmt_select_edit_device);
        $result_data_edit_device = mysqli_stmt_get_result($stmt_select_edit_device);
        if ($result_data_edit_device && mysqli_num_rows($result_data_edit_device) > 0) {
            $data_untuk_diedit_device = mysqli_fetch_assoc($result_data_edit_device);
            // Update $current_latitude_php dan $current_longitude_php JIKA ADA data dari DB untuk mode edit
            if (isset($data_untuk_diedit_device['latitude']) && is_numeric($data_untuk_diedit_device['latitude'])) {
                $current_latitude_php = floatval($data_untuk_diedit_device['latitude']);
            }
            if (isset($data_untuk_diedit_device['longitude']) && is_numeric($data_untuk_diedit_device['longitude'])) {
                $current_longitude_php = floatval($data_untuk_diedit_device['longitude']);
            }
        } else {
            $_SESSION['pesan_feedback'] = ['tipe' => 'warning', 'judul' => 'Info', 'isi' => "Perangkat dengan SN '".htmlspecialchars($id_edit_device)."' tidak ditemukan."];
            // Jika data tidak ditemukan, sebaiknya redirect ke halaman list dari index.php setelah set pesan session
        }
        mysqli_stmt_close($stmt_select_edit_device);
    } else {
        $pesan_lokal_device = ['tipe' => 'danger', 'judul' => 'Error Database!', 'isi' => 'Gagal mempersiapkan query untuk mengambil data perangkat: ' . mysqli_error($conn)];
    }
} elseif (isset($_GET['add'])) {
    // Untuk mode tambah, $current_latitude_php dan $current_longitude_php akan menggunakan nilai default awal yang sudah di-set di atas.
}

// --- AMBIL SEMUA DATA PERANGKAT (Hanya jika tidak sedang add atau edit) ---
if (!isset($_GET['edit']) && !isset($_GET['add'])) {
    $sql_list_device = "SELECT serial_number, mcu_type, location, latitude, longitude, role, protocol, created_time, active FROM devices ORDER BY created_time DESC";
    $result_list_perangkat_device = mysqli_query($conn, $sql_list_device);
    if (!$result_list_perangkat_device) {
        $pesan_lokal_device = ['tipe' => 'danger', 'judul' => 'Error Database!', 'isi' => 'Gagal mengambil daftar perangkat: ' . mysqli_error($conn)];
    }
}
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><b>Manajemen Perangkat</b></h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                    <li class="breadcrumb-item active">Perangkat</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?php
        if (!empty($pesan_lokal_device)) { /* ... (Tampilkan pesan lokal device) ... */ }
        ?>
        <div class="row">
            <div class="col-lg-12">
                <?php if (!isset($_GET['edit']) && !isset($_GET['add'])) : ?>
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Perangkat yang Terdaftar</h3>
                            <a href="?page=<?php echo htmlspecialchars($page_device) ?>&add=true" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Tambah Perangkat</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($result_list_perangkat_device && mysqli_num_rows($result_list_perangkat_device) > 0): ?>
                        <table id="example1" class="table table-bordered table-striped"> 
                                <thead>
                                    <tr>
                                        <th>SN</th>
                                        <th>Kontroler</th>
                                        <th>Deskripsi Lokasi</th>
                                        <th>Koordinat</th>
                                        <th>Peran</th>    
                                        <th>Protokol</th> 
                                        <th>Terdaftar</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                            <tbody>
                                <?php while ($row_perangkat = mysqli_fetch_assoc($result_list_perangkat_device)) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row_perangkat['serial_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row_perangkat['mcu_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row_perangkat['location']); ?></td>
                                    <td>
                                        <?php if (!empty($row_perangkat['latitude']) && !empty($row_perangkat['longitude'])): ?>
                                            <a href="https://www.google.com/maps?q=<?php echo $row_perangkat['latitude']; ?>,<?php echo $row_perangkat['longitude']; ?>" target="_blank" title="Lihat di Peta">
                                                <?php echo htmlspecialchars(round($row_perangkat['latitude'], 5) . ', ' . round($row_perangkat['longitude'], 5)); ?>
                                            </a>
                                        <?php else: echo "-"; endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row_perangkat['role'] ?? '-'); ?></td>         <!-- TAMPILKAN PERAN -->
                                    <td><?php echo htmlspecialchars($row_perangkat['protocol'] ?? '-'); ?></td>     <!-- TAMPILKAN PROTOKOL -->
                                    <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($row_perangkat['created_time']))); ?></td>
                                    <td><?php echo ($row_perangkat['active'] == 'Yes') ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>'; ?></td>

                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-cog"></i> Aksi
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <!-- Link Edit: Pastikan kelas hanya dropdown-item -->
                                                <a class="dropdown-item" href="?page=<?php echo htmlspecialchars($page_device) ?>&edit=<?php echo urlencode($row_perangkat['serial_number']) ?>" title="Edit Perangkat">
                                                    <i class="fas fa-edit text-warning mr-2"></i> Edit Perangkat
                                                </a>
                                                <!-- Link Kelola Sensor: Perbaiki href -->
                                                <a class="dropdown-item" href="?page=device_sensors&device_sn=<?php echo urlencode($row_perangkat['serial_number']) ?>" title="Kelola Sensor">
                                                    <i class="fas fa-microchip text-info mr-2"></i> Kelola Sensor
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <!-- Link Hapus: Pastikan kelas hanya dropdown-item -->
                                                <a class="dropdown-item text-danger" href="?page=<?php echo htmlspecialchars($page_device) ?>&action=delete_device&sn=<?php echo urlencode($row_perangkat['serial_number']) ?>" title="Hapus Perangkat" onclick="return confirm('Yakin hapus perangkat SN <?php echo htmlspecialchars($row_perangkat['serial_number']);?>? Data terkait juga akan terhapus.');">
                                                    <i class="fas fa-trash mr-2"></i> Hapus Perangkat
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php else: echo "<p>Belum ada perangkat terdaftar.</p>"; endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['add'])) : ?>
                <div class="card card-primary">
                    <div class="card-header"><h3 class="card-title">Tambah Perangkat Baru</h3></div>
                    <form method="post" action="?page=<?php echo htmlspecialchars($page_device); ?>" id="formTambahPerangkat">
                        <input type="hidden" name="tambah_perangkat_action" value="1">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group"><label for="addSN">Serial Number <small class="text-muted">(max 8, unik)</small></label><input type="text" class="form-control" id="addSN" name="serial_number" maxlength="8" required></div>
                                    <div class="form-group"><label for="addControllerType">Jenis Kontroler</label><input type="text" class="form-control" id="addControllerType" name="controller_type" required></div>
                                    <div class="form-group"><label for="addLocationText">Deskripsi Lokasi (Opsional)</label><input type="text" class="form-control" id="addLocationText" name="location"></div>
                                    <div class="form-group"><label for="addLatitude">Latitude</label><input type="text" class="form-control" id="addLatitude" name="latitude" readonly required placeholder="Pilih di peta"></div>
                                    <div class="form-group"><label for="addLongitude">Longitude</label><input type="text" class="form-control" id="addLongitude" name="longitude" readonly required placeholder="Pilih di peta"></div>
                                    <div class="form-group"><label for="addActive">Status</label><select class="form-control" id="addActive" name="active"><option value="Yes" selected>Aktif</option><option value="No">Tidak Aktif</option></select></div>
                                    <div class="form-group">
                                        <label for="addRole">Peran Perangkat</label>
                                        <select class="form-control" id="addRole" name="role">
                                            <option value="">- Pilih Peran -</option>
                                            <option value="Master">Master</option>
                                            <option value="Slave">Slave</option>
                                            <option value="Standalone">Standalone</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="addProtocol">Protokol Utama</label>
                                        <input type="text" class="form-control" id="addProtocol" name="protocol" placeholder="Misal: MQTT, HTTP">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pac-input-add">Pilih Lokasi di Peta:</label>
                                        <div class="input-group mb-2">
                                            <input id="pac-input-add" class="form-control" type="text" placeholder="Ketik alamat atau nama tempat..." />
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" id="search-button-add"><i class="fas fa-search"></i> Cari</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="map-add" style="height: 360px; width: 100%; border: 1px solid #ced4da;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
                            <a href="?page=<?php echo htmlspecialchars($page_device); ?>" class="btn btn-secondary ml-2"><i class="fas fa-times mr-1"></i> Batal</a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['edit']) && $data_untuk_diedit_device) : ?>
                <div class="card card-warning">
                    <div class="card-header"><h3 class="card-title">Ubah Data Perangkat: <?php echo htmlspecialchars($data_untuk_diedit_device['serial_number']); ?></h3></div>
                    <form method="post" action="?page=<?php echo htmlspecialchars($page_device); ?>" id="formEditPerangkat">
                        <input type="hidden" name="edit_perangkat_action" value="1">
                        <input type="hidden" name="original_serial_number" value="<?php echo htmlspecialchars($data_untuk_diedit_device['serial_number']); ?>">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group"><label for="editSN">Serial Number</label><input type="text" class="form-control" id="editSN" name="serial_number" maxlength="8" value="<?php echo htmlspecialchars($data_untuk_diedit_device['serial_number']); ?>" required></div>
                                    <div class="form-group"><label for="editControllerType">Jenis Kontroler</label><input type="text" class="form-control" id="editControllerType" name="controller_type" value="<?php echo htmlspecialchars($data_untuk_diedit_device['mcu_type']); ?>" required></div>
                                    <div class="form-group"><label for="editLocationText">Deskripsi Lokasi (Opsional)</label><input type="text" class="form-control" id="editLocationText" name="location" value="<?php echo htmlspecialchars($data_untuk_diedit_device['location'] ?? ''); ?>"></div>
                                    <div class="form-group"><label for="editLatitude">Latitude</label><input type="text" class="form-control" id="editLatitude" name="latitude" value="<?php echo htmlspecialchars($data_untuk_diedit_device['latitude'] ?? ''); ?>" readonly required></div>
                                    <div class="form-group"><label for="editLongitude">Longitude</label><input type="text" class="form-control" id="editLongitude" name="longitude" value="<?php echo htmlspecialchars($data_untuk_diedit_device['longitude'] ?? ''); ?>" readonly required></div>
                                    <div class="form-group"><label for="editActive">Status</label>
                                        <select class="form-control" id="editActive" name="active">
                                            <?php
                                            $statusAktif = isset($data_untuk_diedit_device['active']) ? strtoupper(trim($data_untuk_diedit_device['active'])) : '';
                                            ?>
                                            <option value="Yes" <?php if ($statusAktif === 'YES') echo 'selected'; ?>>Aktif</option>
                                            <option value="No" <?php if ($statusAktif === 'NO') echo 'selected'; ?>>Tidak Aktif</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="editRole">Peran Perangkat</label>
                                        <select class="form-control" id="editRole" name="role">
                                            <?php
                                            $currentRole = isset($data_untuk_diedit_device['role']) ? trim($data_untuk_diedit_device['role']) : '';
                                            ?>
                                            <option value="" <?php if ($currentRole == '') echo 'selected'; ?>>- Pilih Peran -</option>
                                            <option value="Master" <?php if ($currentRole == 'Master') echo 'selected'; ?>>Master</option>
                                            <option value="Slave" <?php if ($currentRole == 'Slave') echo 'selected'; ?>>Slave</option>
                                            <option value="Standalone" <?php if ($currentRole == 'Standalone') echo 'selected'; ?>>Standalone</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="editProtocol">Protokol Utama</label>
                                        <input type="text" class="form-control" id="editProtocol" name="protocol" placeholder="Misal: MQTT, HTTP"
                                            value="<?php echo htmlspecialchars($data_untuk_diedit_device['protocol'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pac-input-edit">Pilih Lokasi di Peta:</label>
                                        <div class="input-group mb-2">
                                            <input id="pac-input-edit" class="form-control" type="text" placeholder="Ketik alamat atau nama tempat..." />
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" id="search-button-edit"><i class="fas fa-search"></i> Cari</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="map-edit" style="height: 360px; width: 100%; border: 1px solid #ced4da;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                            <a href="?page=<?php echo htmlspecialchars($page_device); ?>" class="btn btn-secondary ml-2"><i class="fas fa-times mr-1"></i> Batal</a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Elemen HTML tersembunyi untuk menyimpan koordinat default dari PHP -->
<div id="map-initial-coords"
    data-default-lat="<?php echo htmlspecialchars($current_latitude_php); ?>"
    data-default-lng="<?php echo htmlspecialchars($current_longitude_php); ?>"
    style="display: none;">
</div>

<!-- GOOGLE MAPS API SCRIPT -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaahN9p5eCHMyjWQCXDKrXfgD6BTIq6R0&libraries=places&callback=initMaps"></script>

<script>
// Variabel global untuk peta dan marker
let mapAdd, markerAdd;
let mapEdit, markerEdit;
let defaultLatGlobal, defaultLngGlobal; // Diubah namanya agar jelas ini global dari PHP

// Fungsi inti untuk inisialisasi satu peta
function initMapInstance(mapId, latInputId, lngInputId, searchInputId, searchButtonId, initialLat, initialLng) {
    const mapElement = document.getElementById(mapId);
    const latIn = document.getElementById(latInputId);
    const lngIn = document.getElementById(lngInputId);
    const searchIn = document.getElementById(searchInputId);
    const searchBtn = document.getElementById(searchButtonId);

    if (!mapElement) { console.error("Map element not found:", mapId); return null; }
    if (!latIn || !lngIn) { console.error("Lat/Lng input fields not found for map:", mapId); return null;}

    let currentMapLat = (typeof initialLat === 'number' && !isNaN(initialLat)) ? initialLat : -6.200000;
    let currentMapLng = (typeof initialLng === 'number' && !isNaN(initialLng)) ? initialLng : 106.816666;

    const map = new google.maps.Map(mapElement, { center: { lat: currentMapLat, lng: currentMapLng }, zoom: 15 });
    const marker = new google.maps.Marker({ position: { lat: currentMapLat, lng: currentMapLng }, map: map, draggable: true });

    latIn.value = currentMapLat.toFixed(8);
    lngIn.value = currentMapLng.toFixed(8);

    google.maps.event.addListener(marker, 'dragend', function(event) {
        latIn.value = event.latLng.lat().toFixed(8);
        lngIn.value = event.latLng.lng().toFixed(8);
    });
    google.maps.event.addListener(map, 'click', function(event) {
        marker.setPosition(event.latLng);
        latIn.value = event.latLng.lat().toFixed(8);
        lngIn.value = event.latLng.lng().toFixed(8);
    });

    if (searchIn && searchBtn) {
        const placesService = new google.maps.places.PlacesService(map);
        searchBtn.addEventListener('click', function() {
            const query = searchIn.value;
            if (query.trim() === "") { alert("Masukkan alamat atau nama tempat."); return; }
            const request = { query: query, fields: ['name', 'geometry', 'formatted_address']};
            placesService.textSearch(request, function(results, status) {
                if (status === google.maps.places.PlacesServiceStatus.OK && results && results.length > 0) {
                    const place = results[0];
                    if (place.geometry && place.geometry.location) {
                        map.setCenter(place.geometry.location); map.setZoom(17); marker.setPosition(place.geometry.location);
                        latIn.value = place.geometry.location.lat().toFixed(8); lngIn.value = place.geometry.location.lng().toFixed(8);
                        console.log("Place found:", place.name);
                    } else { alert("Hasil pencarian tidak memiliki detail koordinat."); }
                } else if (status === google.maps.places.PlacesServiceStatus.ZERO_RESULTS) { alert("Alamat/tempat tidak ditemukan."); }
                else { alert("Pencarian gagal: ".concat(status)); console.error("PlacesService error:", status); }
            });
        });
        searchIn.addEventListener('keypress', function(e) { if (e.key === 'Enter') { e.preventDefault(); searchBtn.click(); }});
    } else {
        if (!searchIn && (mapId === 'map-add' || mapId === 'map-edit')) console.warn("Search input for", mapId, "not found (ID:", searchInputId, ")");
        if (!searchBtn && (mapId === 'map-add' || mapId === 'map-edit')) console.warn("Search button for", mapId, "not found (ID:", searchButtonId, ")");
    }
    return { map, marker };
}

// Fungsi callback global yang dipanggil oleh Google Maps API
function initMaps() {
    console.log("Google Maps API loaded, initMaps called.");
    const coordsElement = document.getElementById('map-initial-coords');
    if (coordsElement) {
        defaultLatGlobal = parseFloat(coordsElement.getAttribute('data-default-lat'));
        defaultLngGlobal = parseFloat(coordsElement.getAttribute('data-default-lng'));
    }
    if (isNaN(defaultLatGlobal)) defaultLatGlobal = -6.200000;
    if (isNaN(defaultLngGlobal)) defaultLngGlobal = 106.816666;
    console.log("Default Coords from PHP (Global JS) -> Lat:", defaultLatGlobal, "Lng:", defaultLngGlobal);

    // Inisialisasi Peta untuk Form Tambah
    if (document.getElementById('map-add')) {
        console.log("Initializing map-add using global defaults...");
        const addResult = initMapInstance('map-add', 'addLatitude', 'addLongitude', 'pac-input-add', 'search-button-add', defaultLatGlobal, defaultLngGlobal);
        if (addResult) { mapAdd = addResult.map; markerAdd = addResult.marker; console.log("map-add OK.");}
        else { console.error("Failed to init map-add."); }
    }

    // Inisialisasi Peta untuk Form Edit
    if (document.getElementById('map-edit')) {
        console.log("Initializing map-edit...");
        const editLatIn = document.getElementById('editLatitude');
        const editLngIn = document.getElementById('editLongitude');
        
        let initialEditLat = defaultLatGlobal; // Mulai dengan default global
        let initialEditLng = defaultLngGlobal;

        // Override dengan nilai dari input field jika ada dan valid
        if (editLatIn && editLatIn.value.trim() !== "" && !isNaN(parseFloat(editLatIn.value))) {
            initialEditLat = parseFloat(editLatIn.value);
        } else {
             // Jika input editLat kosong atau tidak valid, gunakan defaultLatGlobal yang sudah di-set PHP
             // (yang seharusnya sudah berisi nilai edit jika ada, atau default umum jika tidak)
            console.warn("EditLatitude input is empty or not a number, using default global Lat:", defaultLatGlobal);
            initialEditLat = defaultLatGlobal;
        }
        if (editLngIn && editLngIn.value.trim() !== "" && !isNaN(parseFloat(editLngIn.value))) {
            initialEditLng = parseFloat(editLngIn.value);
        } else {
            console.warn("EditLongitude input is empty or not a number, using default global Lng:", defaultLngGlobal);
            initialEditLng = defaultLngGlobal;
        }
        console.log("Final Initial Edit Coords -> Lat:", initialEditLat, "Lng:", initialEditLng);
        
        const editResult = initMapInstance('map-edit', 'editLatitude', 'editLongitude', 'pac-input-edit', 'search-button-edit', initialEditLat, initialEditLng);
        if (editResult) { mapEdit = editResult.map; markerEdit = editResult.marker; console.log("map-edit OK.");}
        else { console.error("Failed to init map-edit."); }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('#formTambahPerangkat, #formEditPerangkat');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) { submitButton.disabled = true; }
        });
    });
});
</script>