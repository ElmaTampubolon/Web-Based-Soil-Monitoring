<?php

if(!isset($_SESSION['role']) || $_SESSION['role'] != "Admin"){
    // Jika bukan admin, bisa redirect atau tampilkan pesan akses ditolak
    // Untuk konsistensi, kita set pesan feedback dan biarkan index.php yang redirect jika mau
    $_SESSION['pesan_feedback'] = ['tipe' => 'danger', 'judul' => 'Akses Ditolak!', 'isi' => 'Anda tidak memiliki izin untuk mengakses halaman ini.'];
    // Redirect bisa dilakukan dari sini jika mau, atau biarkan index.php yang handle jika ada pengecekan role global
    echo "<script> location.href='index.php?page=dashboard' </script>"; // Contoh redirect ke dashboard
    exit;
}

// --- Inisialisasi Variabel Lokal ---
$page_user = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'user';
$data_user_untuk_diedit = null;
$result_list_user = null;
$pesan_lokal_user = ''; // Untuk pesan error spesifik pengambilan data di halaman ini

// --- AMBIL DATA UNTUK EDIT (Jika parameter 'edit' ada di URL) ---
if (isset($_GET['edit'])) {
    $username_edit = $_GET['edit'];
    $sql_select_user_edit = "SELECT * FROM user WHERE username = ? LIMIT 1";
    $stmt_select_user_edit = mysqli_prepare($conn, $sql_select_user_edit);
    if ($stmt_select_user_edit) {
        mysqli_stmt_bind_param($stmt_select_user_edit, "s", $username_edit);
        mysqli_stmt_execute($stmt_select_user_edit);
        $result_data_user_edit = mysqli_stmt_get_result($stmt_select_user_edit);
        if ($result_data_user_edit && mysqli_num_rows($result_data_user_edit) > 0) {
            $data_user_untuk_diedit = mysqli_fetch_assoc($result_data_user_edit);
        } else {
            // Pesan sudah diatur di index.php jika ada upaya POST ke edit data yg tidak ada
            // Atau jika akses GET langsung ke edit yg tidak ada:
            if (!isset($_SESSION['pesan_feedback'])) {
                $_SESSION['pesan_feedback'] = ['tipe' => 'warning', 'judul' => 'Info!', 'isi' => "Data pengguna dengan username '$username_edit' tidak ditemukan."];
            }
            header("Location: ?page=" . urlencode($page_user)); // Redirect kembali ke list user
            exit;
        }
        mysqli_stmt_close($stmt_select_user_edit);
    } else {
        $pesan_lokal_user = ['tipe' => 'danger', 'judul' => 'Error!', 'isi' => 'Gagal mempersiapkan statement select data pengguna: ' . mysqli_error($conn)];
    }
}

// --- AMBIL SEMUA DATA PENGGUNA (Hanya jika tidak sedang add atau edit) ---
if (!isset($_GET['edit']) && !isset($_GET['add'])) {
    $sql_list_user = "SELECT username, fullname, role, active FROM user ORDER BY fullname ASC";
    $result_list_user = mysqli_query($conn, $sql_list_user);
    if (!$result_list_user) {
        $pesan_lokal_user = ['tipe' => 'danger', 'judul' => 'Error!', 'isi' => 'Gagal mengambil daftar pengguna: ' . mysqli_error($conn)];
    }
}
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>Manajemen Pengguna</b></h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <?php
            // Tampilkan pesan lokal jika ada (misal error pengambilan data)
            if (!empty($pesan_lokal_user)) {
                $tipe_alert_lokal = $pesan_lokal_user['tipe'];
                $ikon_alert_lokal = $tipe_alert_lokal === 'success' ? 'check' : ($tipe_alert_lokal === 'warning' ? 'exclamation-triangle' : 'ban');
                echo '<div class="alert alert-' . $tipe_alert_lokal . ' alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-' . $ikon_alert_lokal . '"></i> ' . htmlspecialchars($pesan_lokal_user['judul']) . '</h5>' . htmlspecialchars($pesan_lokal_user['isi']) . '
                    </div>';
            }
            ?>
        <div class="row">
            <div class="col-lg-12">
                <?php // --- TAMPILKAN TABEL LIST PENGGUNA ---
                        if (!isset($_GET['edit']) && !isset($_GET['add'])) { ?>
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Pengguna yang Terdaftar</h3>
                            <a href="?page=<?php echo htmlspecialchars($page_user) ?>&add=true"
                                class="btn btn-primary"><i class="fas fa-user-plus mr-1"></i> Tambah Pengguna</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($result_list_user && mysqli_num_rows($result_list_user) > 0): ?>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Hak Akses</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row_user = mysqli_fetch_assoc($result_list_user)) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row_user['username']) ?></td>
                                    <td><?php echo htmlspecialchars($row_user['fullname']) ?></td>
                                    <td><?php echo htmlspecialchars($row_user['role']) ?></td>
                                    <td>
                                        <?php echo ($row_user['active'] == 'Yes') ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>'; ?>
                                    </td>
                                    <td>
                                        <a href="?page=<?php echo htmlspecialchars($page_user) ?>&edit=<?php echo urlencode($row_user['username']) ?>"
                                            class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <!-- === TOMBOL HAPUS USER BARU === -->
                                        <?php if ($_SESSION['username'] !== $row_user['username']): // Admin tidak bisa hapus diri sendiri ?>
                                        <a href="?page=<?php echo htmlspecialchars($page_user) ?>&action=delete_user&username=<?php echo urlencode($row_user['username']) ?>"
                                            class="btn btn-sm btn-danger ml-1" title="Hapus Pengguna"
                                            onclick="return confirm('Anda yakin ingin menghapus pengguna <?php echo htmlspecialchars($row_user['username']); ?>? Aksi ini tidak dapat dibatalkan.');">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                        <?php endif; ?>
                                        <!-- ============================= -->
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="callout callout-info">
                            <h5>Belum Ada Pengguna</h5>
                            <p>Belum ada data pengguna yang terdaftar.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php } ?>

                <?php // --- FORM TAMBAH PENGGUNA ---
                        if (isset($_GET['add'])) { ?>
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Pengguna Baru</h3>
                    </div>
                    <!-- Action form akan ditangani oleh index.php -->
                    <form method="post" action="?page=<?php echo htmlspecialchars($page_user) ?>" id="formTambahUser">
                        <input type="hidden" name="tambah_user_action" value="1">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="addUsername">Username</label>
                                <input type="text" class="form-control" id="addUsername" name="username"
                                    placeholder="Username unik" required>
                            </div>
                            <div class="form-group">
                                <label for="addPassword">Password</label>
                                <input type="password" class="form-control" id="addPassword" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="addFullname">Nama Lengkap</label>
                                <input type="text" class="form-control" id="addFullname" name="fullname" required>
                            </div>
                            <div class="form-group">
                                <label for="addRole">Hak Akses</label>
                                <select class="form-control" id="addRole" name="role">
                                    <option value="User">Pengguna</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="addActive">Status</label>
                                <select class="form-control" id="addActive" name="active">
                                    <option value="Yes" selected>Aktif</option>
                                    <option value="No">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>
                                Simpan</button>
                            <a href="?page=<?php echo htmlspecialchars($page_user) ?>" class="btn btn-secondary ml-2"><i
                                    class="fas fa-times mr-1"></i> Batal</a>
                        </div>
                    </form>
                </div>
                <?php } ?>

                <?php // --- FORM EDIT PENGGUNA ---
                        if (isset($_GET['edit']) && $data_user_untuk_diedit) { ?>
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">Ubah Data Pengguna:
                            <?php echo htmlspecialchars($data_user_untuk_diedit['username']); ?></h3>
                    </div>
                    <!-- Action form akan ditangani oleh index.php -->
                    <form method="post" action="?page=<?php echo htmlspecialchars($page_user) ?>" id="formEditUser">
                        <input type="hidden" name="edit_user_action" value="1">
                        <input type="hidden" name="original_username"
                            value="<?php echo htmlspecialchars($data_user_untuk_diedit['username']) ?>">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="editUsername">Username</label>
                                <input type="text" class="form-control" id="editUsername" name="username"
                                    value="<?php echo htmlspecialchars($data_user_untuk_diedit['username']) ?>"
                                    placeholder="Username unik" required>
                            </div>
                            <div class="form-group">
                                <label for="editPassword">Password Baru</label>
                                <input type="password" class="form-control" id="editPassword" name="password"
                                    placeholder="Kosongkan jika tidak ingin mengubah password">
                                <small class="form-text text-muted">Kosongkan field ini jika Anda tidak ingin
                                    mengubah password.</small>
                            </div>
                            <div class="form-group">
                                <label for="editFullname">Nama Lengkap</label>
                                <input type="text" class="form-control" id="editFullname" name="fullname"
                                    value="<?php echo htmlspecialchars($data_user_untuk_diedit['fullname']) ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="editRole">Hak Akses</label>
                                <select class="form-control" id="editRole" name="role">
                                    <option value="Admin"
                                        <?php echo ($data_user_untuk_diedit['role'] == 'Admin') ? 'selected' : ''; ?>>
                                        Admin</option>
                                    <option value="User"
                                        <?php echo ($data_user_untuk_diedit['role'] == 'User') ? 'selected' : ''; ?>>
                                        Pengguna</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editActive">Status</label>
                                <select class="form-control" id="editActive" name="active">
                                    <option value="Yes"
                                        <?php echo ($data_user_untuk_diedit['active'] == 'Yes') ? 'selected' : ''; ?>>
                                        Aktif</option>
                                    <option value="No"
                                        <?php echo ($data_user_untuk_diedit['active'] == 'No') ? 'selected' : ''; ?>>
                                        Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i> Simpan
                                Perubahan</button>
                            <a href="?page=<?php echo htmlspecialchars($page_user) ?>" class="btn btn-secondary ml-2"><i
                                    class="fas fa-times mr-1"></i> Batal</a>
                        </div>
                    </form>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk menonaktifkan tombol submit (boleh diletakkan di footer.php jika global) -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('#formTambahUser, #formEditUser'); // Target form spesifik
        forms.forEach(form => {
            form.addEventListener('submit', function () {
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                }
            });
        });
    });
</script>