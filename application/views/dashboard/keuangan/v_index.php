<?php 
/**
 * ==============================================
 * VIEW: Dashboard Keuangan
 * File: keuangan/v_index.php
 * Fungsi: KPI Keuangan + Tabel pengajuan OPS internal
 * ==============================================
 */
?>

<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h3 class="fw-bold mb-0">Dashboard Keuangan</h3>
        <span class="text-muted small">
            <?= date('d F Y'); ?>
        </span>
    </div>
	

    <h5 class="fw-semibold text-secondary mb-3"><i class="fas fa-file-invoice-dollar me-2"></i>Status Berkas & Invoice Klien</h5>
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">Siap Buat Invoice</div>
                        <h3 class="fw-bold mb-0 text-warning">
                            <?= $this->db->where('STATUS_VERIFIKASI_OPS', 'Pending Keuangan')->count_all_results('KEUANGAN'); ?>
                        </h3>
                    </div>
                    <div class="rounded-circle border p-3 bg-light">
                       <i class="fas fa-file-invoice text-warning fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">Invoice Terbit</div>
                        <h3 class="fw-bold mb-0 text-success">
                            <?= $this->db->where('STATUS_VERIFIKASI_OPS', 'Selesai')->count_all_results('KEUANGAN'); ?>
                        </h3>
                    </div>
                    <div class="rounded-circle border p-3 bg-light">
                       <i class="fas fa-check-double text-success fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">Ops. Pending Pimpinan</div>
                        <h3 class="fw-bold mb-0 text-danger"><?= $jml_pending; ?></h3>
                    </div>
                    <div class="rounded-circle border p-3 bg-light">
                       <i class="fas fa-clock text-danger fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">Ops. Dana Disetujui</div>
                        <h3 class="fw-bold mb-0 text-primary"><?= $jml_disetujui; ?></h3>
                    </div>
                    <div class="rounded-circle border p-3 bg-light">
                        <i class="fas fa-wallet text-primary fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL INPUT BUKTI NOTA KAS KELUAR -->
<div class="card shadow border-0 mt-4">
    <div class="card-header bg-dark text-white py-2">
        <h6 class="m-0"><i class="fas fa-money-check-alt me-2"></i> Pencairan Dana Operasional (Proses Kasir / Keuangan)</h6>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle small text-center bg-white">
                <thead class="table-light">
                    <tr>
                        <th>No Transaksi</th>
                        <th>No Perkara</th>
                        <th>Judul Kasus</th>
                        <th>Nominal Dana</th>
                        <th>Ketik No. Bukti Nota Kas Keluar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($pengajuan)): ?>
                       <?php foreach($pengajuan as $k): ?>
							<form action="<?= base_url('keuangan/simpan_nota_keluar'); ?>" method="POST">
								<!-- Gunakan bracket [] karena formatnya Array (result_array()) -->
								<input type="hidden" name="no_transaksi" value="<?= $k['NO_TRANSAKSI']; ?>">
								<tr>
									<td><span class="fw-bold"><?= $k['NO_TRANSAKSI']; ?></span></td>
									<td><?= $k['NO_PERKARA']; ?></td>
									<td class="text-start"><?= $k['JUDUL_perkara'] ?? '-'; ?></td>
									<td class="text-success fw-bold">Rp <?= number_format($k['JMLH_PENGAJUAN_OPS'], 0, ',', '.'); ?></td>
									<td>
										<input type="text" name="bukti_nota" class="form-control form-control-sm text-center font-weight-bold" placeholder="Contoh: BKK-001" required>
									</td>
									<td>
										<button type="submit" class="btn btn-sm btn-primary py-1 px-3">
											<i class="fas fa-check-circle me-1"></i> Selesai
										</button>
									</td>
								</tr>
							</form>
						<?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                <i class="fas fa-check-double text-success me-1"></i> Tidak ada antrean pencairan dana operasional aktif saat ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>