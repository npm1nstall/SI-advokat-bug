<div class="container-fluid px-2">
    <div class="card bg-white border-0 shadow-sm rounded-3 mb-3 m-2">
        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-dark">
                <i class="fas fa-gavel me-2 text-primary"></i>
                <?= $this->session->userdata('klien_logged_in') ? 'Monitoring Jadwal Persidangan Anda' : ($title == 'Data Perkara' ? 'Manajemen Berkas Perkara Aktif' : 'Monitoring Jadwal Persidangan'); ?>
            </h6>
            <div class="d-flex align-items-center gap-2">
                <?php if ($this->session->userdata('klien_logged_in')): ?>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahPerkaraKlien">
                    <i class="fas fa-plus me-1"></i> Ajukan Perkara Baru
                </button>
                <?php endif; ?>
                <span class="badge bg-light text-dark border small fw-medium"><?= count($perkara); ?> Kasus</span>
            </div>
        </div>
    </div>

    <div class="row g-3 m-1">
        <?php if(!empty($perkara)): ?>
            <?php foreach($perkara as $b): 
                // Kita bersihkan ID dari karakter khusus untuk mencegah error JS
                $clean_id = str_replace(['/', '.'], '', $b['NO_PERKARA']);
            ?>
                
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-3 h-100 bg-white" style="border-left: 4px solid #0d6efd !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2 border-bottom pb-2">
                                <div>
                                    <span class="badge bg-dark mb-1" style="font-size: 10px;"><?= $b['NO_PERKARA']; ?></span>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 14px;"><?= $b['JUDUL_PERKARA']; ?></h6>
                                </div>
                                <span class="badge bg-primary pt-1" style="font-size: 10px;"><?= $b['STATUS_PERKARA']; ?></span>
                            </div>

                            <div class="mb-2 bg-light p-2 rounded small" style="font-size: 12px;">
                                <div class="d-flex justify-content-between"><span>Klien:</span><span class="fw-bold"><?= $b['NAMA_KLIEN']; ?></span></div>
                                <div class="d-flex justify-content-between"><span>Kontak:</span><span><?= $b['TELP_KLIEN']; ?></span></div>
                                <div class="border-top mt-1 pt-1">Alamat: <?= $b['ALAMAT_KLIEN'] ?? '-'; ?></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-2">
                                <?php if(!empty($b['BERKAS_PERKARA'])): ?>
                                    <a href="<?= base_url('uploads/perkara/'.$b['BERKAS_PERKARA']); ?>" target="_blank" class="btn btn-sm btn-outline-info">Berkas</a>
                                <?php else: ?>
                                    <span class="text-muted small">Berkas Kosong</span>
                                <?php endif; ?>
                                
                                <?php if (!$this->session->userdata('klien_logged_in')): ?>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditPerkara<?= $clean_id; ?>">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!$this->session->userdata('klien_logged_in')): ?>
                <div class="modal fade" id="modalEditPerkara<?= $clean_id; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form action="<?= base_url('perkara/update/'.$b['NO_PERKARA']); ?>" method="post">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Perkara: <?= $b['NO_PERKARA']; ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Judul Perkara</label>
                                            <input type="text" name="judul_perkara" class="form-control" value="<?= $b['JUDUL_PERKARA'] ?? ''; ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Status Perkara</label>
                                            <select name="STATUS_PERKARA" class="form-select">
                                                <option value="Aktif" <?= $b['STATUS_PERKARA']=='Aktif'?'selected':''; ?>>Aktif</option>
                                                <option value="Selesai" <?= $b['STATUS_PERKARA']=='Selesai'?'selected':''; ?>>Selesai</option>
                                                <option value="Ditunda" <?= $b['STATUS_PERKARA']=='Ditunda'?'selected':''; ?>>Ditunda</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tgl Penugasan Tim</label>
                                            <input type="datetime-local" name="TGL_PENUGASAN_TIM" value="<?= !empty($b['TGL_PENUGASAN_TIM']) ? date('Y-m-d\TH:i', strtotime($b['TGL_PENUGASAN_TIM'])) : ''; ?>" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tgl Sidang</label>
                                            <input type="datetime-local" name="TGL_SIDANG" value="<?= !empty($b['TGL_SIDANG']) ? date('Y-m-d\TH:i', strtotime($b['TGL_SIDANG'])) : ''; ?>" class="form-control">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Agenda Sidang</label>
                                            <input type="text" name="AGENDA_SIDANG" value="<?= $b['AGENDA_SIDANG'] ?? ''; ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="modalTambahPerkaraKlien" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= base_url('perkara/simpan'); ?>" method="post" enctype="multipart/form-data">
        <div class="modal-header">
            <h5 class="modal-title">Ajukan Perkara Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="no_perkara" value="PRK-<?= date('Ymd'); ?>-<?= rand(100,999); ?>">
          <div class="mb-3">
            <label class="form-label">Jenis Perkara / Judul</label>
            <input type="text" name="judul" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Upload Berkas</label>
            <input type="file" name="berkas_perkara" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Kirim</button>
        </div>
      </form>
    </div>
  </div>
</div>