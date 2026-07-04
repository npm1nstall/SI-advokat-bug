<?php 
/**
 * ==============================================
 * VIEW: Dashboard Klien - Halaman Utama
 * File: klien/v_dashboard.php
 * Fungsi: Landing page klien setelah login
 * Data dari Controller: $perkara array, $notif_bayar int
 * UX: Klien langsung liat status perkara + tagihan + sidang
 * ==============================================
 */
?>

<!-- GREETING KLIEN -->
<div class="d-flex justify-content-between align-items-center mb-4 ms-3 mt-2">
    <div>
        <h4 class="fw-bold mb-1">
            Selamat Datang,
            <!-- htmlspecialchars = security wajib cegah XSS -->
            <?= htmlspecialchars($this->session->userdata('nama_klien') ?? 'Klien'); ?>
        </h4>
    </div>
</div>

<!-- ALERT NOTIFIKASI TAGIHAN: Muncul dinamis kalo ada tagihan -->
<?php if (!empty($notif_bayar) && $notif_bayar > 0): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Perhatian!</strong> Anda memiliki <?= $notif_bayar ?> tagihan yang belum diselesaikan. 
        <a href="<?= base_url('keuangan/pembayaran_klien') ?>" class="alert-link fw-bold">Klik di sini untuk membayar</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- ROW KONTEN UTAMA: 8:4 LAYOUT -->
<div class="row">
    <!-- KOLOM KIRI: DETAIL PERKARA -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3 text-success">
                <i class="fas fa-balance-scale me-2"></i> Detail Perkara
            </h5>
            
            <div class="row">
                <!-- NO PERKARA -->
                <div class="col-md-6 mb-3">
                    <p class="mb-1 text-muted small">No. Perkara</p>
                    <h6 class="fw-bold"><?= $perkara['NO_PERKARA'] ?? 'Data belum tersedia'; ?></h6>
                </div>
                
                <!-- AGENDA SIDANG -->
                <div class="col-md-6 mb-3">
				<p class="mb-1 text-muted small">Agenda Sidang Berikutnya</p>
				<h6 class="fw-bold text-primary">
					<?php 
					$tgl_sidang = isset($perkara['AGENDA_SIDANG']) ? trim($perkara['AGENDA_SIDANG']) : '';
					
					// 1. Cek jika kosong atau tanda strip
					if ($tgl_sidang == '' || $tgl_sidang == '-' || $tgl_sidang == '0000-00-00' || $tgl_sidang == '0000-00-00 00:00:00') {
						echo 'Tidak ada agenda';
					} else {
						// 2. Cek apakah teks di database bisa diubah jadi tanggal valid
						$timestamp = strtotime($tgl_sidang);
						
						if ($timestamp === FALSE || $timestamp < 0) {
							// Jika isinya teks biasa (bukan tanggal), langsung tampilkan teksnya apa adanya
							echo htmlspecialchars($tgl_sidang);
						} else {
							// Jika isinya format tanggal valid, baru kita format ke d M Y H:i
							echo date('d M Y H:i', $timestamp);
						}
					}
					?>
					</h6>
				</div>
            </div>
            
            <!-- PROGRESS BAR STATUS PERKARA -->
            <div class="mt-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold text-muted small">Progres Status Perkara</span>
                    <span class="text-success fw-bold"><?= $perkara['STATUS_PERKARA'] ?? 'Belum dimulai'; ?></span>
                </div>
                <div class="progress" style="height: 12px; border-radius: 6px;">
                    <!-- Width bisa dibikin dinamis nanti: 0%, 25%, 50%, 75%, 100% sesuai status -->
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         style="width: 70%" 
                         aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KOLOM KAN: STATUS PEMBAYARAN -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 bg-primary text-white h-100">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-wallet me-2"></i> Status Pembayaran
            </h5>
            <div class="text-center py-3">
                <!-- Status: Lunas / Belum Bayar / Menunggu Verifikasi -->
                <h3 class="mb-2"><?= $perkara['STATUS_BAYAR_KLIEN'] ?? 'Belum ada data'; ?></h3>
                <p class="opacity-75 small mb-0">Lihat detail transaksi di menu Keuangan</p>
            </div>
            <!-- Tombol ke halaman upload bukti bayar -->
            <a href="<?= base_url('dashboard/keuangan_klien'); ?>" class="btn btn-light w-100 fw-bold mt-auto">
                <i class="fas fa-receipt me-1"></i> Cek Detail
            </a>
        </div>
    </div>
</div>

<!-- CARD CATATAN TERBARU DARI ADVOKAT -->
<div class="card border-0 shadow-sm p-4 mt-2">
    <h5 class="fw-bold mb-3">
        <i class="fas fa-history me-2"></i> Catatan Terbaru dari Advokat
    </h5>
    <p class="text-muted mb-0 fst-italic">
        "<?= $perkara['CATATAN_DISPOSISI'] ?? 'Tidak ada catatan terbaru dari tim advokat.'; ?>"
    </p>
</div>