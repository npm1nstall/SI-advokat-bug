<?php 
/**
 * ==============================================
 * VIEW: Form Tambah Staff/Klien - Admin Only
 * File: admin/v_tambah_staff.php
 * Fungsi: Form input data user baru ke tabel KARYAWAN atau PERKARA
 * Submit ke: Dashboard.php -> simpan_staff()
 * Fitur: JS toggle password field kalo role = Klien
 * ==============================================
 */
?>

<div class="container-fluid px-4">

    <!-- HEADER FORM -->
    <div class="mt-4 mb-4">
        <h3 class="fw-bold">Tambah Akun Pengguna Baru</h3>
        <p class="text-muted small">Daftarkan Admin, Kuasa Hukum, Keuangan, Pimpinan, atau Klien ke dalam sistem internal.</p>
    </div>

    <!-- FLASHDATA ERROR VALIDASI -->
    <?php if($this->session->flashdata('pesan_error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= $this->session->flashdata('pesan_error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- FORM CARD -->
    <div class="card shadow-sm border-0 col-lg-8">
        <div class="card-body">
            <form action="<?= base_url('dashboard/simpan_staff'); ?>" method="post">

                <!-- SELECT ROLE: Penentu tabel insert -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Otoritas Pengguna (Role / Jabatan)</label>
                    <select name="role" id="role-select" class="form-select form-control fw-medium text-dark" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="Admin">Admin (Otoritas Awal & Surat)</option>
                        <option value="Kuasa Hukum">Kuasa Hukum (Pengacara/Validasi)</option>
                        <option value="Keuangan">Staf Keuangan (Invoice/Pembayaran)</option>
                        <option value="Pimpinan">Pimpinan (Direktur/Approval)</option>
                        <option value="Klien">Klien (Akses Login Instan via No. Telp)</option>
                    </select>
                    <small class="text-muted">Pilih "Klien" untuk daftar klien tanpa password</small>
                </div>

                <!-- INPUT NAMA -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                </div>

                <!-- INPUT TELP/ID LOGIN -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor Telepon (ID Login Utama)</label>
                    <input type="text" name="telp" class="form-control" placeholder="Contoh: 08123456789" required>
                    <small class="text-muted">Dipake buat login klien + identifikasi staff</small>
                </div>

              <!-- INPUT PASSWORD: Auto hide kalo role Klien -->
				<div class="mb-3" id="password-group">
					<label class="form-label fw-semibold">Kata Sandi (Password)</label>
					<!-- Dibungkus input-group agar tombol mata menyatu di dalam kotak input -->
					<div class="input-group">
						<input type="password" name="password" id="password-field" class="form-control" placeholder="Masukkan password akun" required>
						<button class="btn btn-outline-secondary" type="button" id="btnTogglePassword">
							<i class="fas fa-eye" id="iconMata"></i>
						</button>
					</div>
					<small class="text-muted">Wajib diisi untuk staff. Kosongkan otomatis untuk Klien</small>
				</div>


                <!-- TEXTAREA ALAMAT: Khusus Klien -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Rumah</label>
                    <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap jika mendaftarkan Klien"></textarea>
                </div>

                <!-- BUTTON SUBMIT -->
                <div class="pt-2">
                    <button type="submit" class="btn btn-primary px-4 fw-medium">
                        <i class="fas fa-user-plus me-2"></i> Daftarkan Pengguna
                    </button>
                    <a href="<?= base_url('dashboard/staff'); ?>" class="btn btn-secondary ms-1">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- 
SCRIPT JS: TOGGLE PASSWORD FIELD 
Logic: Kalo pilih role "Klien" → hide password + remove required
Kalo pilih staff → show password + required
Ini yg bikin form bisa submit tanpa password buat klien
-->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const roleSelect = document.getElementById("role-select");
        const passwordGroup = document.getElementById("password-group");
        const passwordField = document.getElementById("password-field");
        const btnTogglePassword = document.getElementById("btnTogglePassword");
        const iconMata = document.getElementById("iconMata");

        // 1. Logika bawaan Anda untuk Auto-Hide Role Klien (Jangan Dihapus)
        if (roleSelect) {
            roleSelect.addEventListener("change", function () {
                if (this.value === "Klien") {
                    passwordGroup.style.display = "none";
                    passwordField.removeAttribute("required");
                    passwordField.value = ""; 
                } else {
                    passwordGroup.style.display = "block";
                    passwordField.setAttribute("required", "required");
                }
            });
        }

        // 2. Logika Baru: Klik Ikon Mata untuk Show/Hide Password Staff
        if (btnTogglePassword) {
            btnTogglePassword.addEventListener("click", function () {
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    iconMata.classList.remove("fa-eye");
                    iconMata.classList.add("fa-eye-slash");
                } else {
                    passwordField.type = "password";
                    iconMata.classList.remove("fa-eye-slash");
                    iconMata.classList.add("fa-eye");
                }
            });
        }
    });
</script>

</script>