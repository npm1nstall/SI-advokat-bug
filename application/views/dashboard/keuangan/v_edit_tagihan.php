<div class="container-fluid px-4 mt-4">
    <h3 class="fw-bold mb-3">Edit Tagihan Invoice</h3>
    
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <!-- Dioptimalkan dengan urlencode demi keamanan parameter URL -->
            <form action="<?= base_url('dashboard/keuangan/proses_edit_tagihan/'.urlencode($tagihan['NO_TRANSAKSI'])); ?>" method="POST">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-muted">No. Transaksi (Terkunci)</label>
                        <input type="text" class="form-control bg-light text-dark fw-bold" value="<?= $tagihan['NO_TRANSAKSI']; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-muted">No. Perkara (Terkunci)</label>
                        <input type="text" class="form-control bg-light text-dark fw-bold" value="<?= $tagihan['NO_PERKARA']; ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">No. Invoice Baru</label>
                        <input type="text" name="no_invoice" class="form-control" value="<?= $tagihan['NO_INVOICE']; ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Total Tagihan Baru (Rp)</label>
                        <input type="number" name="ttl_tagihan" class="form-control" value="<?= $tagihan['TTL_TAGIHAN_KLIEN']; ?>" required>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="<?= base_url('dashboard/keuangan/pembayaran'); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-warning text-dark fw-medium">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
