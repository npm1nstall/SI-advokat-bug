<div class="container mt-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white fw-bold py-3">
            <i class="fas fa-calendar-alt me-2"></i>Jadwal Sidang Anda
        </div>
        <div class="card-body">
            
            <!-- HATI-HATI: $perkara harus array hasil foreach. Kalo cuma 1 data pake $perkara['TGL_SIDANG'] bakal error kalo klien punya banyak kasus -->
            <?php if(!empty($perkara)): ?>
                
                <?php foreach($perkara as $p): ?>
                <!-- HATI-HATI: Skip kasus "Pendaftaran Akun Baru" biar ga muncul di jadwal -->
                <?php if(strpos($p['JUDUL_PERKARA'], 'Pendaftaran') !== false) continue; ?>
                
                <div class="border rounded p-3 mb-3 <?= empty($p['TGL_SIDANG']) ? 'bg-light' : 'border-primary'; ?>">
                    
                    <!-- HATI-HATI: Judul kasus wajib ditampilin, kalo ga klien bingung ini sidang kasus yg mana -->
                    <h6 class="fw-bold text-dark mb-2"><?= $p['NO_PERKARA']; ?> - <?= $p['JUDUL_PERKARA']; ?></h6>
                    
                    <?php if(!empty($p['TGL_SIDANG'])): ?>
                        <!-- KASUS UDAH ADA JADWAL -->
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Agenda Sidang</small>
                                <p class="fw-bold mb-1"><?= $p['AGENDA_SIDANG'] ?? '-'; ?></p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Waktu Sidang</small>
                                <!-- HATI-HATI: Format tanggal Indonesia biar rapi "05 Oktober 2026" bukan "2026-10-05" -->
                                <p class="fw-bold text-primary mb-1">
                                    <?= date('d F Y, H:i', strtotime($p['TGL_SIDANG'])); ?> WIB
                                </p>
                            </div>
                        </div>
                        
                        <?php if(!empty($p['HASIL_SIDANG'])): ?>
                            <hr class="my-2">
                            <small class="text-muted">Hasil Sidang Terakhir</small>
                            <p class="mb-0"><?= $p['HASIL_SIDANG']; ?></p>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <!-- KASUS BELUM ADA JADWAL -->
                        <!-- HATI-HATI: Kasih penanda khusus biar ga dikira error -->
                        <div class="text-center py-3">
                            <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Belum ada jadwal sidang untuk kasus ini</p>
                            <small>Status: <?= $p['STATUS_PERKARA']; ?></small>
                        </div>
                    <?php endif; ?>
                    
                </div>
                <?php endforeach; ?>
                
            <?php else: ?>
                <!-- HATI-HATI: Kondisi kalo klien sama sekali belum punya perkara -->
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Anda belum memiliki perkara aktif.</p>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>