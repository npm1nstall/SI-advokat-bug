<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No Perkara</th>
                        <th>No Invoice</th>
                        <th>Total Tagihan</th>
                        <th>Bukti Bayar</th>
                        <th>Alur Berkas</th>
                        <th>Status Bayar</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($tagihan)): ?>
                        <?php foreach($tagihan as $t): ?>
                        <tr>
                            <td><span class="fw-semibold text-dark"><?= $t['NO_PERKARA']; ?></span></td>
                            
                            <td>
                                <?= !empty($t['NO_INVOICE'])
                                    ? '<span class="badge bg-dark">'.$t['NO_INVOICE'].'</span>'
                                    : '<span class="text-muted small italic">Belum Diterbitkan</span>'; ?>
                            </td>

                            <td>
                                <?php if(!empty($t['TTL_TAGIHAN_KLIEN'])): ?>
                                    <span class="fw-bold text-success">Rp <?= number_format($t['TTL_TAGIHAN_KLIEN'],0,',','.'); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if(!empty($t['BUKTI_BAYAR_KLIEN'])): ?>
									<a href="<?= base_url('uploads/pembayaran/'.$t['BUKTI_BAYAR_KLIEN']); ?>" target="_blank" class="btn btn-xs btn-outline-primary small py-1 px-2" style="font-size: 12px;"><i class="fas fa-eye"></i> Lihat Bukti</a>
								<?php else: ?>
									<span class="badge bg-secondary text-white-50">Belum Upload</span>
								<?php endif; ?>
                            </td>

                            <td>
                                <?php 
                                $ops = $t['STATUS_VERIFIKASI_OPS'];
                                $bayar = $t['STATUS_BAYAR_KLIEN'];

                                // Sinkronisasi string status menggunakan huruf kecil sesuai controller
                                if ($bayar == 'Lunas') {
                                    echo '<span class="badge bg-success">Validasi Selesai</span>';
                                } elseif ($ops == 'Pending Admin') {
                                    echo '<span class="badge bg-info text-dark">Di Meja Admin</span>';
                                } elseif ($ops == 'Pending Kuasa Hukum') {
                                    echo '<span class="badge bg-primary">Di Kuasa Hukum</span>';
                                } elseif (strtolower($ops) == 'pending keuangan') {
                                    echo '<span class="badge bg-warning text-dark">Siap Buat Invoice</span>';
                                } else {
                                    echo '<span class="badge bg-success">Validasi Selesai</span>';
                                }
                                ?>
                            </td>

                            <td>
                                <?php
                                $status = $t['STATUS_BAYAR_KLIEN'];
                                if ($status == 'Lunas') {
                                    echo '<span class="badge bg-success">Lunas</span>';
                                } elseif ($status == 'Menunggu Verifikasi') {
                                    echo '<span class="badge bg-warning text-dark">Menunggu Verifikasi</span>';
                                } elseif ($status == 'Ditolak') {
                                    echo '<span class="badge bg-danger">Ditolak</span>';
                                } else {
                                    echo '<span class="badge bg-secondary">Belum Bayar</span>';
                                }
                                ?>
                            </td>
                            <td>
								  <?php if(strcasecmp($t['STATUS_BAYAR_KLIEN'], 'Lunas') == 0): ?>
										<span class="badge bg-success small py-1 px-2 fw-medium" style="font-size: 12px;"><i class="fas fa-check-circle me-1"></i> Lunas & Selesai</span>
									<?php elseif(strcasecmp($t['STATUS_VERIFIKASI_OPS'], 'Pending Keuangan') == 0): ?>
										<a href="<?= base_url('dashboard/keuangan/tambah_tagihan/'.urlencode($t['NO_TRANSAKSI'])); ?>" class="btn btn-sm btn-success fw-medium"><i class="fas fa-file-invoice"></i> Buat Invoice</a>
									<?php else: ?>
										<a href="<?= base_url('dashboard/keuangan/edit_tagihan/' . urlencode($t['NO_TRANSAKSI'])); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
									<?php endif; ?>
							
								<?php if($t['STATUS_BAYAR_KLIEN'] == 'Menunggu Verifikasi'): ?>
									<a href="<?= base_url('dashboard/keuangan/verifikasi_bayar/'.urlencode($t['NO_TRANSAKSI'])); ?>" 
									   class="btn btn-sm btn-info text-white ms-1">Verifikasi</a>
								<?php endif; ?>
							</td>

                            
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data transaksi atau tagihan klien</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
