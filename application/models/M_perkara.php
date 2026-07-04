<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_perkara extends CI_Model {

    // 1. Digunakan untuk halaman Utama (Dashboard)
    public function get_semua_perkara_kuasa_hukum($telp_staff) {
        return $this->db
            ->select('perkara.*, keuangan.NO_TRANSAKSI, keuangan.STATUS_VERIFIKASI_OPS')
            ->from('perkara')
            ->join('keuangan', 'perkara.NO_PERKARA = keuangan.NO_PERKARA', 'left')
            ->where('perkara.TELP_STAFF', $telp_staff)
            ->order_by('perkara.TGL_MASUK', 'DESC')
            ->get()
            ->result_array();
    }

	// Pastikan di M_perkara.php

	// Di dalam file M_perkara.php
public function get_antrean_verifikasi_kuasa($telp_staff) {
    return $this->db->select('perkara.*, keuangan.STATUS_VERIFIKASI_OPS')
        ->from('perkara')
        ->join('keuangan', 'perkara.NO_PERKARA = keuangan.NO_PERKARA', 'inner')
        ->where('perkara.TELP_STAFF', $telp_staff)
        // Kunci utamanya: sesuaikan status dengan yang ada di database
        ->where('keuangan.STATUS_VERIFIKASI_OPS', 'Validasi Selesai') 
        ->get()->result_array();
}

    // 3. Fungsi Admin
    public function get_antrean_admin() {
        return $this->db
            ->select('perkara.*, keuangan.NO_TRANSAKSI, keuangan.STATUS_VERIFIKASI_OPS')
            ->from('perkara')
            ->join('keuangan', 'perkara.NO_PERKARA = keuangan.NO_PERKARA', 'left')
            ->where('keuangan.STATUS_VERIFIKASI_OPS', 'Pending Admin')
            ->order_by('TGL_MASUK', 'DESC')
            ->get()
            ->result_array();
    }
	
	// Tambahkan fungsi ini ke dalam class M_perkara
	public function get_semua_perkara_aktif() {
    return $this->db
        ->select('perkara.*, keuangan.STATUS_VERIFIKASI_OPS, keuangan.STATUS_BAYAR_KLIEN')
        ->from('perkara')
        ->join('keuangan', 'perkara.NO_PERKARA = keuangan.NO_PERKARA', 'left')
        ->order_by('TGL_MASUK', 'DESC')
        ->get()
        ->result_array();
	}
	    /**
     * Mengambil daftar antrean perkara yang siap diproses oleh divisi Keuangan
     * Filter: STATUS_VERIFIKASI_OPS = 'Pending keuangan'
     */
    public function get_antrean_keuangan()
    {
        $this->db->select('keuangan.*, perkara.NO_PERKARA, perkara.JUDUL_perkara, perkara.NAMA_KLIEN');
        $this->db->from('keuangan');
        $this->db->join('perkara', 'perkara.NO_PERKARA = keuangan.NO_PERKARA', 'left');
        
        // Menggunakan group_start demi keamanan pengecekan variasi huruf kapital di DB
        $this->db->group_start();
        $this->db->where('keuangan.STATUS_VERIFIKASI_OPS', 'Pending keuangan');
        $this->db->or_where('keuangan.STATUS_VERIFIKASI_OPS', 'Pending Keuangan');
        $this->db->group_end();
        
        $this->db->order_by('keuangan.NO_TRANSAKSI', 'DESC');
        return $this->db->get()->result_array();
    }
}