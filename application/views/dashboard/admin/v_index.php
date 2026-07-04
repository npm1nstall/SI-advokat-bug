<?php 
/**
 * ==============================================
 * VIEW: Dashboard Admin
 * File: admin/v_index.php
 * Fungsi: Tampilkan summary KPI Admin + Log aktivitas
 * Data dari: Dashboard.php case 'Admin'
 * Variabel: $jml_perkara, $jml_surat, $jml_staff
 * ==============================================
 */
?>

<div class="container-fluid p-3">
    <!-- Judul dinamis sesuai jabatan di session -->
    <h2 class="mt-4 fw-bold">Dashboard <?= $this->session->userdata('jabatan'); ?></h2>
    <p class="text-muted">Halo <?= $this->session->userdata('nama'); ?>, selamat datang di sistem.</p>

    <?php 
    /**
     * CARD SUMMARY KHUS ADMIN
     * 3 card: Perkara Aktif, Surat Masuk, Staff Aktif
     * Data $jml_perkara dll dari Dashboard.php -> $this->M_perkara->get_all()
     * Style: border-start warna beda + icon FontAwesome
     */
    if ($this->session->userdata('jabatan') == 'Admin'): 
    ?>
    <div class="row g-3">
        <!-- Card 1: Perkara Aktif - Border biru -->
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded border-start border-primary border-5">
                <div>
                    <h3 class="fw-bold mb-0"><?= isset($jml_perkara) ? $jml_perkara : 0; ?></h3>
                    <p class="fs-6 text-muted">Perkara Aktif</p>
                </div>
                <i class="fas fa-gavel fs-1 text-primary"></i>
            </div>
        </div>

        <!-- Card 2: Surat Masuk - Border kuning -->
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded border-start border-warning border-5">
                <div>
                    <h3 class="fs-2"><?= isset($jml_surat) ? $jml_surat : 0; ?></h3>
                    <p class="fs-6 text-muted">Surat Masuk</p>
                </div>
                <i class="fas fa-envelope fs-1 text-warning"></i>
            </div>
        </div>

        <!-- Card 3: Staff Aktif - Border hijau -->
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded border-start border-success border-5">
                <div>
                    <h3 class="fs-2"><?= isset($jml_staff) ? $jml_staff : 0; ?></h3>
                    <p class="fs-6 text-muted">Staff Aktif</p>
                </div>
                <i class="fas fa-users fs-1 text-success"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- 
    SECTION LOG AKTIVITAS 
    Placeholder dulu. Nanti bisa diisi data dari tabel LOG/ACTIVITY
    Dipake buat nunjukin HRD kamu kepikiran audit trail
    -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i> Log Aktivitas Terakhir</h5>
                </div>
                <div class="card-body">
                    <p>Daftar aktivitas terkini akan muncul di sini...</p>
                    <!-- TODO: Loop data log aktivitas dari database -->
                </div>
            </div>
        </div>
    </div>
</div>