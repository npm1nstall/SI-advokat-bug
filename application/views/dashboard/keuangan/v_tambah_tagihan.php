<?php 
/**
 * ==============================================
 * VIEW: Form Terbit Invoice Klien
 * File: keuangan/v_tambah_tagihan.php
 * Fungsi: Input NO_INVOICE + TTL_TAGIHAN_KLIEN ke tabel KEUANGAN
 * Submit ke: Keuangan.php -> simpan_tagihan_final()
 * Trigger: Dipanggil dari v_pembayaran.php tombol "Buat Invoice"
 * Alur: Pending Keuangan → Form ini → Klien dapat notif bayar
 * ==============================================
 */
?>

<div class="container-fluid px-4">

    <div class="mt-4 mb-4">
        <h3 class="fw-bold">Penerbitan Invoice & Tagihan</h3>
        <p class="text-muted small">Isi detail invoice untuk perkara yg statusnya "Siap Buat Invoice"</p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <?php 
            /**
             * AMBIL ID DARI URL
             * URL: /dashboard/keuangan/tambah_tagihan/12 
             * segment(4) = 'keuangan', segment(5) = '12'
             * Fallback segment(4) buat jaga2 routing beda
             */
            $seg_4 = $this->uri->segment(4);
            $seg_5 = $this->uri->segment(5);
            $no_transaksi = ($seg_4 !== 'tambah_tagihan' && !empty($seg_4)) ? $seg_4 : $seg_5;
            ?>

            <!-- FORM SUBMIT KE CONTROLLER KEUANGAN (Ditambahkan urlencode demi keamanan) -->
            <form action="<?= base_url('dashboard/keuangan/simpan_tagihan_final/' . urlencode($no_transaksi)); ?>" method="post">

                <!-- NO PERKARA: Locked biar ga bisa diubah manual -->
                <!-- Bagian Input No Perkara di v_tambah_tagihan.php -->
					<div class="mb-3">
						<label class="form-label fw-semibold text-muted">No Perkara (Terkunci Otomatis)</label>
						
						<?php if (!empty($perkara_tunggal)): ?>
							<!-- MODE 1: Jika diakses dari tombol klik "Buat Invoice" yang presisi -->
							<input type="text" class="form-control bg-light text-dark fw-bold" value="<?= $perkara_tunggal['NO_PERKARA'] ?>" readonly>
							<input type="hidden" name="no_perkara" value="<?= $perkara_tunggal['NO_PERKARA'] ?>">
						<?php else: ?>
							<!-- MODE 2: Fallback Dropdown jika masuk tanpa membawa ID transaksi di URL -->
							<select name="no_perkara" class="form-control fw-bold" required>
								<option value="">-- Pilih Perkara Berstatus Pending Keuangan --</option>
								<?php foreach($daftar_perkara_all as $p): ?>
									<?php 
									// Cek status operasional secara realtime
									$cek_ops = $this->db->get_where('keuangan', ['NO_PERKARA' => $p['NO_PERKARA']])->row_array();
									$status_ops = $cek_ops['STATUS_VERIFIKASI_OPS'] ?? '';
									
									// Pengecekan case-insensitive (Aman dari huruf besar/kecil)
									if(strcasecmp($status_ops, 'Pending Keuangan') == 0): ?>
										<option value="<?= $p['NO_PERKARA'] ?>">
											<?= $p['NO_PERKARA'] ?> (Siap Terbit Invoice)
										</option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
						<?php endif; ?>
					</div>
                <!-- INPUT NO INVOICE: Format bebas, contoh INV/2026/001 -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor Resmi Invoice</label>
                    <input type="text"
                           name="no_invoice"
                           class="form-control"
                           placeholder="Contoh: INV/<?= date('Y'); ?>/001"
                           required>
                    <small class="text-muted">Format bebas. Disarankan INV/Tahun/Urutan</small>
                </div>

                <!-- INPUT TOTAL TAGIHAN: Angka doang, tanpa titik/koma -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Total Nominal Tagihan Klien (Rp)</label>
                    <input type="number"
                           name="ttl_tagihan"
                           class="form-control"
                           placeholder="Masukkan jumlah biaya tanpa titik/koma"
                           min="1"
                           required>
                    <small class="text-muted">Contoh: 5000000 = Rp 5.000.000</small>
                </div>

                <!-- BUTTON SUBMIT -->
                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Terbitkan Invoice & Notifikasi Klien
                </button>

                <a href="<?= base_url('dashboard/keuangan/pembayaran'); ?>"
                   class="btn btn-secondary ms-1">
                    Batal
                </a>

            </form>
        </div>
    </div>
</div>
