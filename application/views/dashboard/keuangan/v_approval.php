<?php 
/**
 * ==============================================
 * VIEW: Approval Pimpinan - Biaya Operasional
 * File: keuangan/v_approval.php
 * Fungsi: Tampilkan antrean pengajuan OPS yg status 'Verified'
 * Data dari: Dashboard.php case 'approval' -> $data['antrean_approval']
 * Action: ACC / TOLAK ke Dashboard.php -> proses_approval()
 * Workflow: Pending Admin → Pending Kuasa Hukum → Verified → Approval Pimpinan
 * ==============================================
 */
?>

<div class="card shadow border-0 m-2">
    <!-- Header card biru -->
    <div class="card-header bg-primary text-white py-2">
        <h6 class="m-0"><i class="fas fa-check-double me-2"></i> Persetujuan (Approval) Biaya Operasional Perkara</h6>
    </div>

    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle small text-center">
                <thead class="table-light">
                    <tr>
                        <th>No Perkara</th>
                        <th>Judul Kasus</th>
                        <th>Jumlah Dana</th>
                        <th>Keperluan / Deskripsi</th>
                        <th>TTD Pengajuan</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($antrean_approval)): ?>
                        <?php foreach($antrean_approval as $a): ?>
                            <tr>
                                <!-- NO_PERKARA bold biar gampang scan -->
                                <td><span class="fw-bold text-dark"><?= $a['NO_PERKARA']; ?></span></td>
                                
                                <!-- Judul kasus rata kiri biar enak dibaca -->
                                <td class="text-start"><?= $a['JUDUL_PERKARA']; ?></td>
                                
                                <!-- Format rupiah Indonesia: Rp 1.500.000 -->
                                <td><span class="fw-bold text-success">Rp <?= number_format($a['JMLH_PENGAJUAN_OPS'], 0, ',', '.'); ?></span></td>
                                
                                <!-- Keperluan dana rata kiri + small muted -->
                                <td class="text-start"><small class="text-muted"><?= $a['KEPERLUAN_DANA_OPS']; ?></small></td>
                                
                                <!-- Status TTD dari Kuasa Hukum -->
                                <td><span class="badge bg-secondary"><i class="fas fa-signature"></i> Verified</span></td>
                                
                               <!-- BUTTON AKSI: ACC / TOLAK -->
								<td style="min-width: 180px;">
									<div class="d-flex justify-content-center align-items-center gap-1">
										<!-- PERBAIKAN LINK ACC: Mengarah langsung ke controller Dashboard -->
										<a href="<?= base_url('dashboard/proses_approval/'.$a['NO_TRANSAKSI'].'/ACC'); ?>" 
										   class="btn btn-sm btn-success text-white py-1 px-2 text-nowrap"
										   onclick="return confirm('Yakin setujui pengajuan ini?')">
											<i class="fas fa-check"></i> Setujui
										</a>
										
										<!-- PERBAIKAN LINK TOLAK: Mengarah langsung ke controller Dashboard -->
										<a href="<?= base_url('dashboard/proses_approval/'.$a['NO_TRANSAKSI'].'/TOLAK'); ?>" 
										   class="btn btn-sm btn-danger text-white py-1 px-2 text-nowrap"
										   onclick="return confirm('Yakin tolak pengajuan ini?')">
											<i class="fas fa-times"></i> Tolak
										</a>
									</div>
								</td>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- EMPTY STATE: Kalo ga ada pengajuan pending -->
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-check-circle fs-3 text-success mb-2 d-block"></i>
                                Tidak ada antrean pengajuan biaya operasional yang butuh persetujuan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>