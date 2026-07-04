<?php 
/**
 * ==============================================================
 * VIEW: DASHBOARD KUASA HUKUM
 * File: kuasa_hukum/kuasa_hukum_index.php
 * Fungsi: Halaman utama Kuasa Hukum setelah login
 * Isi: KPI Ringkas + Tabel Antrean Berkas "Pending Validasi"
 * Controller: Dashboard::kuasa_hukum_index()
 * ==============================================================
 */
?>

<div class="container-fluid px-4 py-2">
    <!-- py-2 dibikin rapat biar gak kejauhan dari navbar -->
    
    <!-- ROW KPI: 2 CARD PENTING BUAT KUASA HUKUM -->
    <div class="row">
        
        <!-- CARD 1: TOTAL PERKARA YANG DITANGANI -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Perkara Aktif
                    </div>
                    <!-- Data dinamis dari controller, fallback 0 biar gak error -->
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= isset($jml_perkara) ? $jml_perkara : 0 ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- CARD 2: JADWAL SIDANG YANG AKAN DATANG -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Jadwal Sidang Mendatang
                    </div>
                    <!-- Data dinamis dari controller -->
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= isset($jml_sidang) ? $jml_sidang : 0 ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- CARD UTAMA: TABEL ANTREAN BERKAS YANG HARUS DIVALIDASI -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Daftar Perkara Saya
            </h6>
        </div>
        <div class="card-body">
            <!-- table-responsive biar mobile friendly -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No Perkara</th>
                            <th>Nama Klien</th>
                            <th>Judul Perkara</th>
                            <th>Status Posisi</th>
                            <th width="140" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pengajuan)): ?>
                            <?php foreach($pengajuan as $row): ?>
                                <tr>
                                    <td><span class="fw-bold text-dark"><?= $row['NO_PERKARA']; ?></span></td>
                                    <td><?= $row['NAMA_KLIEN'] ?? '-'; ?></td>
                                    <td><?= $row['JUDUL_PERKARA'] ?? '-'; ?></td>
                                    <td>
                                        <!-- Badge status: khusus Kuasa Hukum = "Antrean Kuasa Hukum" -->
                                        <span class="badge bg-primary text-white">
                                            <i class="fas fa-gavel me-1"></i> Antrean Kuasa Hukum
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <!-- Tombol aksi: redirect ke halaman validasi detail -->
                                        <a href="<?= base_url('dashboard/keuangan/proses_validasi/'.$row['NO_PERKARA']); ?>"
                                           class="btn btn-sm btn-outline-primary px-3">
                                            <i class="fas fa-edit me-1"></i> Validasi
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- State kosong: UX friendly, kasih icon + text -->
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fs-3 text-success-50 mb-2 d-block"></i>
                                    Bersih! Tidak ada berkas perkara yang menunggu validasi saat ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>