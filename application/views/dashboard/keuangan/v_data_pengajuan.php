<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h3 class="fw-bold mb-0">Data Pengajuan Biaya</h3>
            <p class="text-muted small">Rekapitulasi pengajuan biaya operasional dari Kuasa Hukum</p>
        </div>
        <a href="<?= base_url('dashboard/keuangan'); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
							<th>No telp staff</th>
                            <th class="ps-4">No Transaksi</th>
                            <th>No Perkara</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($pengajuan)): ?>
                            <?php foreach($pengajuan as $p): 
                                // Logika warna badge
                                $status = $p['STATUS_VERIFIKASI_OPS'];
                                $badge = ($status == 'Pending Pimpinan') ? 'bg-warning text-dark' : 'bg-success';
                            ?>
                           <tr>
								<td>
									<strong><?= $p['NAMA_STAFF']; ?></strong><br>
									<?php if ($p['TELP_STAFF'] != 'SYSTEM_KLIEN'): ?>
										<small class="text-muted"><i class="fas fa-phone-alt"></i> <?= $p['TELP_STAFF'] ?? '-'; ?></small>
									<?php endif; ?>
								</td>
								<td class="ps-4">
									<div class="d-flex flex-column">
										<span class="fw-bold text-dark"><?= $p['NO_TRANSAKSI']; ?></span>
										<small class="text-muted">Nota: #<?= $p['BUKTI_NOTA_KAS_KELUAR'] ?? '-'; ?></small>
								</td>
								<td><?= $p['NO_PERKARA']; ?></td>
								<td>
									<span class="badge bg-light text-dark border shadow-sm">
										Rp <?= number_format($p['JMLH_PENGAJUAN_OPS'], 0, ',', '.'); ?>
									</span>
								</td>
								<td>
									<span class="badge rounded-pill <?= $badge ?> px-3">
										<?= $status; ?>
									</span>
								</td>
								<td class="text-center">
									<button type="button" class="btn btn-sm btn-light border" onclick="loadDetail('<?= $p['NO_TRANSAKSI']; ?>')">
										Detail
									</button>
								</td>
							</tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fs-2 d-block mb-2"></i>
                                    Belum ada pengajuan biaya operasional saat ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetailLabel">Detail Pengajuan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="isiDetail">
            <p class="text-center">Memuat data...</p>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
function loadDetail(no_transaksi) {
    // 1. Mencegah reload halaman
    event.preventDefault(); 
    
    document.getElementById('isiDetail').innerHTML = 'Memuat data...';
    
    // 2. Pastikan URL ke Controller Keuangan benar
    // Coba tambahkan console.log untuk memastikan URL-nya benar
    var url = '<?= base_url('keuangan/ajax_detail/'); ?>' + no_transaksi;
    console.log("Mengakses: " + url);
    
    fetch(url)
        .then(response => response.text())
        .then(data => {
            document.getElementById('isiDetail').innerHTML = data;
            var myModal = new bootstrap.Modal(document.getElementById('modalDetail'));
            myModal.show();
        })
        .catch(err => {
            console.error(err);
        });
}
</script>