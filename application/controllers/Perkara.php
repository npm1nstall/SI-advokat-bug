<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class perkara extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // GATEKEEPER: Cek login dulu. Staff pake 'jabatan', Klien pake 'klien_logged_in'
        if (!$this->session->userdata('jabatan') && !$this->session->userdata('klien_logged_in')) {
            redirect('auth');
        }
        $this->load->model('M_perkara'); 
    }

    // HELPER: Render view + header + sidebar + footer biar rapi
    private function _render($view, $data = []) {
        $this->load->view('auth/v_header');
        $this->load->view('v_sidebar', $data); // $data dilempar biar notif sidebar kebaca
        $this->load->view($view, $data); 
        $this->load->view('auth/v_footer', $data); 
    }

    // HALAMAN UTAMA: List semua perkara sesuai role
   public function index()
	{
    $data['title'] = 'Data Perkara';
    $jabatan = $this->session->userdata('jabatan');

    // LOGIKA KLIEN
    if ($this->session->userdata('klien_logged_in')) {
        $telp = $this->session->userdata('telp_klien');
        $this->db->where('TELP_KLIEN', $telp);
        
        // Filter "Pendaftaran" hanya jika sudah punya kasus asli
        $punya_asli = $this->db->not_like('JUDUL_PERKARA', 'Pendaftaran')->count_all_results('perkara', false);
        if ($punya_asli > 0) {
            $this->db->not_like('JUDUL_PERKARA', 'Pendaftaran');
        }

        $data['perkara'] = $this->db->order_by('TGL_MASUK', 'DESC')->get()->result_array();
        $this->_render('perkara/v_daftar', $data);
        return;
    }

    // LOGIKA STAFF INTERNAL (ADMIN & LAINNYA)
    if ($jabatan == 'Admin') {
        // Panggil fungsi monitoring agar semua data muncul
        $data['perkara'] = $this->M_perkara->get_semua_perkara_aktif();
    } 
    else // Jika user yang login adalah Kuasa Hukum (punya $telp_staff)
		if ($this->session->userdata('jabatan') == 'Kuasa Hukum') {
   
	// 1. Ambil data nama staf dari session
	$nama_aktif = $this->session->userdata('nama_staf');

	// 2. Ambil data telp_staff dari database berdasarkan nama staf tersebut
	$stf = $this->db->get_where('karyawan', ['NAMA_STAFF' => $nama_aktif])->row_array();
	$telp_staff = $stf['TELP_STAFF'] ?? 'KOSONG'; // Ini membuat variabel $telp_staff ada

	// 3. Sekarang baru gunakan $telp_staff untuk memanggil model
	$data['perkara'] = $this->M_perkara->get_semua_perkara_kuasa_hukum($telp_staff);
	}
	// Jika user yang login adalah Admin (tidak butuh telp_staff)
	else {
    // Gunakan fungsi untuk mengambil semua perkara
    $data['perkara'] = $this->M_perkara->get_semua_perkara_aktif();
	}

    $this->_render('perkara/v_daftar', $data);
	}

    // SIMPAN perkara BARU DARI FORM KLIEN
    public function simpan()
	{
		// 1. CEK SESI DI AWAL (Jangan simpan apa-apa dulu kalau user tidak login)
		$telp_klien = $this->session->userdata('telp_klien');
		$nama_klien = $this->session->userdata('nama_klien');

		if (empty($telp_klien) || empty($nama_klien)) {
			$this->session->set_flashdata('pesan_error', 'Sesi Anda habis, silakan login kembali.');
			redirect('auth');
			return; // Tambahkan return agar kode di bawah tidak tereksekusi
		}

		// 2. LOAD & PROSES UPLOAD
		$config['upload_path']   = FCPATH . 'uploads/perkara/';
		$config['allowed_types'] = 'pdf|jpg|jpeg|png|doc|docx';
		$config['max_size']      = 2048;
		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('berkas_perkara')) {
			echo $this->upload->display_errors();
			die();
		} 
		
		// 3. PROSES DATA JIKA UPLOAD BERHASIL
		$file_data = $this->upload->data();
		$NO_PERKARA = $this->input->post('NO_PERKARA') ?: 'PRK-' . date('Ymd') . '-' . rand(100,999);

		// Ambil alamat
		$data_pendaftaran = $this->db->get_where('perkara', [
			'TELP_KLIEN' => $telp_klien,
			'JUDUL_perkara' => 'Pendaftaran Akun Baru'
		])->row_array();
		
		$alamat_klien = $data_pendaftaran['ALAMAT_KLIEN'] ?? '-';

		// 4. INSERT KE DATABASE (Gunakan Transaction agar lebih aman)
		$this->db->trans_start(); // Memulai transaksi database

		$data_perkara = [
			'NO_PERKARA'     => $NO_PERKARA,
			'JUDUL_perkara'  => $this->input->post('judul'),
			'NAMA_KLIEN'     => $nama_klien,
			'TELP_KLIEN'     => $telp_klien,
			'ALAMAT_KLIEN'   => $alamat_klien,
			'BERKAS_perkara' => $file_data['file_name'],
			'TGL_MASUK'      => date('Y-m-d H:i:s'),
			'STATUS_perkara' => 'Baru'
		];
		$this->db->insert('perkara', $data_perkara);

		$data_alur_ops = [
			'NO_TRANSAKSI'          => 'TRX-' . date('YmdHis') . '-' . rand(100, 999),
			'NO_PERKARA'            => $NO_PERKARA,
			'STATUS_VERIFIKASI_OPS' => 'Pending Admin',
			'STATUS_BAYAR_KLIEN'    => 'Belum Bayar'
		];
		$this->db->insert('keuangan', $data_alur_ops);

		$this->db->trans_complete(); // Selesai & otomatis rollback jika terjadi error

		$this->session->set_flashdata('pesan', 'Data perkara berhasil disimpan & berkas dikirim ke Admin!');
		redirect('perkara');
	}

    // UPDATE BIODATA KLIEN: Dipake pas edit nama/telp/alamat doang
    public function update_biodata()
    {
        $NO_PERKARA = $this->input->post('NO_PERKARA');

        $data_update = [
            'JUDUL_perkara' => $this->input->post('judul_perkara'),
            'NAMA_KLIEN'    => $this->input->post('nama_klien'),
            'TELP_KLIEN'    => $this->input->post('telp_klien'),
            'ALAMAT_KLIEN'  => $this->input->post('alamat_klien')
        ];

        $this->db->where('NO_PERKARA', $NO_PERKARA);
        $this->db->update('perkara', $data_update);

        $this->session->set_flashdata('sukses', 'Biodata perkara berhasil diubah!');
        redirect('perkara');
    }

    // HAPUS perkara: Hapus dari keuangan dulu biar ga kena foreign key
    public function hapus($NO_PERKARA) 
	{
		$id = urldecode($NO_PERKARA);
		
		// 1. Ambil nama file sebelum dihapus
		$row = $this->db->get_where('perkara', ['NO_PERKARA' => $id])->row_array();
		
		// 2. Hapus file fisik jika ada
		if (!empty($row['BERKAS_perkara']) && file_exists(FCPATH . 'uploads/perkara/' . $row['BERKAS_perkara'])) {
			unlink(FCPATH . 'uploads/perkara/' . $row['BERKAS_perkara']);
		}

		// 3. Hapus database
		$this->db->where('NO_PERKARA', $id);
		$this->db->delete('keuangan');
		$this->db->where('NO_PERKARA', $id);
		$this->db->delete('perkara');
		
		$this->session->set_flashdata('pesan', 'Data dan file berhasil dihapus!');
		redirect('perkara');
	}
    
    // CETAK PDF: Generate laporan perkara ke PDF pake library TCPDF
    public function cetak($NO_PERKARA) {
        $this->load->library('pdf');
        $id = urldecode($NO_PERKARA);
        $data['p'] = $this->db->get_where('perkara', ['NO_PERKARA' => $id])->row_array();
        
        if (!$data['p']) {
            $this->session->set_flashdata('pesan', 'Data tidak ditemukan!');
            redirect('perkara');
        }

        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle('Laporan perkara - ' . $id);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        
        $html = '
        <h2 style="text-align:center;">LAPORAN DATA perkara</h2>
        <table border="1" cellpadding="5">
            <tr><th width="30%">No perkara</th><td>'.$data['p']['NO_PERKARA'].'</td></tr>
            <tr><th>Judul perkara</th><td>'.$data['p']['JUDUL_perkara'].'</td></tr>
            <tr><th>Tanggal Masuk</th><td>'.date('d-m-Y H:i', strtotime($data['p']['TGL_MASUK'])).'</td></tr>
            <tr><th>Berkas perkara</th><td>'.($data['p']['BERKAS_perkara'] ?? '-').'</td></tr>
            <tr><th>Status perkara</th><td>'.($data['p']['STATUS_perkara'] ?? '-').'</td></tr>
            <tr><th>Nama Klien</th><td>'.($data['p']['NAMA_KLIEN']).'</td></tr>
            <tr><th>Telp Klien</th><td>'.$data['p']['TELP_KLIEN'].'</td></tr>
            <tr><th>Alamat Klien</th><td>'.$data['p']['ALAMAT_KLIEN'].'</td></tr>
            <tr><th>Agenda Sidang</th><td>'.($data['p']['AGENDA_SIDANG'] ?? '-').'</td></tr>
        </table>';
        
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('perkara_'.$id.'.pdf', 'I');
    }
    
    // HALAMAN JADWAL SIDANG KHUS KLIEN: Filter + kasih penanda status_halaman
   public function jadwal_sidang() 
	{
		if ($this->session->userdata('klien_logged_in')) {
			$telp = $this->session->userdata('telp_klien'); 
			
			// HATI-HATI: Ambil semua perkara klien, tapi skip "Pendaftaran Akun Baru"
			$this->db->where('TELP_KLIEN', $telp);
			$this->db->not_like('JUDUL_perkara', 'Pendaftaran');
			$this->db->order_by('TGL_MASUK', 'DESC');
			
			$data['perkara'] = $this->db->get('perkara')->result_array(); // WAJIB result_array biar bisa foreach
			
			$data['title'] = 'Jadwal Sidang';
			
			// HATI-HATI: Load view dari folder klien, bukan perkara
			$this->_render('klien/v_jadwal', $data); // INI YANG DIGANTI
			
		} else {
			redirect('auth');
		}
}

    // FORM PROSES SIDANG: Halaman khusus Admin/KH buat input hasil sidang
    public function proses_sidang($NO_PERKARA)
    {
        $data['title'] = 'Proses Data Persidangan';
        $data['perkara'] = $this->db->get_where('perkara', ['NO_PERKARA' => urldecode($NO_PERKARA)])->row_array();
        $this->_render('perkara/v_proses_sidang', $data);
    }

    // SIMPAN HASIL PROSES SIDANG: Update tgl sidang, agenda, disposisi, hasil, + upload berkas baru
    public function simpan_proses_sidang()
    {
        $NO_PERKARA = $this->input->post('NO_PERKARA');

        $tgl_penugasan_raw = $this->input->post('tgl_penugasan');
        $tgl_sidang_raw    = $this->input->post('tgl_sidang');

        // TRIK: datetime-local format "2026-06-22T08:00" harus ganti T jadi spasi biar MySQL mau
        $tgl_penugasan = !empty($tgl_penugasan_raw) ? date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $tgl_penugasan_raw))) : NULL;
        $tgl_sidang    = !empty($tgl_sidang_raw) ? date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $tgl_sidang_raw))) : NULL;

        // Ambil file lama dulu, kalo ga upload baru pake yg lama
        $perkara_lama = $this->db->get_where('perkara', ['NO_PERKARA' => $NO_PERKARA])->row_array();
        $nama_file = $perkara_lama['BERKAS_perkara']; 

        // Upload berkas sidang baru kalo ada
        if (!empty($_FILES['berkas_baru']['name'])) {
            $config['upload_path']   = FCPATH . 'uploads/perkara/';
            $config['allowed_types'] = 'pdf|jpg|jpeg|png|doc|docx';
            $config['max_size']      = 5120;
            $config['encrypt_name']  = TRUE;
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('berkas_baru')) {
                $upload_data = $this->upload->data();
                $nama_file = $upload_data['file_name'];
            }
        }

        // UPDATE SEMUA FIELD PERSIDANGAN
        $data_update = [
            'BERKAS_perkara'    => $nama_file,
            'TGL_PENUGASAN_TIM' => $tgl_penugasan, 
            'TGL_SIDANG'        => $tgl_sidang,    
            'CATATAN_DISPOSISI' => $this->input->post('catatan_disposisi'),
            'AGENDA_SIDANG'     => $this->input->post('agenda_sidang'),
            'HASIL_SIDANG'      => $this->input->post('hasil_sidang')
        ];

        $this->db->where('NO_PERKARA', $NO_PERKARA);
        $this->db->update('perkara', $data_update);

        redirect('perkara');
    }

    // TAMBAH perkara INTERNAL: Dipake Staff/Admin input manual tanpa lewat klien
    public function tambah_perkara_internal()
    {
        $config['upload_path']   = FCPATH . 'uploads/perkara/';
        $config['allowed_types'] = 'pdf|jpg|jpeg|png|doc|docx';
        $config['max_size']      = 2048;
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('berkas_perkara')) {
            echo $this->upload->display_errors();
            die();
        } else {
            date_default_timezone_set('Asia/Jakarta');

            $file_data = $this->upload->data();
            $NO_PERKARA = $this->input->post('NO_PERKARA');
            $telp_staff = $this->session->userdata('telp');

            $data_perkara = [
                'NO_PERKARA'     => $NO_PERKARA,
                'TELP_STAFF'     => $telp_staff,
                'JUDUL_perkara'  => $this->input->post('judul'),
                'TGL_MASUK'      => date('Y-m-d H:i:s'), // Lock jam masuk real-time
                'BERKAS_perkara' => $file_data['file_name'],
                'STATUS_perkara' => 'Baru',
                'NAMA_KLIEN'     => $this->input->post('nama_klien'),
                'TELP_KLIEN'     => $this->input->post('telp_klien'),
                'ALAMAT_KLIEN'   => $this->input->post('alamat_klien')
            ];
            $this->db->insert('perkara', $data_perkara);

            // Bikin tracking keuangan juga
            $data_alur_keuangan = [
                'NO_TRANSAKSI'          => 'TRX-' . date('YmdHis') . '-' . rand(100, 999),
                'NO_PERKARA'            => $NO_PERKARA,
                'STATUS_VERIFIKASI_OPS' => 'Pending Admin',
                'STATUS_BAYAR_KLIEN'    => 'Belum Bayar'
            ];
            $this->db->insert('keuangan', $data_alur_keuangan);

            $this->session->set_flashdata('pesan', 'perkara baru berhasil didaftarkan!');
            redirect('perkara');
        }
    }
    
    // UPDATE CEPAT DARI MODAL EDIT: Dipake tombol Edit di card. Handle semua field + convert datetime
    public function update($NO_PERKARA)
    {
        // SECURITY: Cek admin/staff login
        if(!$this->session->userdata('admin_logged_in') && !$this->session->userdata('jabatan')) redirect('auth');
            
        // CONVERT: datetime-local "2026-06-22T18:30" → MySQL "2026-06-22 18:30:00"
        $tgl_penugasan_raw = $this->input->post('TGL_PENUGASAN_TIM');
        $tgl_sidang_raw = $this->input->post('TGL_SIDANG');
            
        $tgl_penugasan = !empty($tgl_penugasan_raw) ? date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $tgl_penugasan_raw))) : NULL;
        $tgl_sidang = !empty($tgl_sidang_raw) ? date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $tgl_sidang_raw))) : NULL;

        $data = [
            'JUDUL_perkara'     => $this->input->post('JUDUL_perkara'),
            'STATUS_perkara'    => $this->input->post('STATUS_perkara'),
            'TGL_PENUGASAN_TIM' => $tgl_penugasan,
            'TGL_SIDANG'        => $tgl_sidang,
            'AGENDA_SIDANG'     => $this->input->post('AGENDA_SIDANG'),
            'CATATAN_DISPOSISI' => $this->input->post('CATATAN_DISPOSISI'),
            'HASIL_SIDANG'      => $this->input->post('HASIL_SIDANG')
        ];
            
        $this->db->where('NO_PERKARA', $NO_PERKARA);
        $this->db->update('perkara', $data);
            
        $this->session->set_flashdata('pesan', 'Data perkara berhasil diupdate');
        redirect('perkara');
    }
}