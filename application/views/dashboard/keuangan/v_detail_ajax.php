<div class="row g-3">
    <div class="col-md-6">
        <label class="text-muted small">No. Transaksi</label>
        <div class="fw-bold fs-6"><?= $item['NO_TRANSAKSI']; ?></div>
    </div>
    <div class="col-md-6">
        <label class="text-muted small">No. Perkara</label>
        <div class="fw-bold fs-6"><?= $item['NO_PERKARA']; ?></div>
    </div>

    <hr class="my-2">

    <?php if(!empty($item['KEPERLUAN_DANA_OPS'])): ?>
    <div class="col-12">
        <label class="text-muted small">Keperluan / Keterangan</label>
        <div class="bg-light p-2 rounded mb-3"><?= $item['KEPERLUAN_DANA_OPS']; ?></div>
    </div>
    <?php endif; ?>
    
    <div class="col-md-6">
        <label class="text-muted small">Jumlah Pengajuan</label>
        <div class="fw-bold text-success fs-5">
            <?= ($item['JMLH_PENGAJUAN_OPS'] > 0) ? 'Rp ' . number_format($item['JMLH_PENGAJUAN_OPS'], 0, ',', '.') : '-'; ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <label class="text-muted small">Bukti Bayar Klien</label>
        <div>
            <?php if(!empty($item['BUKTI_BAYAR_KLIEN'])): ?>
                <a href="<?= base_url('uploads/pembayaran/'.$item['BUKTI_BAYAR_KLIEN']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-file-download"></i> Lihat Bukti
                </a>
            <?php else: ?>
                <span class="text-danger small">Belum ada bukti</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-12 mt-3">
        <label class="text-muted small">Status Verifikasi</label>
        <div>
            <span class="badge <?= ($item['STATUS_VERIFIKASI_OPS'] == 'Pending Admin') ? 'bg-warning text-dark' : 'bg-success' ?>">
                <?= $item['STATUS_VERIFIKASI_OPS']; ?>
            </span>
        </div>
    </div>
</div>