<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ==============================================
 * CONTROLLER: Auth 
 * Fungsi: Login & Logout Klien + Staff
 * Author: [Nama Kamu]
 * ==============================================
 */
class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session'); // Load session library
        $this->load->database();         // Load database buat query
    }

    /**
     * Halaman Login
     * URL: /auth
     */
    public function index() {
        // Kalo udah login, ga usah ke halaman login lagi
        if ($this->session->userdata('klien_logged_in') || $this->session->userdata('admin_logged_in')) {
            redirect('dashboard'); 
            return;
        }

        // Load template login
        $this->load->view('auth/v_header');
        $this->load->view('auth/v_login'); 
        $this->load->view('auth/v_footer');
    }

    /**
     * Proses Login
     * Logic: Password kosong = Klien, Ada password = Staff
     * URL: /auth/proses_login
     */
    public function proses_login(){
        $username_input = trim($this->input->post('telp_klien'));
        $password_input = $this->input->post('password');

        // 1. Validasi input wajib diisi
        if (empty($username_input)) {
            $this->session->set_flashdata('error', 'Username/Nomor Telepon wajib diisi!');
            redirect('auth');
            return;
        }

        // 2. CEK LOGIN KLIEN - kalo password kosong
        if (empty($password_input)) {
            $cek_klien = $this->db->get_where('perkara', ['TELP_KLIEN' => $username_input])->row_array();

            if ($cek_klien) {
                // Simpan data klien ke session
                $this->session->set_userdata([
                    'nama_klien'      => $cek_klien['NAMA_KLIEN'],
                    'telp_klien'      => $cek_klien['TELP_KLIEN'],
                    'klien_logged_in' => TRUE
                ]);
                redirect('dashboard');
                return; // PENTING: stop biar ga lanjut ke cek staff

            } else {
                $this->session->set_flashdata('error', 'Klien tidak ditemukan!');
                redirect('auth');
                return;
            }
        }

               // 3. CEK LOGIN STAF - kalo ada password
        $this->db->where('TELP_STAFF', $username_input);
        $this->db->or_where('NAMA_STAFF', $username_input);
        $cek_staf = $this->db->get('karyawan')->row_array();

        if ($cek_staf) {
            // Cek password MD5
            if (md5($password_input) === $cek_staf['PASS_STAFF']) {
                
                // PERBAIKAN DI SINI: Simpan data ke session HANYA jika password cocok
                $this->session->set_userdata([
                    'nama_staf'       => $cek_staf['NAMA_STAFF'],
                    'telp_staff'      => $cek_staf['TELP_STAFF'], // Merekam data nomor telepon unik pengaju
                    'jabatan'         => ucwords(strtolower($cek_staf['JABATAN_STAFF'])), // Hasilnya: "Kuasa Hukum" / "Keuangan"
                    'admin_logged_in' => TRUE
                ]);

                redirect('dashboard');
                return;

            } else {
                $this->session->set_flashdata('error', 'Password salah!');
                redirect('auth');
                return;
            }

        } else {
            $this->session->set_flashdata('error', 'User tidak terdaftar!');
            redirect('auth');
            return;
        }
    }


    /**
     * Logout
     * Fungsi: Hapus semua session + balik ke login
     * URL: /auth/logout
     */
    public function logout() {
        // Hancurkan semua data session
        $this->session->sess_destroy();
        
        // Redirect ke halaman login
        redirect('auth');
    }
}
/* End of file Auth.php */
/* Location: ./application/controllers/Auth.php */