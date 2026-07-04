<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keuangan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        if (!$this->session->userdata('jabatan') && !$this->session->userdata('klien_logged_in')) {
            redirect('auth');
        }
        $this->load->model('M_keuangan');
    }

   private function _render($view, $data = []) {
        $this->load->view('auth/v_header');
        $this->load->view('v_sidebar');
        $this->load->view($view, $data);
        $this->load->view('auth/v_footer', $data);
    }

    public function index() {
        $data['title'] = 'Dashboard Keuangan';
        $data['jml_pending'] = $this->M_keuangan->count_pengajuan_by_status('Pending');
        $data['jml_disetujui'] = $this->M_keuangan->count_pengajuan_by_status('Disetujui');
        $data['jml_total'] = $this->db->count_all('keuangan');
		$data['stats'] = $this->M_keuangan->get_statistik_keuangan(); // Ambil statistik

        $data['pengajuan'] = $this->db
            ->order_by('TGL_PENGAJUAN_OPS', 'DESC')
            ->limit(10)
            ->get('keuangan')
            ->result();

        $this->_render('dashboard/keuangan/v_index', $data);
    }

    public function pembayaran_klien() {
        $telp = $this->session->userdata('telp_klien');
        if (empty($telp)) { redirect('auth'); }
        
        $data['pembayaran'] = $this->M_keuangan->get_data_pembayaran_klien($telp);
        $data['title'] = 'Pembayaran Perkara';
        
        $this->_render('klien/v_pembayaran_klien', $data);
    }
	
	public function pengajuan_ops() {
    $data['title'] = 'Ajukan Biaya Ops';
    
    // Ambil semua riwayat pengajuan tanpa membatasi hanya yang "Pending Pimpinan"
    $data['riwayat_pengajuan'] = $this->db
        ->select('keuangan.*, perkara.JUDUL_perkara AS JUDUL_PERKARA')
        ->from('keuangan')
        ->join('perkara', 'perkara.NO_PERKARA = keuangan.NO_PERKARA', 'left')
        // Filter berdasarkan nomor telepon staff yang login agar tidak melihat data orang lain
        ->where('keuangan.TELP_STAFF', $this->session->userdata('telp_staff')) 
        ->get()
        ->result_array();

    $this->_render('dashboard/keuangan/v_pengajuan_ops', $data); // Sesuaikan dengan nama view Anda
	}


    public function proses_pengajuan() {
    // 1. Ambil nomor telepon dari akun Kuasa Hukum yang sedang login saat ini
    $telp_login = $this->session->userdata('telp_staff');
    
    // Keamanan presentasi: Jika session kosong/terputus, paksa login ulang agar tidak eror NULL
    if (empty($telp_login)) {
        $this->session->set_flashdata('pesan_error', 'Sesi login habis, silakan masuk kembali.');
        redirect('auth'); // Sesuaikan dengan route url login Anda
    }

    // 2. Ambil data dari form input halaman web
    $no_perkara         = $this->security->xss_clean($this->input->post('no_perkara'));
    $keperluan_dana     = $this->security->xss_clean($this->input->post('keperluan_dana'));
    $jmlh_pengajuan     = $this->security->xss_clean($this->input->post('jmlh_pengajuan'));
    
    // 3. Susun data operasional yang akan dimasukkan ke dalam baris TRX
    $data_update = [
        'KEPERLUAN_DANA_OPS'    => $keperluan_dana,
        'JMLH_PENGAJUAN_OPS'    => $jmlh_pengajuan,
        'TGL_PENGAJUAN_OPS'     => date('Y-m-d H:i:s'),
        'STATUS_VERIFIKASI_OPS' => 'Pending pimpinan', // Mengubah status menjadi menunggu ACC
        'TELP_STAFF'            => $telp_login          // Mengunci data ke nomor telepon pengaju nyata
    ];
    
    // 4. Proses Update ke Database (Mencari data transaksi TRX berdasarkan nomor perkara yang dipilih)
    $this->db->where('NO_PERKARA', $no_perkara);
    $this->db->like('NO_TRANSAKSI', 'TRX-', 'after'); 
    $this->db->update('keuangan', $data_update);
    
    // 5. Beri notifikasi sukses dan kembalikan ke halaman monitor
    $this->session->set_flashdata('pesan_sukses', 'Pengajuan dana operasional berhasil dikirim ke Pimpinan!');
    redirect('dashboard/pengajuan_ops');
	}


    public function klien_upload_berkas() {
        $no_perkara = $this->security->xss_clean($this->input->post('no_perkara'));
        $data = [
            'NO_TRANSAKSI'          => 'TRX-' . date('YmdHis') . '-' . rand(100, 999),
            'NO_PERKARA'            => $no_perkara,
            'STATUS_VERIFIKASI_OPS' => 'Pending Admin', 
            'STATUS_BAYAR_KLIEN'    => 'Belum Bayar',
			'TELP_STAFF'           	=> 'SYSTEM_KLIEN'
        ];
        
        $this->db->insert('keuangan', $data);
        $this->session->set_flashdata('pesan', 'Berkas berhasil diunggah, menunggu verifikasi Admin.');
        redirect('dashboard');
    }

    public function admin_setujui_berkas() {
        if ($this->session->userdata('jabatan') != 'Admin') redirect('dashboard');

        $id = urldecode($this->input->get('id'));
        if (!empty($id)) {
            $this->db->where('NO_TRANSAKSI', $id);
            $this->db->update('keuangan', ['STATUS_VERIFIKASI_OPS' => 'Pending Kuasa Hukum']);
            $this->session->set_flashdata('pesan', 'Berkas diteruskan ke Kuasa Hukum.');
        }
        redirect('dashboard/keuangan/verifikasi');
    }

    public function kuasahukum_setujui_berkas() {
        if ($this->session->userdata('jabatan') != 'Kuasa Hukum') redirect('dashboard');

        $id = urldecode($this->input->get('id'));
        if (!empty($id)) {
            $this->db->where('NO_TRANSAKSI', $id);
            $this->db->update('keuangan', ['STATUS_VERIFIKASI_OPS' => 'Pending Keuangan']);
            $this->session->set_flashdata('pesan', 'Berkas diteruskan ke Keuangan.');
        }
        redirect('dashboard/keuangan/verifikasi');
    }
   
    
    public function klien_kirim_bukti() {
        $no_transaksi = $this->input->post('no_transaksi') ?? $this->input->get('id');

        if (empty($no_transaksi)) {
            $this->session->set_flashdata('pesan_error', 'ID Transaksi tidak ditemukan.');
            redirect('keuangan/pembayaran_klien');
        }

        $config['upload_path']   = FCPATH . 'uploads/pembayaran/';
        $config['allowed_types'] = 'pdf|jpg|jpeg|png';
        $config['max_size']      = 2048;
        $config['encrypt_name']  = TRUE; 

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('bukti_bayar')) {
            $this->session->set_flashdata('pesan_error', $this->upload->display_errors());
        } else {
            $file_data = $this->upload->data();
            $this->db->where('NO_TRANSAKSI', urldecode($no_transaksi));
            $this->db->update('keuangan', [
                'BUKTI_BAYAR_KLIEN'  => $file_data['file_name'],
                'STATUS_BAYAR_KLIEN' => 'Menunggu Verifikasi' 
            ]);
            $this->session->set_flashdata('pesan', 'Bukti transfer berhasil dikirim.');
        }
        redirect('keuangan/pembayaran_klien');
    }
	// --- TAMBAHKAN FUNGSI INI DI BAWAH klien_kirim_bukti ---

    public function data_pengajuan() {
        // Method untuk menu "Data Pengajuan"
        $data['title'] = 'Data Pengajuan Biaya';
        // Pastikan model M_keuangan memiliki fungsi get_all_pengajuan
        $data['pengajuan'] = $this->M_keuangan->get_all_pengajuan(); 
        $this->_render('dashboard/keuangan/v_data_pengajuan', $data);
    }

    public function verifikasi_berkas_klien() {
        // Method untuk menu "Verifikasi Berkas Klien"
        $data['title'] = 'Verifikasi Berkas Klien';
        // Ambil data dari tabel keuangan
        $data['berkas'] = $this->db->get('keuangan')->result_array(); 
        $this->_render('dashboard/keuangan/v_verifikasi_berkas', $data);
    }

    public function pembayaran_invoice_klien() {
        // Method untuk menu "Pembayaran & Invoice"
        $data['title'] = 'Pembayaran & Invoice';
        // Pastikan model M_keuangan memiliki fungsi get_tagihan_aktif
        $data['tagihan'] = $this->M_keuangan->get_tagihan_aktif();
        $this->_render('dashboard/keuangan/v_pembayaran', $data);
    }

    // Pastikan juga fungsi verifikasi (untuk Admin/Kuasa Hukum) sudah ada
    public function verifikasi() {
    $data['title'] = 'Verifikasi Berkas';
    // Gunakan JOIN agar data perkara (nama klien, judul) bisa muncul di tabel verifikasi
    $this->db->select('keuangan.*, perkara.JUDUL_PERKARA, perkara.NAMA_KLIEN');
    $this->db->from('keuangan');
    $this->db->join('perkara', 'perkara.NO_PERKARA = keuangan.NO_PERKARA', 'left');
    $data['berkas'] = $this->db->get()->result_array();
    
    $this->_render('dashboard/keuangan/v_verifikasi', $data);
	}
	public function ajax_detail($no_transaksi) {
    // Ambil data dari database
    $data['item'] = $this->db->get_where('keuangan', ['NO_TRANSAKSI' => $no_transaksi])->row_array();
    
    // Pengecekan: Jika data tidak ditemukan
    if (empty($data['item'])) {
        echo '<div class="alert alert-danger">Data tidak ditemukan.</div>';
        return;
    }
    
    // Tampilkan hanya HTML-nya saja
	$this->output->set_content_type('text/html');
    $this->load->view('dashboard/keuangan/v_detail_ajax', $data);
	}
	
	public function edit_tagihan($no_transaksi = null) {
    if (empty($no_transaksi)) {
        echo "Error: Parameter ID kosong!"; exit();
    }

    $id = urldecode($no_transaksi);
    $data['tagihan'] = $this->db->get_where('keuangan', ['NO_TRANSAKSI' => $id])->row_array();

    // Debugging: Cek isi data
    if (empty($data['tagihan'])) {
        echo "Error: Data dengan ID " . $id . " tidak ditemukan di database tabel 'keuangan'."; 
        exit();
    }

    $data['title'] = 'Edit Tagihan';
    $this->_render('dashboard/keuangan/v_edit_tagihan', $data);
	}
	
	public function proses_edit_tagihan($no_transaksi) {
    if ($this->session->userdata('jabatan') != 'Keuangan') redirect('dashboard');

    $data_update = [
        'NO_INVOICE'        => $this->security->xss_clean($this->input->post('no_invoice')),
        'TTL_TAGIHAN_KLIEN' => $this->security->xss_clean($this->input->post('ttl_tagihan'))
    ];

    $this->db->where('NO_TRANSAKSI', urldecode($no_transaksi));
    $this->db->update('keuangan', $data_update);

    $this->session->set_flashdata('pesan', 'Tagihan berhasil diperbarui.');
    redirect('dashboard/keuangan/pembayaran');
	}
	
	public function proses_upload_ttd() {
        $no_transaksi = $this->input->post('NO_TRANSAKSI');
        $item = $this->db->get_where('keuangan', ['NO_TRANSAKSI' => $no_transaksi])->row_array();

        $config['upload_path']   = './uploads/perkara/';
        $config['allowed_types'] = 'pdf';
        $config['encrypt_name']  = TRUE;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('berkas_ttd')) {
            $upload_data = $this->upload->data();
            if (!empty($item['BERKAS_TTD_KH']) && file_exists('./uploads/perkara/'.$item['BERKAS_TTD_KH'])) {
                unlink('./uploads/perkara/'.$item['BERKAS_TTD_KH']);
            }

            $this->db->where('NO_TRANSAKSI', $no_transaksi);
            $this->db->update('keuangan', [
                'BERKAS_TTD_KH'         => $upload_data['file_name'],
                'STATUS_VERIFIKASI_OPS' => 'Pending Keuangan',
                'TTD_KUASA_HUKUM'       => 'TERTANDA_SISTEM_KH'
            ]);

            $this->session->set_flashdata('pesan', 'Berkas bertanda tangan berhasil diunggah.');
        } else {
            $this->session->set_flashdata('pesan', 'Gagal: ' . $this->upload->display_errors());
        }
        redirect('dashboard/keuangan/verifikasi');
    }
	
	public function simpan_nota_keluar() {
    // Tangkap data inputan dari form ketik di dashboard
    $no_transaksi = $this->input->post('no_transaksi');
    $bukti_nota   = $this->security->xss_clean($this->input->post('bukti_nota'));

    // Susun pembaruan kolom nota dan status validasi final
    $data_update = [
        'BUKTI_NOTA_KAS_KELUAR' => $bukti_nota,
        'STATUS_VERIFIKASI_OPS' => 'Validasi Selesai' 
    ];

    $this->db->where('NO_TRANSAKSI', $no_transaksi);
    $this->db->update('keuangan', $data_update);

    $this->session->set_flashdata('pesan_sukses', 'Nomor bukti nota kas keluar sukses disimpan, pencairan dana rampung!');
    redirect('dashboard'); // Lempar balik halaman ke dashboard utama keuangan
	}

}