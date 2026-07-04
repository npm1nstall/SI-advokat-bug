<div class="container-fluid px-4 mt-4">
    <h3 class="fw-bold mb-0">Verifikasi Berkas Klien</h3>
    <p class="text-muted small">Kelola dan verifikasi berkas yang diunggah oleh klien</p>

    <div class="card shadow-sm border-0 mt-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No Transaksi</th>
                            <th>No Perkara</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($berkas as $b): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-dark"><?= $b['NO_TRANSAKSI']; ?></td>
                            <td><?= $b['NO_PERKARA']; ?></td>
                            <td>
                                <span class="badge <?= ($b['STATUS_VERIFIKASI_OPS'] == 'Pending Admin') ? 'bg-warning text-dark' : 'bg-success' ?> rounded-pill">
                                    <?= $b['STATUS_VERIFIKASI_OPS']; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light border" onclick="loadDetail('<?= $b['NO_TRANSAKSI']; ?>')">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                                <?php if($b['STATUS_VERIFIKASI_OPS'] == 'Pending Admin'): ?>
                                    <a href="<?= base_url('keuangan/admin_setujui_berkas?id='.urlencode($b['NO_TRANSAKSI'])); ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-check"></i> Verifikasi
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Berkas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="isiDetail">Memuat data...</div>
      </div>
    </div>
  </div>
</div>
<script>
function loadDetail(no_transaksi) {
    // 1. Tampilkan "Memuat..."
    document.getElementById('isiDetail').innerHTML = 'Memuat data...';
    
    // 2. Inisialisasi modal
    var myModal = new bootstrap.Modal(document.getElementById('modalDetail'));
    myModal.show();
    
    // 3. Fetch data dari controller
    var url = '<?= base_url('keuangan/ajax_detail/'); ?>' + no_transaksi;
    
    fetch(url)
        .then(response => response.text())
        .then(data => {
            document.getElementById('isiDetail').innerHTML = data;
        })
        .catch(err => {
            document.getElementById('isiDetail').innerHTML = 'Gagal memuat data.';
            console.error(err);
        });
}
</script>