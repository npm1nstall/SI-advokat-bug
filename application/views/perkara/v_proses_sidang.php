<div class="card shadow border-0 m-2">
    <div class="card-header bg-primary text-white py-2">
        <h6 class="m-0"><i class="fas fa-gavel me-2"></i> Update Perkembangan Perkara</h6>
    </div>
    <div class="card-body p-3">
        
        <!-- HATI-HATI: Notifikasi wajib di atas biar admin tau sukses/gagal -->
        <?php if($this->session->flashdata('pesan')): ?>
            <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3 small" role="alert">
                <i class="fas fa-check-circle me-1"></i><?= $this->session->flashdata('pesan'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Informasi Singkat Ringkas -->
        <div class="alert alert-info py-2 px-3 mb-3 small d-flex justify-content-between align-items-center">
            <span><strong>No. Internal:</strong> <?= $perkara['NO_PERKARA']; ?></span>
            <span><strong>Kasus:</strong> <?= $perkara['JUDUL_PERKARA']; ?></span>
            <span><strong>Klien:</strong> <?= $perkara['NAMA_KLIEN']; ?></span>
        </div>

        <!-- HATI-HATI: WAJIB enctype="multipart/form-data" Kalo mau upload berkas. Kalo ga ada = $_FILES kosong -->
       <form action="<?= base_url('perkara/simpan_proses_sidang'); ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="no_perkara" value="<?= $perkara['NO_PERKARA']; ?>">
			 <!-- TAMBAHKAN INPUT HIDDEN BARU INI: Menjaga judul perkara agar tidak hilang saat di-save -->
				<input type="hidden" name="judul_perkara" value="<?= $perkara['JUDUL_PERKARA']; ?>">
						<div class="row">
                <!-- KOLOM KIRI -->
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Tanggal Penugasan Tim</label>
                        <!-- HATI-HATI: Format datetime-local harus Y-m-d\TH:i. Kalo salah = input kosong -->
                        <input type="datetime-local" name="tgl_penugasan" class="form-control form-control-sm" value="<?= !empty($perkara['TGL_PENUGASAN_TIM']) ? date('Y-m-d\TH:i', strtotime($perkara['TGL_PENUGASAN_TIM'])) : ''; ?>">
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Tanggal Sidang</label>
                        <input type="datetime-local" name="tgl_sidang" class="form-control form-control-sm" value="<?= !empty($perkara['TGL_SIDANG']) ? date('Y-m-d\TH:i', strtotime($perkara['TGL_SIDANG'])) : ''; ?>">
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Agenda Sidang</label>
                        <input type="text" name="agenda_sidang" class="form-control form-control-sm" placeholder="Contoh: Sidang Pertama / Pembacaan Gugatan" value="<?= $perkara['AGENDA_SIDANG'] ?? ''; ?>">
                    </div>
                </div>

                <!-- KOLOM KAN -->
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Catatan Disposisi & Nomor Perkara Sah PN</label>
                        <textarea name="catatan_disposisi" class="form-control form-control-sm" rows="3" placeholder="Masukkan instruksi tim dan Nomor Perkara resmi dari PN..."><?= $perkara['CATATAN_DISPOSISI'] ?? ''; ?></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Hasil Sidang</label>
                        <textarea name="hasil_sidang" class="form-control form-control-sm" rows="2" placeholder="Masukkan hasil putusan jika sudah terlaksana..."><?= $perkara['HASIL_SIDANG'] ?? ''; ?></textarea>
                    </div>
                    
                    <!-- HATI-HATI: FIELD INI WAJIB ADA KALO MAU UPDATE BERKAS SIDANG -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Upload Berkas Sidang Baru</label>
                        <input type="file" name="berkas_baru" class="form-control form-control-sm">
                        <small class="text-muted">Kosongkan jika tidak ganti berkas. Format: PDF/JPG/PNG max 5MB</small>
                        
                        <!-- HATI-HATI: Tampilin berkas lama biar admin tau ini berkas apa -->
                        <?php if(!empty($perkara['BERKAS_PERKARA'])): ?>
                            <div class="mt-1">
                                <small>Berkas lama: 
                                    <a href="<?= base_url('uploads/perkara/'.$perkara['BERKAS_PERKARA']); ?>" target="_blank" class="text-primary">
                                        <?= $perkara['BERKAS_PERKARA']; ?>
                                    </a>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- TOMBOL AKSI -->
            <div class="text-end border-top pt-2 mt-2">
                <a href="<?= base_url('perkara'); ?>" class="btn btn-sm btn-secondary me-2">Kembali</a>
                <button type="submit" class="btn btn-sm btn-success px-3">
                    <i class="fas fa-save me-1"></i>Simpan Perkembangan
                </button>
            </div>
        </form>
    </div>
</div>