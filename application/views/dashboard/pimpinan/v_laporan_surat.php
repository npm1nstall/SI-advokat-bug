<div class="container-fluid pt-4 px-4">
    <!-- BARIS KARTU RINGKASAN DATA (STATISTIK) -->
    <div class="row g-3 mb-4 area-no-print">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-white rounded d-flex align-items-center justify-content-between p-3 border shadow-sm">
                <i class="fas fa-inbox fa-2x text-info"></i>
                <div class="text-end">
                    <p class="mb-1 text-muted small fw-bold">Surat Masuk</p>
                    <h5 class="mb-0 fw-bold text-dark"><?= $total_masuk; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-white rounded d-flex align-items-center justify-content-between p-3 border shadow-sm">
                <i class="fas fa-paper-plane fa-2x text-secondary"></i>
                <div class="text-end">
                    <p class="mb-1 text-muted small fw-bold">Surat Keluar</p>
                    <h5 class="mb-0 fw-bold text-dark"><?= $total_keluar; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-white rounded d-flex align-items-center justify-content-between p-3 border shadow-sm">
                <i class="fas fa-network-wired fa-2x text-primary"></i>
                <div class="text-end">
                    <p class="mb-1 text-muted small fw-bold">Disposisi Tim</p>
                    <h5 class="mb-0 fw-bold text-dark"><?= $total_disposisi; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-white rounded d-flex align-items-center justify-content-between p-3 border shadow-sm" style="background-color: #f8f9fa;">
                <i class="fas fa-mail-bulk fa-2x text-success"></i>
                <div class="text-end">
                    <p class="mb-1 text-muted small fw-bold">Total Arsip</p>
                    <h5 class="mb-0 fw-bold text-success"><?= $total_semua; ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL UTAMA LAPORAN -->
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-white rounded h-100 p-4 shadow-sm border">
                
                <!-- HEADER LAPORAN & TOMBOL CETAK -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-print text-primary me-2 fs-4"></i>
                        <h5 class="mb-0 fw-bold">Daftar Rekapitulasi Dokumen Surat Kantor</h5>
                    </div>
                    <!-- Area Tombol Aksi (Akan disembunyikan saat dicetak) -->
                    <div class="d-flex gap-2 area-no-print">
                        <button onclick="window.print()" class="btn btn-sm btn-dark px-3">
                            <i class="fas fa-print me-1"></i> Cetak / PDF
                        </button>
                        <button onclick="exportToExcel()" class="btn btn-sm btn-success px-3">
                            <i class="fas fa-file-excel me-1"></i> Ekspor Excel
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle small" id="tabelLaporanSurat">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 120px;">Kategori</th>
                                <th>No. Surat</th>
                                <th>Perihal / Deskripsi</th>
                                <th>No. Perkara</th>
                                <th>Delegasi Staff</th>
                                <th style="width: 100px;">Tgl Surat</th>
                                <th style="width: 80px;" class="area-no-print">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($daftar_surat)): ?>
                                <?php foreach ($daftar_surat as $s): ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php if($s['JNS_SURAT'] == 'Surat Masuk'): ?>
                                                <span class="badge bg-info text-dark px-2 py-1">Surat Masuk</span>
                                            <?php elseif($s['JNS_SURAT'] == 'Surat Keluar'): ?>
                                                <span class="badge bg-secondary px-2 py-1">Surat Keluar</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary px-2 py-1">Disposisi Tim</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($s['NO_SURAT']); ?></td>
                                        <td><?= htmlspecialchars($s['PERIHAL']); ?></td>
                                        <td class="text-center text-danger fw-bold"><?= $s['NO_PERKARA'] ? htmlspecialchars($s['NO_PERKARA']) : '-'; ?></td>
                                        <td class="text-start">
                                            <?= $s['NAMA_STAFF'] ? htmlspecialchars($s['NAMA_STAFF']) : '-'; ?>
                                        </td>
                                        <td class="text-center"><?= date('d-m-Y', strtotime($s['TGL_SURAT'])); ?></td>
                                        <td class="text-center area-no-print">
                                            <a href="<?= base_url('assets/uploads/surat/' . $s['ARSIP_DIGITAL']); ?>" class="btn btn-xs btn-outline-primary py-0 px-2" target="_blank" style="font-size: 11px;">
                                                <i class="fas fa-eye"></i> Buka
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada arsip surat berkas yang terekam di sistem.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- STYLE KHUSUS UNTUK MENYEMBUNYIKAN MENU SIDEBAR SAAT PRINT -->
<style>
@media print {
    /* Sembunyikan sidebar, navbar, kartu statistik, tombol aksi, dan kolom aksi tabel */
    .area-no-print, 
    header, 
    nav, 
    .sidebar, 
    .navbar, 
    footer, 
    .btn {
        display: none !important;
    }
    /* Lebarkan container utama agar penuh kertas */
    .container-fluid, .content, body {
        padding: 0 !important;
        margin: 0 !important;
        background-color: white !important;
    }
    /* Pastikan tabel rapi di kertas */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
}
</style>

<!-- SCRIPT JAVASCRIPT UNTUK EKSPOR EXCEL TANPA REPOT -->
<script src="<?= base_url('assets/js/xlsx.full.min.js'); ?>"></script>
<script>
function exportToExcel() {
    // 1. Ambil elemen tabel laporan surat
    var table = document.getElementById("tabelLaporanSurat");
    
    // 2. Duplikasi tabel sementara agar kita bisa hapus kolom "Aksi" sebelum di-export
    var cloneTable = table.cloneNode(true);
    var rows = cloneTable.querySelectorAll("tr");
    
    // Hapus kolom terakhir (Aksi) di setiap baris agar file Excel bersih
    rows.forEach(function(row) {
        if(row.lastElementChild) {
            row.removeChild(row.lastElementChild);
        }
    });

    // 3. Konversi data tabel kloningan menjadi format Excel spreadsheet
    var wb = XLSX.utils.table_to_book(cloneTable, {sheet: "Laporan Surat Kantor"});
    
    // 4. Download file Excel otomatis ke komputer Pimpinan
    XLSX.writeFile(wb, "Laporan_Arsip_Surat_Internal_" + new Date().toISOString().slice(0,10) + ".xlsx");
}
</script>
