<?php 
/**
 * ==============================================
 * VIEW: Manajemen Staff - Admin Only
 * File: admin/v_staff.php
 * Fungsi: Tampilkan daftar staff + tombol tambah + flash message
 * Data dari: Dashboard.php case 'staff' -> $data['staff']
 * Table: KARYAWAN -> NAMA_STAFF, TELP_STAFF, JABATAN_STAFF
 * ==============================================
 */
?>

<div class="container-fluid px-4">

    <!-- HEADER + TOMBOL TAMBAH -->
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Manajemen Akun Pengguna</h3>
            <p class="text-muted small mb-0">Daftar staf internal kantor advokat yang memiliki hak akses sistem.</p>
        </div>
        
        <!-- Tombol ke Dashboard/tambah_staff -->
        <a href="<?= base_url('dashboard/tambah_staff'); ?>" class="btn btn-primary fw-medium shadow-sm">
            <i class="fas fa-user-plus me-2"></i> Tambah Pengguna Baru
        </a>
    </div>

    <!-- 
    FLASHDATA SUCCESS/ERROR 
    Muncul 1x setelah redirect dari controller
    Contoh: "Data staff berhasil ditambahkan!" 
    -->
    <?php if($this->session->flashdata('pesan')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= $this->session->flashdata('pesan'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if($this->session->flashdata('pesan_error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= $this->session->flashdata('pesan_error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- TABEL DATA STAFF -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60" class="text-center">No</th>
                            <th>Nama Lengkap</th>
                            <th>No. Telepon / ID Login</th>
                            <th>Jabatan / Role</th>
                            <th width="100" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($staff)): ?>
                            <?php $no = 1; foreach($staff as $row): ?>
                                <tr>
                                    <!-- Nomor urut auto increment -->
                                    <td class="text-center text-muted"><?= $no++; ?></td>
                                    
                                    <!-- Nama staff dari field NAMA_STAFF -->
                                    <td><span class="fw-semibold text-dark"><?= $row['NAMA_STAFF'] ?? '-'; ?></span></td>
                                    
                                    <!-- Telp/ID login pake badge monospace biar rapi -->
                                    <td><span class="badge bg-light text-dark border font-monospace px-3 py-2"><?= $row['TELP_STAFF'] ?? '-'; ?></span></td>
                                    
                                    <!-- Badge warna beda sesuai JABATAN_STAFF -->
                                    <td>
                                        <?php 
                                        $jbt = $row['JABATAN_STAFF'] ?? '-';
                                        $jbt_lower = strtolower($jbt);
                                        
                                        if ($jbt_lower == 'admin') {
                                            echo '<span class="badge bg-info text-dark px-3 py-2"><i class="fas fa-user-shield me-1"></i> Admin</span>';
                                        } elseif ($jbt_lower == 'kuasa hukum' || $jbt_lower == 'pengacara') {
                                            echo '<span class="badge bg-primary px-3 py-2"><i class="fas fa-gavel me-1"></i> Kuasa Hukum</span>';
                                        } elseif ($jbt_lower == 'keuangan') {
                                            echo '<span class="badge bg-success px-3 py-2"><i class="fas fa-wallet me-1"></i> Keuangan</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary px-3 py-2"><i class="fas fa-user me-1"></i> ' . $jbt . '</span>';
                                        }
                                        ?>
                                    </td>
                                    
                                    <!-- Status hardcode "Aktif" dulu. Nanti bisa tambah field STATUS_STAFF -->
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success border-success-subtle px-2 py-1 small">Aktif</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- EMPTY STATE: Kalo data staff kosong -->
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-users fs-3 text-white-50 mb-2 d-block"></i> Belum ada data staff internal.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- INFO BOX: Jelaskan arsitektur akun klien -->
    <div class="alert alert-info mt-4 border-0 shadow-sm d-flex align-items-start">
        <i class="fas fa-info-circle fs-4 me-3 mt-1"></i>
        <div>
            <h6 class="fw-bold mb-1">Informasi Hak Akses Klien:</h6>
            <p class="mb-0 small text-secondary">Sesuai arsitektur sistem, akun Klien disimpan di tabel <strong>PERKARA</strong> via `TELP_KLIEN`, bukan di tabel KARYAWAN. Jadi kelola klien lewat menu <strong>Manajemen Perkara</strong>.</p>
        </div>
    </div>

</div>