<div class="container-fluid pt-4 px-4">
    <!-- BARIS KARTU STATISTIK KEUANGAN EKSEKUTIF -->
    <div class="row g-3 mb-4 area-no-print">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-white rounded d-flex align-items-center justify-content-between p-3 border shadow-sm" style="border-left: 4px solid #198754 !important;">
                <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                <div class="text-end">
                    <p class="mb-1 text-muted small fw-bold">Dana Ops Cair</p>
                    <h5 class="mb-0 fw-bold text-success">Rp <?= number_format($total_ops_cair, 0, ',', '.'); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-white rounded d-flex align-items-center justify-content-between p-3 border shadow-sm">
                <i class="fas fa-hand-holding-usd fa-2x text-warning"></i>
                <div class="text-end">
                    <p class="mb-1 text-muted small fw-bold">Dana Ops Pending</p>
                    <h5 class="mb-0 fw-bold text-warning">Rp <?= number_format($total_ops_pending, 0, ',', '.'); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-white rounded d-flex align-items-center justify-content-between p-3 border shadow-sm">
                <i class="fas fa-check-circle fa-2x text-primary"></i>
                <div class="text-end">
                    <p class="mb-1 text-muted small fw-bold">Kasus Klien Lunas</p>
                    <h5 class="mb-0 fw-bold text-primary"><?= $perkara_lunas; ?> Kasus</h5>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-white rounded d-flex align-items-center justify-content-between p-3 border shadow-sm">
                <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                <div class="text-end">
                    <p class="mb-1 text-muted small fw-bold">Klien Belum Bayar</p>
                    <h5 class="mb-0 fw-bold text-danger"><?= $perkara_belum; ?> Kasus</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL REKAPITULASI ARUS KEUANGAN PERKARA -->
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-white rounded h-100 p-4 shadow-sm border">
                
                <!-- HEADER & KONTROL CETAK -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-invoice-dollar text-primary me-2 fs-4"></i>
                        <h5 class="mb-0 fw-bold">Jurnal Rekapitulasi Keuangan Perkara Kantor Hukum</h5>
                    </div>
                    <div class="d-flex gap-2 area-no-print">
                        <button onclick="window.print()" class="btn btn-sm btn-dark px-3">
                            <i class="fas fa-print me-1"></i> Cetak Jurnal / PDF
                        </button>
                        <button onclick="exportKeuanganExcel()" class="btn btn-sm btn-success px-3">
                            <i class="fas fa-file-excel me-1"></i> Ekspor Excel
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle small text-center" id="tabelLaporanKeuangan">
                        <thead class="table-light">
                            <tr>
                                <th>No. Transaksi</th>
                                <th>No. Perkara</th>
                                <th>Judul Kasus / Perkara</th>
                                <th>Nama Klien</th>
                                <th>Pengajuan Ops Dana</th>
                                <th>Status Ops Dana</th>
                                <th>Status Pembayaran Klien</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($daftar_keuangan)): ?>
                                <?php foreach ($daftar_keuangan as $k): ?>
                                    <tr>
                                        <td class="fw-bold text-muted"><?= htmlspecialchars($k['NO_TRANSAKSI']); ?></td>
                                        <td class="fw-bold"><span class="badge bg-dark"><?= htmlspecialchars($k['NO_PERKARA']); ?></span></td>
                                        <td class="text-start fw-bold text-dark"><?= htmlspecialchars($k['JUDUL_PERKARA'] ?? 'Suarat Kuasa Waris'); ?></td>
                                        <td class="text-start"><?= htmlspecialchars($k['NAMA_KLIEN'] ?? '-'); ?></td>
                                        
                                        <!-- Nominal Pengajuan Dana -->
                                        <td class="text-end text-dark fw-bold">
                                            <?= $k['JMLH_PENGAJUAN_OPS'] > 0 ? 'Rp ' . number_format($k['JMLH_PENGAJUAN_OPS'], 0, ',', '.') : '<span class="text-muted fw-normal">-</span>'; ?>
                                        </td>
                                        
                                        <!-- Status Pengajuan Ops Workflow -->
                                        <td>
                                            <?php if(empty($k['JMLH_PENGAJUAN_OPS']) || $k['JMLH_PENGAJUAN_OPS'] == 0): ?>
                                                <span class="text-muted small">Belum Ada Pengajuan</span>
                                            <?php elseif($k['STATUS_VERIFIKASI_OPS'] == 'Pending Pimpinan'): ?>
                                                <span class="badge bg-warning text-dark">Pending Pimpinan</span>
                                            <?php elseif($k['STATUS_VERIFIKASI_OPS'] == 'Pending keuangan'): ?>
                                                <span class="badge bg-info text-dark">Pending Keuangan</span>
                                            <?php elseif($k['STATUS_VERIFIKASI_OPS'] == 'Ditolak'): ?>
                                                <span class="badge bg-danger">Ditolak</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Disetujui (Cair)</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Status Invoice/Tagihan Pembayaran Klien -->
                                        <td>
                                            <?php if($k['STATUS_BAYAR_KLIEN'] == 'Lunas'): ?>
                                                <span class="badge bg-success px-3 py-1"><i class="fas fa-check-circle me-1"></i> Lunas</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger px-2 py-1"><i class="fas fa-times-circle me-1"></i> Belum Bayar</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada rekam data riwayat transaksi keuangan pada sistem.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- STYLING CSS PROTEKSI PRINT LAYOUT KERTAS -->
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
    /* Pastikan warna teks status lunas/belum bayar tetap kontras saat diprint */
    .badge {
        border: 1px solid #000 !important;
        color: #000 !important;
        background: transparent !important;
    }
}
</style>

<!-- LOAD LIBRARY EXCEL LOKAL YANG KITA SIMPAN DI ASSETS JAVASCRIPT KEMARIN -->
<script src="<?= base_url('assets/js/xlsx.full.min.js'); ?>"></script>
<script>
function exportKeuanganExcel() {
    var table = document.getElementById("tabelLaporanKeuangan");
    
    // Konversi objek tabel HTML kas jurnal langsung menjadi file workbook excel spreadsheet
    var wb = XLSX.utils.table_to_book(table, {sheet: "Jurnal Kas & Keuangan"});
    
    // Eksekusi download otomatis file berkas xlsx
    XLSX.writeFile(wb, "Laporan_Arus_Keuangan_Perkara_" + new Date().toISOString().slice(0,10) + ".xlsx");
}
</script>
