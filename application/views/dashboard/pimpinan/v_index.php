<div class="container-fluid mt-2">


    <div class="row">

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Perkara</h6>
                    <h3><?= isset($jml_perkara) ? $jml_perkara : 0 ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Surat</h6>
                    <h3><?= isset($jml_surat) ? $jml_surat : 0 ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Staff</h6>
                    <h3><?= isset($jml_staff) ? $jml_staff : 0 ?></h3>
                </div>
            </div>
        </div>

    </div>

    <hr>

    <div class="row">

        <div class="col-md-6">
            <h5>Perkara Terbaru</h5>
            <ul class="list-group">
			<?php if (!empty($recent_perkara)): ?>
                <?php foreach ($recent_perkara as $p): ?>
                    <li class="list-group-item">
                        <?= $p->JUDUL_PERKARA; ?>
                    </li>
                <?php endforeach; ?>
			<?php endif; ?>
            </ul>
        </div>

        <div class="col-md-6">
    <h5 class="small fw-bold text-secondary mb-2"><i class="fas fa-wallet me-1"></i> Keuangan Terbaru</h5>
    <ul class="list-group">
        <?php if (!empty($recent_keuangan)): ?>
            <?php foreach ($recent_keuangan as $k): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center p-2 small">
                    <div class="text-truncate me-2" style="max-width: 70%;">
                        <!-- Menampilkan Keperluan Dana -->
                        <?= !empty($k->KEPERLUAN_DANA_OPS) ? $k->KEPERLUAN_DANA_OPS : '<span class="text-muted italic">Tidak ada keterangan</span>'; ?>
                    </div>
                    
                    <!-- Menampilkan Status Verifikasi (Badge Warna) -->
                    <?php 
                    $status = $k->STATUS_VERIFIKASI_OPS;
                    if ($status == 'Pending Pimpinan') {
                        echo '<span class="badge bg-warning text-dark px-2 py-1">Pending Pimpinan</span>';
                    } elseif ($status == 'Pending Keuangan') {
                        echo '<span class="badge bg-info text-dark px-2 py-1">Pending Keuangan</span>';
                    } elseif ($status == 'Validasi Selesai') {
                        echo '<span class="badge bg-success px-2 py-1">Selesai</span>';
                    } else {
                        echo '<span class="badge bg-secondary px-2 py-1">'.($status ?? 'Pending').'</span>';
                    }
                    ?>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="list-group-item text-center text-muted small py-3">Belum ada riwayat keuangan terbaru.</li>
        <?php endif; ?>
    </ul>
</div>


    </div>

</div>