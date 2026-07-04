<div class="container-fluid pt-4 px-4">
    <!-- BARIS KARTU STATISTIK PENANGANAN PERKARA -->
    <div class="row g-3 mb-4 area-no-print">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-white rounded d-flex align-items-center justify-content-between p-3 border shadow-sm">
                <i class="fas fa-folder-plus fa-2x text-info"></i>
                <div class="text-end">
                    <p class="mb-1 text-muted small fw-bold">Kasus Baru</p>
                    <h5 class="mb-0 fw-bold text-dark"><?= $total_baru; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-white rounded d-flex align-items-center justify-content-between p-3 border shadow-sm" style="border-left: 4px solid #0d6efd !important;">
                <i class="fas fa-gavel fa-2x text-primary"></i>
                <div class="text-end">
                    <p class="mb-1 text-muted small fw-bold">Berjalan (Aktif)</p>
                    <h5 class="mb-0 fw-bold text-primary"><?= $total_aktif; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-white rounded d-flex align-items-center justify-content-between p-3 border shadow-sm">
                <i class="fas fa-check-double fa-2x text-success"></i>
                <div class="text-end">
                    <p class="mb-1 text-muted small fw-bold">Kasus Selesai</p>
                    <h5 class="mb-0 fw-bold text-success"><?= $total_selesai; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-white rounded d-flex align-items-center justify-content-between p-3 border shadow-sm">
                <i class="fas fa-hourglass-start fa-2x text-warning"></i>
                <div class="text-end">
                    <p class="mb-1 text-muted small fw-bold">Kasus Ditunda</p>
                    <h5 class="mb-0 fw-bold text-warning"><?= $total_ditunda; ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL UTAMA LAPORAN REKAPITULASI -->
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-white rounded h-100 p-4 shadow-sm border">
                
                <!-- HEADER LAPORAN & TOMBOL CETAK -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-balance-scale text-primary me-2 fs-4"></i>
                        <h5 class="mb-0 fw-bold">Daftar Rekapitulasi Perkara & Jadwal Sidang</h5>
                    </div>
                    <!-- Tombol Kontrol Cetak -->
                    <div class="d-flex gap-2 area-no-print">
                        <button onclick="window.print()" class="btn btn-sm btn-dark px-3">
                            <i class="fas fa-print me-1"></i> Cetak / PDF
                        </button>
                        <button onclick="exportPerkaraExcel()" class="btn btn-sm btn-success px-3">
                            <i class="fas fa-file-excel me-1"></i> Ekspor Excel
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle small" id="tabelLaporanPerkara">
                        <thead class="table-light text-center">
                            <tr>
                                <th>No. Perkara</th>
                                <th>Judul Kasus / Perkara</th>
                                <th>Nama Klien</th>
                                <th>Kuasa Hukum (Advokat)</th>
                                <th>Jadwal Sidang Berikutnya</th>
                                <th>Agenda Sidang</th>
                                <th style="width: 100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($daftar_perkara)): ?>
                                <?php foreach ($daftar_perkara as $p): ?>
                                    <tr>
                                        <td class="fw-bold text-dark text-center"><span class="badge bg-dark"><?= htmlspecialchars($p['NO_PERKARA']); ?></span></td>
                                        <td class="fw-bold"><?= htmlspecialchars($p['JUDUL_PERKARA'] ?? 'Suarat Kuasa Waris'); ?></td>
                                        <td><?= htmlspecialchars($p['NAMA_KLIEN']); ?></td>
                                        <td>
                                            <?= $p['NAMA_STAFF'] ? '<i class="fas fa-user-tie text-muted me-1"></i> ' . htmlspecialchars($p['NAMA_STAFF']) : '<span class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Belum Ditunjuk</span>'; ?>
                                        </td>
                                        <td class="text-center fw-bold text-primary">
                                            <?= !empty($p['TGL_SIDANG']) ? date('d-m-Y H:i', strtotime($p['TGL_SIDANG'])) : 'Belum Dijadwalkan'; ?>
                                        </td>
                                        <td><?= !empty($p['AGENDA_SIDANG']) ? htmlspecialchars($p['AGENDA_SIDANG']) : '<span class="text-muted small">-</span>'; ?></td>
                                        <td class="text-center">
                                            <?php if($p['STATUS_PERKARA'] == 'Baru'): ?>
                                                <span class="badge bg-info text-dark">Baru</span>
                                            <?php elseif($p['STATUS_PERKARA'] == 'Aktif'): ?>
                                                <span class="badge bg-primary">Aktif</span>
                                            <?php elseif($p['STATUS_PERKARA'] == 'Selesai'): ?>
                                                <span class="badge bg-success">Selesai</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Ditunda</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada data rekaman perkara di database.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- STYLE KHUSUS PRINT DOKUMEN KERTAS -->
<style>
@media print {
    .area-no-print, header, nav, .sidebar, .navbar, footer {
        display: none !important;
    }
    .container-fluid, body {
        padding: 0 !important;
        margin: 0 !important;
        background-color: white !important;
    }
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
}
</style>

<!-- MEMANGGIL FILE EXCEL LOKAL YANG KITA BUAT SEBELUMNYA -->
<script src="<?= base_url('assets/js/xlsx.full.min.js'); ?>"></script>
<script>
function exportPerkaraExcel() {
    var table = document.getElementById("tabelLaporanPerkara");
    
    // Konversi langsung tabel data perkara ke file spreadsheet excel
    var wb = XLSX.utils.table_to_book(table, {sheet: "Laporan Perkara & Sidang"});
    
    // Download otomatis file excel
    XLSX.writeFile(wb, "Laporan_Perkara_Dan_Sidang_" + new Date().toISOString().slice(0,10) + ".xlsx");
}
</script>
