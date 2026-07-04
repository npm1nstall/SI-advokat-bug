<div class="container-fluid pt-4 px-4">
    <!-- Notifikasi sukses jika pimpinan mengubah status -->
    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i><?= $this->session->flashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-white rounded h-100 p-4 shadow-sm border">
                
                <div class="d-flex align-items-center mb-4">
                    <i class="fas fa-tasks text-primary me-2 fs-4"></i>
                    <h5 class="mb-0 fw-bold">Manajemen & Persetujuan Cuti Staff</h5>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Nama Karyawan</th>
                                <th>Jabatan</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th>Aksi Persetujuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($daftar_cuti)): ?>
                                <?php foreach ($daftar_cuti as $c): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($c['NAMA_STAFF']); ?></td>
                                        <td class="text-center"><span class="badge bg-secondary"><?= htmlspecialchars($c['JABATAN_STAFF']); ?></span></td>
                                        <td class="text-center"><?= date('d M Y', strtotime($c['TGL_MULAI_CUTI'])); ?></td>
                                        <td class="text-center"><?= date('d M Y', strtotime($c['TGL_SELESAI_CUTI'])); ?></td>
                                        <td><?= htmlspecialchars($c['ALASAN_CUTI']); ?></td>
                                        <td class="text-center">
                                            <?php if ($c['STATUS_CUTI'] == 'Pending'): ?>
                                                <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-clock me-1"></i> Pending</span>
                                            <?php elseif ($c['STATUS_CUTI'] == 'Disetujui'): ?>
                                                <span class="badge bg-success px-2 py-1"><i class="fas fa-check me-1"></i> Disetujui</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger px-2 py-1"><i class="fas fa-times me-1"></i> Ditolak</span>
                                            <?php endif; ?>
                                        </td>
                                       <td class="text-center">
										<?php if ($c['STATUS_CUTI'] == 'Pending'): ?>
											<form action="<?= base_url('dashboard/aksi_cuti'); ?>" method="post" class="d-flex gap-1 justify-content-center">
												<input type="hidden" name="nama_staf" value="<?= $c['NAMA_STAFF']; ?>">
												
												<button type="submit" name="status" value="setuju" class="btn btn-sm btn-success">
													<i class="fas fa-check me-1"></i> Setuju
												</button>
												<button type="submit" name="status" value="tolak" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan cuti ini?')">
													<i class="fas fa-times me-1"></i> Tolak
												</button>
											</form>
										<?php else: ?>
											<span class="text-muted small">Selesai diproses</span>
										<?php endif; ?>
									</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada pengajuan cuti dari staf manapun.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
