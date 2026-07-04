<div class="container-fluid pt-4 px-4">
    <!-- Menampilkan Flash Notifikasi Jika Sukses -->
    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i><?= $this->session->flashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-white rounded h-100 p-4 shadow-sm border">
                
                <!-- Header halaman dan Tombol Pemicu Modal -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-umbrella-beach text-primary me-2 fs-4"></i>
                        <h5 class="mb-0 fw-bold">Status & Riwayat Cuti Anda</h5>
                    </div>
                    <!-- Tombol untuk memunculkan Modal Pop-up -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCuti">
                        <i class="fas fa-plus me-2"></i>Ajukan Cuti Baru
                    </button>
                </div>
                
                <!-- Tabel Informasi Status Cuti Aktif -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Alasan Cuti</th>
                                <th>Status Persetujuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($staff['TGL_MULAI_CUTI'])): ?>
                                <tr>
                                    <td class="text-center"><?= date('d M Y', strtotime($staff['TGL_MULAI_CUTI'])); ?></td>
                                    <td class="text-center"><?= date('d M Y', strtotime($staff['TGL_SELESAI_CUTI'])); ?></td>
                                    <td><?= htmlspecialchars($staff['ALASAN_CUTI']); ?></td>
                                    <td class="text-center">
                                        <?php if ($staff['STATUS_CUTI'] == 'Pending'): ?>
											<span class="badge bg-warning text-dark px-3 py-2">
												<i class="fas fa-clock me-1"></i> Menunggu Pimpinan
											</span>
										<?php elseif ($staff['STATUS_CUTI'] == 'Disetujui'): ?>
											<span class="badge bg-success px-3 py-2">
												<i class="fas fa-check me-1"></i> Disetujui Pimpinan
											</span>
										<?php else: ?>
											<span class="badge bg-danger px-3 py-2">
												<i class="fas fa-times me-1"></i> Ditolak Pimpinan
											</span>
										<?php endif; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Anda belum pernah mengajukan cuti atau riwayat kosong.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ==================== BOOTSTRAP MODAL FORM (POP-UP) ==================== -->
<div class="modal fade" id="modalCuti" tabindex="-1" aria-labelledby="modalCutiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalCutiLabel"><i class="fas fa-paper-plane me-2"></i>Form Pengajuan Cuti</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('dashboard/proses_cuti'); ?>" method="post">
                <div class="modal-body p-4">
					<!-- Mengirimkan data Nama Staf yang sedang aktif login -->
					<input type="hidden" name="nama_staf" value="<?= $staff['NAMA_STAFF'] ?? $this->session->userdata('nama_staf'); ?>">
             
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Tanggal Mulai Cuti</label>
                        <input type="date" name="tgl_mulai" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Tanggal Selesai Cuti</label>
                        <input type="date" name="tgl_selesai" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Alasan Cuti</label>
                        <textarea name="alasan" class="form-control" rows="4" placeholder="Tuliskan alasan cuti secara detail..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
