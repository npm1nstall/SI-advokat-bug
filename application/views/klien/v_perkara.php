<div class="card shadow-sm">
    <div class="card-header bg-success text-white fw-bold">
        <i class="fas fa-file-alt me-2"></i>Detail Perkara Lengkap
    </div>
    <div class="card-body">
        <?php if($perkara): ?>
            <table class="table table-bordered align-middle">
                <!-- HATI-HATI: Jangan hapus field di bawah ini. Kalo dihapus = data ga muncul = kamu komplain lagi -->
                
                <tr>
                    <th width="30%">No. Perkara</th>
                    <td class="fw-bold text-primary"><?= $perkara['NO_PERKARA']; ?></td>
                </tr>
                
                <tr>
                    <th>Judul Kasus</th>
                    <td><?= $perkara['JUDUL_PERKARA']; ?></td>
                </tr>
                
                <!-- HATI-HATI: 3 FIELD INI WAJIB ADA BIAR GA "BERKURANG" LAGI -->
                <tr>
                    <th>Nama Klien</th>
                    <td class="fw-bold"><?= $perkara['NAMA_KLIEN'] ?? '-'; ?></td>
                </tr>
                
                <tr>
                    <th>No. HP Klien</th>
                    <td><?= $perkara['TELP_KLIEN'] ?? '-'; ?></td>
                </tr>
                
                <tr>
                    <th>Alamat Klien</th>
                    <td><?= $perkara['ALAMAT_KLIEN'] ?? '-'; ?></td>
                </tr>
                
                <tr>
                    <th>Tanggal Masuk</th>
                    <!-- HATI-HATI: Format tanggal biar rapi. Kalo langsung echo = 2026-06-19 18:04:29 jelek -->
                    <td><?= date('d-m-Y H:i', strtotime($perkara['TGL_MASUK'])); ?></td>
                </tr>
                
                <tr>
                    <th>Status</th>
                    <td><span class="badge bg-info"><?= $perkara['STATUS_PERKARA']; ?></span></td>
                </tr>
                
                <!-- HATI-HATI: Path berkas harus "uploads/perkara/" bukan "uploads/" doang. Kalo salah = 404 -->
                <tr>
                    <th>Berkas</th>
                    <td>
                        <?php if(!empty($perkara['BERKAS_PERKARA'])): ?>
                            <a href="<?= base_url('uploads/perkara/'.$perkara['BERKAS_PERKARA']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i>Download Berkas
                            </a>
                        <?php else: ?>
                            <span class="text-muted">- Belum ada berkas -</span>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <!-- HATI-HATI: 3 FIELD SIDANG INI MUNCUL KALO UDAH DIISI ADMIN. Kalo NULL = tampil "-" -->
                <tr>
                    <th>Tanggal Sidang</th>
                    <td><?= !empty($perkara['TGL_SIDANG']) ? date('d-m-Y H:i', strtotime($perkara['TGL_SIDANG'])) : '- Belum dijadwalkan -'; ?></td>
                </tr>
                
                <tr>
                    <th>Agenda Sidang</th>
                    <td><?= $perkara['AGENDA_SIDANG'] ?? '-'; ?></td>
                </tr>
                
                <tr>
                    <th>Hasil Sidang</th>
                    <td><?= $perkara['HASIL_SIDANG'] ?? '-'; ?></td>
                </tr>
            </table>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle me-2"></i>Data perkara tidak ditemukan.
            </div>
        <?php endif; ?>
    </div>
</div>