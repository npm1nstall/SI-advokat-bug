<div class="card shadow-sm">
    <div class="card-body">
        <!-- FIX NOTIFIKASI: Menampung pesan sukses/gagal tepat di atas judul h5 -->
        <?php if($this->session->flashdata('pesan')): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $this->session->flashdata('pesan'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('pesan_error')): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= $this->session->flashdata('pesan_error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <h5>Data Tagihan Perkara</h5>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>No Perkara</th>
                    <th>Jumlah Tagihan</th>
                    <th>Status</th>
                    <!-- HATI-HATI: Jangan hapus style width ini, biar tombol "Bayar" ga turun ke bawah -->
                    <th class="text-center" style="width:180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($pembayaran)): ?>
                    <?php foreach($pembayaran as $p): ?>
                    <tr>
                        <!-- HATI-HATI: NO_PERKARA harus pake fw-bold biar konsisten sama tabel perkara -->
                        <td><span class="fw-bold text-dark"><?= $p->NO_PERKARA; ?></span></td>
                        
                        <!-- HATI-HATI: number_format wajib 0 desimal biar rapi Rp 1.000.000 bukan 1000000.00 -->
                        <td><span class="fw-bold text-success">Rp <?= number_format($p->TTL_TAGIHAN_KLIEN, 0, ',', '.'); ?></span></td>
                        
                        <td>
                            <!-- HATI-HATI: Status harus 3 kondisi doang, jangan tambah2 biar ga error badge -->
                            <?php if($p->STATUS_BAYAR_KLIEN == 'Belum Bayar'): ?>
                                <span class="badge bg-warning text-dark px-3 py-2">Belum Bayar</span>
                            <?php elseif($p->STATUS_BAYAR_KLIEN == 'Menunggu Verifikasi'): ?>
                                <span class="badge bg-info text-white px-3 py-2">Menunggu Verifikasi</span>
                            <?php else: ?>
                                <span class="badge bg-success px-3 py-2"><?= $p->STATUS_BAYAR_KLIEN; ?></span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="text-center">
                            <!-- HATI-HATI: Kondisi tombol. Kalo udah Lunas/Verifikasi jangan kasih tombol lagi -->
                            <?php if($p->STATUS_BAYAR_KLIEN == 'Belum Bayar' || $p->STATUS_BAYAR_KLIEN == 'Ditolak'): ?>
                                <button type="button"
                                    class="btn btn-sm btn-primary fw-medium px-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalBayar<?= $p->NO_TRANSAKSI; ?>">
                                    <i class="fas fa-upload me-1"></i>
                                    Bayar Sekarang
                                </button>
                            <?php else: ?>
                                <span class="text-muted small fst-italic">Tidak ada aksi</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- =========================================================================
                       MODAL BAYAR: HATI-HATI JANGAN UBAH ID & NAME DI BAWAH INI
                       1. id="modalBayar<?= $p->NO_TRANSAKSI; ?>" harus unik per baris
                       2. name="no_transaksi" harus sama persis sama di Controller Keuangan.php
                       3. name="bukti_bayar" harus sama persis sama di $this->upload->do_upload()
                       Kalo diubah = upload gagal + data ga masuk DB
                       ========================================================================= -->
                    <div class="modal fade" id="modalBayar<?= $p->NO_TRANSAKSI; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            
                            <!-- HATI-HATI: Action harus ke keuangan/klien_kirim_bukti, bukan perkara/simpan -->
                            <form action="<?= base_url('keuangan/klien_kirim_bukti'); ?>" method="post" enctype="multipart/form-data">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title fw-bold"><i class="fas fa-wallet me-2"></i>Konfirmasi Pembayaran</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- HATI-HATI: Hidden input ini WAJIB ADA. Kalo dihapus controller ga tau transaksi mana -->
                                        <input type="hidden" name="no_transaksi" value="<?= $p->NO_TRANSAKSI; ?>">

                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Nomor Perkara</label>
                                            <!-- HATI-HATI: readonly biar klien ga bisa ganti manual -->
                                            <input type="text" class="form-control bg-light text-dark fw-bold" value="<?= $p->NO_PERKARA; ?>" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Total yang Harus Dibayar</label>
                                            <div class="form-control bg-light text-success fw-bold fs-5">Rp <?= number_format($p->TTL_TAGIHAN_KLIEN, 0, ',', '.'); ?></div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Unggah Bukti Transfer (Gambar/PDF)</label>
                                            <!-- HATI-HATI: name="bukti_bayar" harus sama persis sama di Controller -->
                                            <input type="file" name="bukti_bayar" class="form-control" required>
                                            <div class="form-text text-muted small">Format: JPG, PNG, atau PDF. Maksimal ukuran file 2MB.</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success px-4 fw-medium">
                                            <i class="fas fa-paper-plane me-1"></i> Kirim Bukti
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada tagihan resmi untuk Anda.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>