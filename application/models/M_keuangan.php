<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ==============================================
 * MODEL: M_keuangan 
 * Fungsi: Query database tabel keuangan + JOIN perkara
 * Dipake di: keuangan.php, Dashboard.php, perkara.php
 * ==============================================
 */
class M_keuangan extends CI_Model {

    /**
     * Hitung jumlah pengajuan berdasarkan status
     * Dipake di: Dashboard keuangan buat card summary
     * Contoh: count_pengajuan_by_status('Pending keuangan')
     * 
     * @param string $status Status_verifikasi_ops yg mau dihitung
     * @return int Jumlah data
     */
    public function count_pengajuan_by_status($status) {
        return $this->db
                    ->where('STATUS_VERIFIKASI_OPS', $status)
                    ->count_all_results('keuangan');
    }

    /**
     * Hitung notifikasi tagihan belum bayar - KHUS KLIEN
     * Logic: Cuma muncul kalo STATUS_VERIFIKASI_OPS = 'Selesai' 
     *        + STATUS_BAYAR_KLIEN = 'Belum Bayar'
     * Dipake di: Dashboard.php index klien buat badge notif
     * 
     * @param string $telp Nomor telp klien dari session
     * @return int Jumlah tagihan yg harus dibayar
     */
    public function count_tagihan_belum_bayar($telp) {
        return $this->db
            ->from('keuangan')
            ->join('perkara', 'keuangan.NO_perkara = perkara.NO_perkara')
            ->where('perkara.TELP_KLIEN', $telp)
            ->where('keuangan.STATUS_VERIFIKASI_OPS', 'Selesai') // Kunci: nunggu invoice terbit dulu
            ->where('keuangan.STATUS_BAYAR_KLIEN', 'Belum Bayar')
            ->count_all_results();
    }

    /**
     * Simpan data pengajuan baru ke tabel keuangan
     * Dipake di: keuangan.php proses_pengajuan & klien_upload_berkas
     * 
     * @param array $data Data array NO_TRANSAKSI, NO_perkara, status, dll
     * @return bool True kalo berhasil insert
     */
    public function simpan_pengajuan($data) {
        return $this->db->insert('keuangan', $data);
    }

    /**
     * Ambil detail data pembayaran klien + join judul perkara
     * Logic: Cuma ambil yg STATUS_VERIFIKASI_OPS = 'Selesai' 
     *        = yg udah diterbitin invoice sama keuangan
     * Dipake di: keuangan.php pembayaran_klien
     * 
     * @param string $telp Nomor telp klien dari session
     * @return array Object hasil query
     */
    public function get_data_pembayaran_klien($telp) {
        return $this->db
            ->select('keuangan.*, perkara.JUDUL_perkara, perkara.STATUS_perkara')
            ->from('keuangan')
            ->join('perkara', 'keuangan.NO_perkara = perkara.NO_perkara')
            ->where('perkara.TELP_KLIEN', $telp)
            ->where('keuangan.STATUS_VERIFIKASI_OPS', 'Selesai') // Filter: berkas ber-invoice doang
            ->get()
            ->result();
    }
	
		// Tambahkan ini di dalam class M_keuangan
	public function get_all_pengajuan() {
    $this->db->select('keuangan.*, karyawan.NAMA_STAFF');
    $this->db->from('keuangan');
    $this->db->join('karyawan', 'keuangan.TELP_STAFF = karyawan.TELP_STAFF', 'left');
    $this->db->order_by('keuangan.TGL_PENGAJUAN_OPS', 'DESC');
    
    $query = $this->db->get()->result_array();
    
    foreach ($query as &$row) {
        // Jika STATUS_VERIFIKASI_OPS adalah 'Pending Admin', berarti ini unggahan klien
        if ($row['STATUS_VERIFIKASI_OPS'] == 'Pending Admin') {
            $row['NAMA_STAFF'] = 'Data Klien';
            $row['TELP_STAFF'] = 'Self-Service';
        } 
        // Jika data internal staf tapi nama tidak ditemukan
        elseif (empty($row['NAMA_STAFF'])) {
            $row['NAMA_STAFF'] = 'Staf Tidak Terdata';
        }
    }
    return $query;
}


	public function get_tagihan_aktif() {
		// Fungsi ini mengambil data yang statusnya belum selesai/masih perlu ditagih
		return $this->db->where('STATUS_BAYAR_KLIEN !=', 'Lunas')
						->get('keuangan')
						->result_array();
	}
	public function get_statistik_keuangan() {
    // Menghitung jumlah perkara yang sudah lunas
    $this->db->where('STATUS_BAYAR_KLIEN', 'Lunas');
    $data['total_lunas'] = $this->db->count_all_results('keuangan');

    // Menghitung total nilai pengajuan yang sedang Pending
    $this->db->select_sum('JMLH_PENGAJUAN_OPS');
    $this->db->where('STATUS_VERIFIKASI_OPS', 'Pending');
    $query = $this->db->get('keuangan')->row();
    $data['total_pending_ops'] = $query->JMLH_PENGAJUAN_OPS ?? 0;

    return $data;
	}
}
/* End of file M_keuangan.php */
/* Location: ./application/models/M_keuangan.php */