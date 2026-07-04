<?php 
/**
 * ==============================================
 * SIDEBAR TEMPLATE - MENU ROLE BASED (KAPITAL AWAL)
 * File: v_sidebar.php
 * ==============================================
 */
$jabatan = $this->session->userdata('jabatan'); 
$is_klien = ($this->session->userdata('klien_logged_in') == TRUE);
?>

<div id="sidebar-wrapper"> 
    <div class="list-group list-group-flush my-3">

		<!-- ================= 1. MENU DASHBOARD UTAMA ================= -->
		<?php if ($is_klien): ?>
			<a href="<?= base_url('dashboard'); ?>" class="list-group-item bg-transparent text-white fw-medium border-0">
				<i class="fas fa-th-large me-3"></i> Dashboard Klien
			</a>

		<?php elseif ($jabatan == 'Keuangan' || $jabatan == 'keuangan'): ?>
			<!-- Arahkan ke method keuangan di controller Dashboard -->
			<a href="<?= base_url('dashboard/keuangan'); ?>" class="list-group-item bg-transparent text-white fw-medium border-0">
				<i class="fas fa-th-large me-3"></i> Dashboard Keuangan
			</a>

		<?php else: ?>
			<a href="<?= base_url('dashboard'); ?>" class="list-group-item bg-transparent text-white fw-medium border-0">
				<i class="fas fa-th-large me-3"></i> Dashboard
			</a>
		<?php endif; ?>


        <!-- ================= 2. JALUR MENUS KLIEN PERKARA ================= -->
        <?php if ($is_klien): ?>
            <a href="<?= base_url('perkara/jadwal_sidang'); ?>" class="list-group-item bg-transparent text-white fw-medium border-0">
               <i class="fas fa-calendar-check me-3"></i> Jadwal Sidang
            </a>
            <a href="<?= base_url('keuangan/pembayaran_klien'); ?>" class="list-group-item bg-transparent text-white fw-medium border-0">
               <i class="fas fa-wallet me-3"></i> Pembayaran
            </a>
            <a href="<?= base_url('perkara'); ?>" class="list-group-item bg-transparent text-white fw-medium border-0">
                <i class="fas fa-folder-open me-3"></i> Data Perkara
            </a>


        <!-- ================= 3. JALUR MENUS STAF INTERNAL ================= -->
        <?php else: ?>

            <!-- MENU UTAMA PERKARA -->
            <?php if (in_array($jabatan, ['Admin', 'Kuasa Hukum'])): ?>
                <a href="<?= base_url('perkara'); ?>" class="list-group-item bg-transparent text-white fw-medium border-0">
                    <i class="fas fa-gavel me-3"></i> Manajemen Perkara
                </a>
            <?php endif; ?>

            <!-- MENU ARSIP SURAT -->
            <?php if ($jabatan == 'Admin'): ?>
                <a href="<?= base_url('dashboard/surat'); ?>" class="list-group-item bg-transparent text-white fw-medium border-0">
                    <i class="fas fa-envelope me-3"></i> Manajemen Surat
                </a>
            <?php elseif ($jabatan == 'Pimpinan'): ?>
                <a href="<?= base_url('dashboard/laporan_surat'); ?>" class="list-group-item bg-transparent text-white fw-medium border-0">
                    <i class="fas fa-envelope me-3"></i> Laporan Surat
                </a>
            <?php endif; ?>


            <!-- ================= SEPARATOR KEUANGAN & OPERASIONAL ================= -->
           <div class="px-3 pt-3 text-white-50 small fw-bold">KEUANGAN & OPERASIONAL</div>

			<?php if ($jabatan == 'Kuasa Hukum'): ?>
				<a href="<?= base_url('keuangan/verifikasi'); ?>" class="list-group-item bg-transparent text-white border-0">
					<i class="fas fa-tasks me-3"></i> Verifikasi Berkas
				</a>
				<a href="<?= base_url('keuangan/pengajuan_ops'); ?>" class="list-group-item bg-transparent text-white border-0">
					<i class="fas fa-file-invoice-dollar me-3"></i> Ajukan Biaya Ops
				</a>
			<?php endif; ?>

			<?php if ($jabatan == 'Admin'): ?>
				<a href="<?= base_url('keuangan/verifikasi'); ?>" class="list-group-item bg-transparent text-white border-0">
					<i class="fas fa-tasks me-3"></i> Verifikasi Berkas
				</a>
			<?php endif; ?>

			<?php if ($jabatan == 'Keuangan'): ?>
				<a href="<?= base_url('keuangan/data_pengajuan'); ?>" class="list-group-item bg-transparent text-white border-0">
					<i class="fas fa-folder-open me-3"></i> Data Pengajuan
				</a>
				<a href="<?= base_url('keuangan/verifikasi_berkas_klien'); ?>" class="list-group-item bg-transparent text-white border-0">
					<i class="fas fa-tasks me-3"></i> Verifikasi Berkas Klien
				</a>
				<a href="<?= base_url('keuangan/pembayaran_invoice_klien'); ?>" class="list-group-item bg-transparent text-white border-0">
					<i class="fas fa-wallet me-3"></i> Pembayaran & Invoice
				</a>
			<?php endif; ?>


            <!-- APPROVAL KEUANGAN PIMPINAN -->
            <?php if ($jabatan == 'Pimpinan'): ?>
                <a href="<?= base_url('dashboard/keuangan/approval'); ?>" class="list-group-item bg-transparent text-white border-0">
                    <i class="fas fa-check-double me-3"></i> Approval Pimpinan
                </a>
            <?php endif; ?>

            <!-- AKSES MONITOR LAPORAN BERSAMA (FOLDER DASHBOARD) -->
            <?php if (in_array($jabatan, ['Keuangan', 'Pimpinan'])): ?>
                <a href="<?= base_url('dashboard/laporan_keuangan'); ?>" class="list-group-item bg-transparent text-white border-0">
                    <i class="fas fa-chart-line me-3"></i> Laporan Keuangan
                </a>
            <?php endif; ?>


            <!-- ================= SEPARATOR SDM & MASTER DATA ================= -->
            <?php if (in_array($jabatan, ['Admin', 'Pimpinan', 'Kuasa Hukum'])): ?>
                <div class="px-3 pt-3 text-white-50 small fw-bold">LAPORAN & SDM</div>
            <?php endif; ?>

            <!-- KHUSUS PIMPINAN -->
            <?php if ($jabatan == 'Pimpinan'): ?>
                <a href="<?= base_url('dashboard/laporan_perkara'); ?>" class="list-group-item bg-transparent text-white border-0">
                    <i class="fas fa-gavel me-3"></i> Laporan Perkara & Sidang
                </a>
                <a href="<?= base_url('dashboard/cuti'); ?>" class="list-group-item bg-transparent text-white border-0">
                    <i class="fas fa-umbrella-beach me-3"></i> Manajemen Cuti Staff
                </a>
            <?php endif; ?>

            <!-- KHUSUS KUASA HUKUM -->
            <?php if ($jabatan == 'Kuasa Hukum'): ?>
                <a href="<?= base_url('dashboard/ajukan_cuti'); ?>" class="list-group-item bg-transparent text-white border-0">
                    <i class="fas fa-umbrella-beach me-3"></i> Ajukan Cuti Mandiri
                </a>
            <?php endif; ?>

            <!-- KHUSUS ADMIN MASTER DATA -->
            <?php if ($jabatan == 'Admin'): ?>
                <a href="<?= base_url('dashboard/staff'); ?>" class="list-group-item bg-transparent text-white border-0">
                    <i class="fas fa-users me-3"></i> Data Staff
                </a>
            <?php endif; ?>

        <?php endif; ?>

        <!-- ================= 4. MENU LOGOUT AMAN ================= -->
        <a href="<?= base_url('auth/logout'); ?>" class="list-group-item bg-transparent text-danger fw-bold border-0 mt-3">
            <i class="fas fa-sign-out-alt me-3"></i> Logout
        </a>

    </div>
</div>


<!-- KONTEN UTAMA - Navbar + Judul Halaman -->
<div id="page-content-wrapper">
    <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
        <div class="d-flex align-items-center">
            <!-- Tombol toggle sidebar mobile -->
            <button id="menu-toggle" class="btn btn-outline-secondary me-3">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Judul dinamis dari $data['title'] di controller -->
            <h2 class="fs-2 m-0"><?= isset($title) ? $title : 'Dashboard'; ?></h2>
        </div>
    </nav>