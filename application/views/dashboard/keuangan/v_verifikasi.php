<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Verifikasi & Validasi Berkas Perkara</h3>
            <p class="text-muted small mb-0">Otoritas: <strong><?= $this->session->userdata('jabatan'); ?></strong>.</p>
        </div>
    </div>

    <?php if($this->session->flashdata('pesan')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $this->session->flashdata('pesan'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No Perkara</th>
                            <th>Nama Klien</th>
                            <th>Judul Perkara</th>
                            <th>Berkas</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($berkas)): ?>
                            <?php 
                            $jabatan_sekarang = $this->session->userdata('jabatan');
                            foreach($berkas as $b): 
                                $status_berkas = strtolower($b['STATUS_VERIFIKASI_OPS'] ?? '');
                                $boleh_tampil = false;

                                if ($jabatan_sekarang == 'Admin' && $status_berkas == 'pending admin') {
                                    $boleh_tampil = true;
                                } elseif ($jabatan_sekarang == 'Kuasa Hukum' && ($status_berkas == 'pending kuasa hukum' || $status_berkas == 'validasi selesai')) {
                                    $boleh_tampil = true;
                                } elseif ($jabatan_sekarang == 'Keuangan' && $status_berkas == 'pending keuangan') {
                                    $boleh_tampil = true;
                                }
                            ?>

                                <?php if ($boleh_tampil): ?>
                                <tr>
                                    <td><strong><?= $b['NO_PERKARA']; ?></strong></td>
                                    <td><?= $b['NAMA_KLIEN'] ?? '-'; ?></td>
                                    <td><?= $b['JUDUL_PERKARA'] ?? '-'; ?></td>
                                    <td>
                                        <?php if(!empty($b['BERKAS_PERKARA'])): ?>
                                            <a href="<?= base_url('uploads/perkara/'.$b['BERKAS_PERKARA']); ?>" target="_blank" class="btn btn-sm btn-outline-danger">Lihat</a>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= $b['STATUS_VERIFIKASI_OPS']; ?></span></td>
                                    <td class="text-center">
                                        <?php if($jabatan_sekarang == 'Admin'): ?>
                                            <a href="<?= base_url('keuangan/admin_setujui_berkas?id=' . urlencode($b['NO_TRANSAKSI'])); ?>" class="btn btn-sm btn-success">Teruskan</a>
                                        <?php elseif($jabatan_sekarang == 'Kuasa Hukum'): ?>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalUpload<?= $b['NO_TRANSAKSI']; ?>">Upload TTD</button>
                                        <?php else: ?>
                                            <a href="<?= base_url('dashboard/keuangan/pembayaran'); ?>" class="btn btn-sm btn-success">Proses Tagihan</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-5">Tidak ada data.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($berkas)): ?>
    <?php foreach($berkas as $b): ?>
        <div class="modal fade" id="modalUpload<?= $b['NO_TRANSAKSI']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <form action="<?= base_url('keuangan/proses_upload_ttd'); ?>" method="post" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Upload Berkas Bertanda Tangan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="NO_TRANSAKSI" value="<?= $b['NO_TRANSAKSI']; ?>">
                            <input type="file" name="berkas_ttd" class="form-control" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>