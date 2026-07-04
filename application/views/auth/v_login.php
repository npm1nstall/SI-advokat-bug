<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            
            <div class="card border-0 shadow-lg rounded-3">
                <div class="card-body p-5">
                    
                    <div class="text-center mb-4">
                        <div class="text-success mb-2">
                            <i class="fas fa-balance-scale fa-3x"></i>
                        </div>
                        <h4 class="fw-bold text-dark">SI ADVOKAT</h4>
                        <p class="text-muted small">Sistem Informasi Pemantauan & Manajemen Perkara</p>
                    </div>

                    <hr class="text-muted mb-4">

                    <?php if($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show small py-2" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $this->session->flashdata('error'); ?>
                            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo base_url('auth/proses_login'); ?>" method="POST">
                        
                        <div class="mb-3">
                            <label for="telp_klien" class="form-label text-secondary small fw-bold">USERNAME / NO. TELEPON</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" 
                                       name="telp_klien" 
                                       id="telp_klien" 
                                       class="form-control bg-light border-start-0 ps-0" 
                                       placeholder="Masukkan Nomor HP atau Username Anda" 
                                       autocomplete="off"
                                       required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label text-secondary small fw-bold">PASSWORD</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="form-control bg-light border-start-0 ps-0" 
                                       placeholder="Kosongkan jika Anda adalah Klien">
                            </div>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                *Klien: Isi No. Telepon & Kosongkan Password.<br>
                                *Staf: Isi Username & Password Staf Anda.
                            </div>
                        </div>

                        <div class="d-grid shadow-sm">
                            <button type="submit" class="btn btn-success fw-bold py-2.5 rounded-2">
                                <i class="fas fa-sign-in-alt me-2"></i> Masuk Sistem
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <div class="text-center mt-3 text-muted" style="font-size: 0.8rem;">
                &copy; 2026 Kantor Advokat & Kuasa Hukum. All Rights Reserved.
            </div>

        </div>
    </div>
</div>