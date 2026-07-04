<div class="container-fluid pt-4 px-4">
    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i><?= $this->session->flashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i><?= $this->session->flashdata('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-white rounded h-100 p-4 shadow-sm border">
                
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-folder-open text-primary me-2 fs-4"></i>
                        <h5 class="mb-0 fw-bold">Manajemen Surat Masuk, Keluar & Disposisi</h5>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUploadSurat">
                        <i class="fas fa-upload me-2"></i>Upload Surat Baru
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Jenis Surat</th>
                                <th>No. Surat</th>
                                <th>Perihal</th>
                                <th>No. Perkara</th>
                                <th>Staf Ditunjuk (Telp)</th>
                                <th>Tanggal Surat</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($daftar_surat)): ?>
                                <?php foreach ($daftar_surat as $s): ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php if($s['JNS_SURAT'] == 'Surat Masuk'): ?>
                                                <span class="badge bg-info text-dark">Surat Masuk</span>
                                            <?php elseif($s['JNS_SURAT'] == 'Surat Keluar'): ?>
                                                <span class="badge bg-secondary">Surat Keluar</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Disposisi Tim</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($s['NO_SURAT']); ?></td>
                                        <td><?= htmlspecialchars($s['PERIHAL']); ?></td>
                                        <td class="text-center"><?= $s['NO_PERKARA'] ? htmlspecialchars($s['NO_PERKARA']) : '-'; ?></td>
                                        <td class="text-center fw-bold text-success">
                                            <?= $s['TELP_STAFF'] ? htmlspecialchars($s['TELP_STAFF']) : '-'; ?>
                                        </td>
                                        <td class="text-center"><?= date('d M Y', strtotime($s['TGL_SURAT'])); ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('assets/uploads/surat/' . $s['ARSIP_DIGITAL']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="fas fa-download me-1"></i> Lihat/Unduh
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada dokumen surat di database lokal.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL UPLOAD SURAT ==================== -->
<div class="modal fade" id="modalUploadSurat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-upload me-2"></i>Form Dokumen Surat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?= form_open_multipart('dashboard/upload_surat'); ?>
                <div class="modal-body p-4">
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Jenis Surat</label>
                        <select name="jns_surat" id="jns_surat" class="form-select" required onchange="toggleDisposisi()">
                            <option value="Surat Masuk">Surat Masuk</option>
                            <option value="Surat Keluar">Surat Keluar</option>
                            <option value="Disposisi Tim">Disposisi Tim (Pendelegasian / Pengalihan Tugas)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nomor Surat</label>
                        <input type="text" name="no_surat" class="form-control" placeholder="Contoh: 024/SK/ADV/VI/2026" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Hubungkan ke Nomor Perkara (Opsional)</label>
                        <select name="no_perkara" class="form-select">
                            <option value="">-- Pilih Nomor Perkara Dari Database --</option>
                            <?php foreach($daftar_perkara as $p): ?>
                                <option value="<?= $p['NO_PERKARA']; ?>">
                                    <?= $p['NO_PERKARA']; ?> - <?= $p['JUDUL_PERKARA']; ?> (Klien: <?= $p['NAMA_KLIEN']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Perihal / Keterangan</label>
                        <input type="text" name="perihal" class="form-control" placeholder="Contoh: Delegasi Sidang Perkara Sugeng" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Tanggal Surat Dibuat</label>
                        <input type="date" name="tgl_surat" class="form-control" required>
                    </div>

                    <!-- Pilihan ini muncul otomatis jika memilih 'Disposisi Tim' -->
                    <div class="mb-3" id="box_disposisi" style="display: none;">
                       <label class="form-label text-muted small fw-bold text-primary">Tunjuk Kuasa Hukum Terdelegasi / Penerima Tugas</label>
							<select name="telp_staff" class="form-select">
								<option value="">-- Pilih Kuasa Hukum --</option>
                            <?php foreach($karyawan as $k): ?>
                                <option value="<?= $k['TELP_STAFF']; ?>"><?= $k['NAMA_STAFF']; ?> (<?= $k['TELP_STAFF']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">File Dokumen (PDF/DOCX/Gambar Max 5MB)</label>
                        <input type="file" name="file_surat" class="form-control" required>
                    </div>

                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Mulai Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDisposisi() {
    var jenis = document.getElementById("jns_surat").value;
    var box = document.getElementById("box_disposisi");
    if (jenis === "Disposisi Tim") {
        box.style.display = "block";
    } else {
        box.style.display = "none";
    }
}
</script>
