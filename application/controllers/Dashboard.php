<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ==============================================
 * CONTROLLER: Dashboard 
 * Fungsi: Halaman utama beda role + CRUD Staff + keuangan + Pimpinan
 * Role: Klien, Admin, keuangan, Kuasa Hukum, Pimpinan
 * Author: [Nama Kamu]
 * ==============================================
 */
class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // CEK LOGIN: Kalo belum login, tendang ke auth
        if (!$this->session->userdata('jabatan') && !$this->session->userdata('klien_logged_in')) {
            redirect('auth');
            return;
        }
        
        // Load model keuangan, dipake di semua fungsi
        $this->load->model('M_keuangan');
    }

    /**
     * Helper render template
     * Fungsi: Biar ga nulis load view header+sidebar+footer berulang
     */
    private function _render($view, $data = []) {
        $this->load->view('auth/v_header');
        $this->load->view('v_sidebar', $data);
        $this->load->view($view, $data);
        $this->load->view('auth/v_footer');
    }

    /**
     * Dashboard Utama - Auto detect role
     * URL: /dashboard
     */
    public function index() 
    {
        // === 1. JALUR KLIEN ===
        if ($this->session->userdata('klien_logged_in')) {
            $telp = $this->session->userdata('telp_klien');

            // Ambil data perkara aktif milik klien ini
            $data['perkara'] = $this->db->get_where('perkara', ['TELP_KLIEN' => $telp])->row_array();

            // Hitung notif tagihan belum bayar
            $data['notif_bayar'] = $this->M_keuangan->count_tagihan_belum_bayar($telp);
            
            // Ambil status bayar terakhir klien dari tabel keuangan jika perkara ada
            if ($data['perkara']) {
                $keuangan = $this->db->select('STATUS_BAYAR_KLIEN')
                    ->where('NO_PERKARA', $data['perkara']['NO_PERKARA'])
                    ->order_by('NO_TRANSAKSI', 'DESC')
                    ->limit(1)
                    ->get('keuangan')
                    ->row_array();

                $data['perkara']['STATUS_BAYAR_KLIEN'] = $keuangan['STATUS_BAYAR_KLIEN'] ?? 'Belum ada data';
            }

            // Kalo data perkara kosong, kasih default biar view ga error
            if (!$data['perkara']) {
                $data['perkara'] = [
                    'NO_PERKARA'          => '-',
                    'JUDUL_perkara'       => 'Belum ada perkara aktif',
                    'AGENDA_SIDANG'       => null,
                    'STATUS_perkara'      => '-',
                    'STATUS_BAYAR_KLIEN'  => 'Belum ada data',
                    'CATATAN_DISPOSISI'   => null
                ];
            }

            $this->_render('klien/v_dashboard', $data);
            return;
        }

        // === 2. JALUR STAFF INTERNAL ===
        $jabatan = $this->session->userdata('jabatan');
        $data['title'] = 'Dashboard';
        
        $this->load->model('M_perkara');

        switch ($jabatan) {
            case 'Admin':
                $data['jml_perkara'] = $this->db->count_all('perkara');
                $data['jml_surat']   = $this->db->count_all('surat');
                $data['jml_staff']   = $this->db->count_all('karyawan');
                $data['jml_sidang']  = $this->db->where('TGL_SIDANG >=', date('Y-m-d H:i:s'))->count_all_results('perkara');
                $data['pengajuan']   = $this->M_perkara->get_antrean_admin();
                
                $this->_render('dashboard/admin/v_index', $data);
                break;

            case 'Keuangan':
                $data['jml_pending']   = $this->db->where('STATUS_VERIFIKASI_OPS', 'Pending Pimpinan')->count_all_results('keuangan');
                $data['jml_disetujui'] = $this->db->where('STATUS_VERIFIKASI_OPS', 'Pending keuangan')->count_all_results('keuangan');
                $data['jml_total']     = $this->db->count_all('keuangan');
                
                // KITA MATIKAN FILTER LIKE SEMENTARA BIAR DATA PASTI KELUAR
                $data['pengajuan'] = $this->db
                    ->select('
                        keuangan.NO_TRANSAKSI, 
                        keuangan.NO_PERKARA, 
                        keuangan.JMLH_PENGAJUAN_OPS, 
                        keuangan.STATUS_VERIFIKASI_OPS, 
                        perkara.JUDUL_perkara AS JUDUL_PERKARA
                    ')
                    ->from('keuangan')
                    ->join('perkara', 'perkara.NO_PERKARA = keuangan.NO_PERKARA', 'left')
                    ->group_start()
                        ->where('keuangan.STATUS_VERIFIKASI_OPS', 'Pending keuangan')
                        ->or_where('keuangan.STATUS_VERIFIKASI_OPS', 'Pending Keuangan')
                        ->or_where('keuangan.STATUS_VERIFIKASI_OPS', 'pending keuangan')
                    ->group_end() // <--- Sambungan query di bawah ini dijamin aman:
                    ->get()
                    ->result_array(); 
                    
                $this->_render('dashboard/keuangan/v_index', $data);
                break;


            case 'Pimpinan':
                redirect('dashboard/pimpinan');
                break;

            case 'Kuasa Hukum':
                // Realtime get data nomor telepon staff dari session
                $nama_aktif = $this->session->userdata('nama_staf');
                $stf = $this->db->get_where('karyawan', ['NAMA_STAFF' => $nama_aktif])->row_array();
                $telp_staff = $stf['TELP_STAFF'] ?? 'KOSONG';

                // Hitung perkara aktif & jadwal sidang
                $data['jml_perkara'] = $this->db->where('TELP_STAFF', $telp_staff)->count_all_results('perkara');
                $data['jml_sidang']  = $this->db->where(['TELP_STAFF' => $telp_staff, 'TGL_SIDANG >=' => date('Y-m-d H:i:s')])->count_all_results('perkara');

                // Ambil daftar antrean verifikasi untuk Kuasa Hukum
                $data['pengajuan'] = $this->M_perkara->get_antrean_verifikasi_kuasa($telp_staff);
                
                $this->_render('dashboard/kuasa_hukum/v_index', $data);
                break;

            default:
                show_404();
                break;
        }
    }

    // ================= MODUL SDM - ADMIN ONLY =================

    /**
     * Simpan data staff/klien baru
     * Logic: Klien ga pake password, Staff wajib password MD5
     */
    public function simpan_staff() {
        if ($this->session->userdata('jabatan') != 'Admin') redirect('dashboard');

        $role = $this->input->post('role');
        $telp = $this->input->post('telp');
        $nama = $this->input->post('nama');

        // Validasi wajib
        if (empty($role) || empty($telp) || empty($nama)) {
            $this->session->set_flashdata('pesan_error', 'Semua kolom wajib diisi!');
            redirect('dashboard/tambah_staff');
            return;
        }

        // Kalo role Klien
        if ($role == 'Klien') {
            $NO_PERKARA = 'REG-' . date('YmdHis') . '-' . rand(10, 99);

            $data_perkara = [
                'NO_PERKARA'     => $NO_PERKARA,
                'NAMA_KLIEN'     => $nama,
                'TELP_KLIEN'     => $telp,
                'ALAMAT_KLIEN'   => $this->input->post('alamat'),
                'JUDUL_perkara'  => 'Pendaftaran Akun Baru',
                'TGL_MASUK'      => date('Y-m-d H:i:s'),
                'STATUS_perkara' => 'Baru'
            ];
            $this->db->insert('perkara', $data_perkara);

            // Auto bikin data keuangan kosong
            $data_keuangan = [
                'NO_TRANSAKSI'          => 'TRX-' . date('YmdHis') . '-' . rand(100, 999),
                'NO_PERKARA'            => $NO_PERKARA,
                'STATUS_VERIFIKASI_OPS' => 'Pending Admin',
                'STATUS_BAYAR_KLIEN'    => 'Belum Bayar'
            ];
            $this->db->insert('keuangan', $data_keuangan);
            
        } else {
            // Kalo role Staff - wajib password
            $password_input = $this->input->post('password');
            if (empty($password_input)) {
                $this->session->set_flashdata('pesan_error', 'Password wajib diisi untuk staf internal!');
                redirect('dashboard/tambah_staff');
                return;
            }

            $data_karyawan = [
                'NAMA_STAFF'    => $nama,
                'TELP_STAFF'    => $telp,
                'JABATAN_STAFF' => $role, 
                'PASS_STAFF'    => md5($password_input) // MD5 buat demo
            ];
            $this->db->insert('karyawan', $data_karyawan);
        }

        $this->session->set_flashdata('pesan', 'Akun baru role ' . $role . ' berhasil dibuat!');
        redirect('dashboard/staff');
    }

    /**
     * Tampilkan form tambah staff
     */
    public function tambah_staff() {
        if ($this->session->userdata('jabatan') != 'Admin') redirect('dashboard');
        $data['title'] = 'Tambah Akun Pengguna';
        $this->_render('dashboard/admin/v_tambah_staff', $data);
    }

    /**
     * Tampilkan data semua staff / karyawan
     */
    public function staff() {
        if ($this->session->userdata('jabatan') != 'Admin') redirect('dashboard');
        
        $data['title'] = 'Daftar Staff / Karyawan';
        $data['staff'] = $this->db->get('karyawan')->result_array();
        
        $this->_render('dashboard/admin/v_staff', $data);
    }
	


        // ================= MODUL SURAT - ADMIN ONLY =================

    /**
     * Manajemen surat - Admin only
     */
    public function surat()
    {
        // 1. Ambil data dari tabel surat asli milik Anda
        $data['daftar_surat'] = $this->db->order_by('TGL_REGISTRASI', 'DESC')->get('surat')->result_array();
        
        // 2. Ambil list staf Kuasa Hukum / Advokat saja untuk dropdown
        $this->db->group_start();
        $this->db->where('JABATAN_STAFF', 'Kuasa Hukum');
        $this->db->or_where('JABATAN_STAFF', 'Advokat');
        $this->db->group_end();
        $data['karyawan'] = $this->db->get('karyawan')->result_array();
        
        // 3. Ambil data perkara aktif untuk dropdown
        $data['daftar_perkara'] = $this->db->order_by('TGL_MASUK', 'DESC')->get('perkara')->result_array();
        $data['jabatan'] = $this->session->userdata('jabatan');

        // 4. Render ke view admin Anda
        $this->_render('dashboard/admin/v_manajemen_surat', $data);
    }

    /**
     * Proses unggah berkas surat dan disposisi perkara
     */
    public function upload_surat()
    {
        $config['upload_path']   = './assets/uploads/surat/';
        $config['allowed_types'] = 'pdf|doc|docx|jpg|png';
        $config['max_size']      = 5000;
        $config['encrypt_name']  = TRUE;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_surat')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            redirect('dashboard/surat');
        } else {
            $file_data = $this->upload->data();
            
            $nama_admin = $this->session->userdata('nama_staf') ?? 'Pimpinan';
            $no_perkara = $this->input->post('no_perkara') ? $this->input->post('no_perkara') : NULL;
            $telp_staff = $this->input->post('telp_staff') ? $this->input->post('telp_staff') : NULL;
            $jns_surat  = $this->input->post('jns_surat');

            $data_input = [
                'NO_SURAT'       => $this->input->post('no_surat'),
                'NO_PERKARA'     => $no_perkara,
                'TELP_STAFF'     => $telp_staff, 
                'JNS_SURAT'      => $jns_surat,
                'PERIHAL'        => $this->input->post('perihal'),
                'TGL_SURAT'      => $this->input->post('tgl_surat'),
                'TGL_REGISTRASI' => date('Y-m-d H:i:s'),
                'ARSIP_DIGITAL'  => $file_data['file_name'],
                'TTD_ADMIN'      => $nama_admin
            ];

            // 1. Simpan dokumen ke tabel surat
            $this->db->insert('surat', $data_input); 

            // 2. LOGIKA OTOMATIS: Jika ini surat "Disposisi Tim" dan ada perkara + staf yang ditunjuk
            if ($jns_surat === 'Disposisi Tim' && !empty($no_perkara) && !empty($telp_staff)) {
                // Update kolom TELP_STAFF di tabel perkara agar dialihkan ke Kuasa Hukum baru
                $this->db->where('NO_PERKARA', $no_perkara);
                $this->db->update('perkara', ['TELP_STAFF' => $telp_staff]);
            }

            $this->session->set_flashdata('success', 'Dokumen surat berhasil disimpan dan perkara resmi didelegasikan.');
            redirect('dashboard/surat');
        }
    }

    // ================= MODUL KEUANGAN =================
    
    /**
     * Controller keuangan multi-aksi
     * Aksi: index, pengajuan, verifikasi, approval, pembayaran, dll
     */
    public function keuangan($aksi = 'index') {
        $jabatan = $this->session->userdata('jabatan');

        // Cek akses role
        if (!in_array($jabatan, ['Admin', 'Keuangan', 'Pimpinan', 'Kuasa Hukum'])) {
            redirect('dashboard');
            return;
        }

        switch ($aksi) {
              case 'index':
                // Perbaikan: Jangan di-redirect ke verifikasi, melainkan load dashboard utama keuangan
                $data['title'] = 'Dashboard Keuangan';
                $data['jml_pending']   = $this->db->where('STATUS_VERIFIKASI_OPS', 'Pending keuangan')->count_all_results('keuangan');
                $data['jml_disetujui'] = $this->db->where('STATUS_VERIFIKASI_OPS', 'Validasi Selesai')->count_all_results('keuangan'); 
                $data['jml_total']     = $this->db->count_all('keuangan');
                $data['pengajuan']     = $this->db->get_where('keuangan', ['STATUS_VERIFIKASI_OPS' => 'Pending keuangan'])->result();

                $this->_render('dashboard/keuangan/v_index', $data);
                break;

            case 'pengajuan':
                $this->_render('dashboard/keuangan/v_pengajuan');
                break;

			case 'pengajuan_ops':
					$data['title'] = 'Ajukan Biaya Ops';
					
					// 1. Ambil perkara yang relevan saja (Misal: status bukan 'Selesai')
					// Jika Anda ingin semua perkara, gunakan: $this->db->get('perkara')->result_array();
					$data['perkara_ops'] = $this->db->where('STATUS_perkara !=', 'Selesai')->get('perkara')->result_array();

					// 2. Ambil jabatan & telp dari session (pastikan key session sesuai dengan login Anda)
					$jabatan    = $this->session->userdata('jabatan'); // Periksa apakah 'jabatan' atau 'jabatan_staff'
					$telp_login = $this->session->userdata('telp_staff');

					// 3. Logika Hak Akses
					$this->db->select('keuangan.*, perkara.JUDUL_perkara AS JUDUL_PERKARA');
					$this->db->from('keuangan');
					$this->db->join('perkara', 'perkara.NO_PERKARA = keuangan.NO_PERKARA', 'left');
					$this->db->like('keuangan.NO_TRANSAKSI', 'TRX-', 'after');

					if (strtolower($jabatan) == 'kuasa hukum') {
						$this->db->where('keuangan.TELP_STAFF', $telp_login);
					}
					
					$data['riwayat_pengajuan'] = $this->db->order_by('keuangan.TGL_PENGAJUAN_OPS', 'DESC')->get()->result_array();

					$this->_render('dashboard/keuangan/v_pengajuan_ops', $data);
					break;

            case 'verifikasi':
                $this->load->model('M_perkara');
                if ($jabatan == 'Admin') {
                    $data['berkas'] = $this->M_perkara->get_antrean_admin();
                } else if ($jabatan == 'Kuasa Hukum') {
                    $nama_aktif = $this->session->userdata('nama_staf');
                    $stf = $this->db->get_where('karyawan', ['NAMA_STAFF' => $nama_aktif])->row_array();
                    $telp_staff = $stf['TELP_STAFF'] ?? 'KOSONG';
                    $data['berkas'] = $this->M_perkara->get_antrean_verifikasi_kuasa($telp_staff);
                } else {
                    $data['berkas'] = $this->M_perkara->get_antrean_keuangan();
                }
                $data['title'] = 'Verifikasi Berkas';
                $this->_render('dashboard/keuangan/v_verifikasi', $data);
                break;

            case 'proses_verifikasi':
                $NO_PERKARA = $this->input->post('NO_PERKARA'); 
                $this->db->where('NO_PERKARA', $NO_PERKARA);
                $this->db->update('keuangan', ['STATUS_VERIFIKASI_OPS' => 'Validasi Selesai']);
                $this->session->set_flashdata('sukses', 'Berkas berhasil diverifikasi!');
                redirect('dashboard/keuangan/pembayaran'); 
                break;

           case 'approval':
				$data['title'] = 'Approval Pimpinan';
				$data['antrean_approval'] = $this->db
					// PERBAIKAN: Tambahkan AS JUDUL_PERKARA menggunakan huruf besar
					->select('keuangan.*, perkara.NO_PERKARA, perkara.JUDUL_perkara AS JUDUL_PERKARA')
					->from('keuangan')
					->join('perkara', 'perkara.NO_PERKARA = keuangan.NO_PERKARA', 'left')
					->like('keuangan.STATUS_VERIFIKASI_OPS', 'Pending Pimpinan', 'both')
					->get()
					->result_array();
				$this->_render('dashboard/keuangan/v_approval', $data);
				break;

                
            case 'proses_approval':
                $no_transaksi = $this->uri->segment(4);
                $status_klik  = $this->uri->segment(5);
                if (!empty($no_transaksi)) {
                    if ($status_klik == 'ACC') {
                        $data_update = [
                            'STATUS_VERIFIKASI_OPS' => 'Pending keuangan', 
                            'TTD_PIMPINAN'          => 'APPROVED_BY_PIMPINAN' 
                        ];
                    } else {
                        $data_update = [
                            'STATUS_VERIFIKASI_OPS' => 'Ditolak', 
                            'TTD_PIMPINAN'          => 'REJECTED_BY_PIMPINAN'
                        ];
                    }
                    $this->db->where('NO_TRANSAKSI', $no_transaksi);
                    $this->db->update('keuangan', $data_update);
                    $this->session->set_flashdata('sukses_approval', 'Pengajuan operasional berhasil diproses!');
                }
                redirect('dashboard/pimpinan'); 
                break;
                
            case 'cairkan_ops':
                $no_transaksi = $this->input->post('no_transaksi');
                $no_nota      = $this->input->post('no_nota');
                if (!empty($no_transaksi)) {
                    $data_update = [
                        'BUKTI_NOTA_KAS_KELUAR' => $no_nota,
                        'STATUS_VERIFIKASI_OPS' => 'Validasi Selesai'
                    ];
                    $this->db->where('NO_TRANSAKSI', $no_transaksi);
                    $this->db->update('keuangan', $data_update);
                }
                redirect('dashboard/keuangan');
                break;

                       case 'pembayaran':
                $data['title'] = 'Pembayaran Klien';
                
                // Bersihkan sisa filter query sebelumnya
                $this->db->flush_cache(); 
                
                // Ambil semua data keuangan untuk dilempar ke view v_pembayaran
                $data['tagihan'] = $this->db->order_by('NO_TRANSAKSI', 'DESC')->get('keuangan')->result_array();
                
                $this->_render('dashboard/keuangan/v_pembayaran', $data);
                break;


                
            case 'verifikasi_bayar':
                $segments = $this->uri->segment_array();
                $no_transaksi = end($segments);
                if (!empty($no_transaksi) && $no_transaksi !== 'verifikasi_bayar') {
                    $no_transaksi = urldecode($no_transaksi);
                    $data_update = [
                        'STATUS_BAYAR_KLIEN'    => 'Lunas',
                        'STATUS_VERIFIKASI_OPS' => 'Validasi Selesai'
                    ];
                    $this->db->where('NO_TRANSAKSI', $no_transaksi);
                    $this->db->update('keuangan', $data_update);
                }
                redirect('dashboard/keuangan/pembayaran');
                break;

            case 'tambah_tagihan':
				$data['title'] = 'Buat Tagihan';
				$seg_4 = $this->uri->segment(4);
				$seg_5 = $this->uri->segment(5);
				$no_transaksi = ($seg_4 !== 'tambah_tagihan' && !empty($seg_4)) ? $seg_4 : $seg_5;

				$data['perkara_tunggal'] = null;

				if (!empty($no_transaksi)) {
					// Ambil data satu transaksi spesifik secara akurat
					$data['perkara_tunggal'] = $this->db
						->select('perkara.*, keuangan.NO_TRANSAKSI, keuangan.STATUS_VERIFIKASI_OPS')
						->from('perkara')
						->join('keuangan', 'perkara.NO_PERKARA = keuangan.NO_PERKARA')
						->where('keuangan.NO_TRANSAKSI', urldecode($no_transaksi))
						->get()
						->row_array();
				}

				// List fallback jika tidak ada parameter transaksi di URL
				$data['daftar_perkara_all'] = $this->db->get('perkara')->result_array();

				$this->_render('dashboard/keuangan/v_tambah_tagihan', $data);
				break;


            case 'simpan_tagihan_final':
                $this->proses_simpan_tagihan(); 
                break;
			
			            case 'edit_tagihan':
                $data['title'] = 'Edit Tagihan Invoice';
                $no_transaksi = $this->uri->segment(4);

                if (!empty($no_transaksi)) {
                    $data['tagihan'] = $this->db->get_where('keuangan', ['NO_TRANSAKSI' => urldecode($no_transaksi)])->row_array();
                }

                if (empty($data['tagihan'])) {
                    $this->session->set_flashdata('error', 'Data tagihan tidak ditemukan!');
                    redirect('dashboard/keuangan/pembayaran');
                    return;
                }

                $this->_render('dashboard/keuangan/v_edit_tagihan', $data);
                break;

            case 'proses_edit_tagihan':
                $no_transaksi = $this->uri->segment(4);

                if (!empty($no_transaksi)) {
                    $data_update = [
                        'NO_INVOICE'        => $this->security->xss_clean($this->input->post('no_invoice')),
                        'TTL_TAGIHAN_KLIEN' => $this->security->xss_clean($this->input->post('ttl_tagihan'))
                    ];

                    $this->db->where('NO_TRANSAKSI', urldecode($no_transaksi));
                    $this->db->update('keuangan', $data_update);

                    $this->session->set_flashdata('pesan', 'Data tagihan invoice berhasil diperbarui!');
                }
                redirect('dashboard/keuangan/pembayaran');
                break;


            default: // <--- SEKARANG HANYA ADA SATU DEFAULT DI PALING BAWAH SWITCH
                $data['jml_pending']   = $this->db->where('STATUS_VERIFIKASI_OPS', 'Pending keuangan')->count_all_results('keuangan');
                $data['jml_disetujui'] = $this->db->where('STATUS_VERIFIKASI_OPS', 'Validasi Selesai')->count_all_results('keuangan'); 
                $data['jml_total']     = $this->db->count_all('keuangan');
                $data['pengajuan']     = $this->db->get_where('keuangan', ['STATUS_VERIFIKASI_OPS' => 'Pending keuangan'])->result();
                $this->_render('dashboard/keuangan/v_index', $data);
                break;
        }
    }

    /**
     * Laporan sistem - Admin & Pimpinan
     */
    public function laporan() {
        if (!in_array($this->session->userdata('jabatan'), ['Admin', 'Pimpinan'])) redirect('dashboard');
        $this->_render('dashboard/admin/v_laporan', ['title' => 'Laporan Sistem']);
    }
    
    // ================= DASHBOARD PIMPINAN =================
    
    /**
     * Dashboard khusus Pimpinan
     * Isi: KPI, keuangan, Data recent
     */
    public function pimpinan() {
        if ($this->session->userdata('jabatan') != 'Pimpinan') {
            redirect('dashboard');
            return;
        }

        $data['title'] = 'Dashboard Pimpinan';
        
        // Summary card atas
        $data['jml_perkara'] = $this->db->count_all('perkara');
        $data['jml_surat']   = $this->db->count_all('surat');
        $data['jml_staff']   = $this->db->count_all('karyawan');

        // KPI perkara
        $data['perkara_proses']  = $this->db->where('STATUS_perkara', 'Proses')->count_all_results('perkara');
        $data['perkara_selesai'] = $this->db->where('STATUS_perkara', 'Selesai')->count_all_results('perkara');
        $data['surat_masuk']     = $this->db->where('JNS_SURAT', 'Masuk')->count_all_results('surat');

        // Status keuangan
        $data['total_pengajuan']      = $this->db->count_all('keuangan');
        $data['pending_admin']         = $this->db->where('STATUS_VERIFIKASI_OPS', 'Pending Admin')->count_all_results('keuangan');
        $data['pending_kuasa_hukum']   = $this->db->where('STATUS_VERIFIKASI_OPS', 'Pending Kuasa Hukum')->count_all_results('keuangan');
        $data['pending_keuangan']      = $this->db->where('STATUS_VERIFIKASI_OPS', 'Pending keuangan')->count_all_results('keuangan');
        $data['pending_approval']      = $this->db->where('STATUS_VERIFIKASI_OPS', 'Verifikasi keuangan')->count_all_results('keuangan');
        $data['pending_pimpinan']      = $this->db->where('STATUS_VERIFIKASI_OPS', 'Pending Pimpinan')->count_all_results('keuangan');

        // Total pembayaran lunas
        $query_bayar = $this->db->select_sum('TTL_TAGIHAN_KLIEN')->where('STATUS_BAYAR_KLIEN', 'Lunas')->get('keuangan')->row();
        $data['total_pembayaran'] = $query_bayar->TTL_TAGIHAN_KLIEN ?? 0;

        // Data recent 5 terakhir
        $data['recent_perkara'] = $this->db->order_by('NO_PERKARA', 'DESC')->limit(5)->get('perkara')->result();
        $data['recent_keuangan'] = $this->db
            ->select('keuangan.*, perkara.JUDUL_perkara')
            ->from('keuangan')
            ->join('perkara', 'perkara.NO_PERKARA = keuangan.NO_PERKARA', 'left')
            ->where('keuangan.JMLH_PENGAJUAN_OPS >', 0)
            ->order_by('keuangan.NO_TRANSAKSI', 'DESC')
            ->limit(5)
            ->get()
            ->result();

        $this->_render('dashboard/pimpinan/v_index', $data);
    }

	    // ================= MODUL MANAJEMEN CUTI =================

    /**
     * Manajemen cuti - Pimpinan
     */
    public function cuti()
    {
        $jabatan = $this->session->userdata('jabatan');
        
        if ($jabatan !== 'Pimpinan') {
            $this->session->set_flashdata('error', 'Akses ditolak! Halaman ini hanya untuk Pimpinan.');
            redirect('dashboard');
            return;
        }

        $this->db->where('TGL_MULAI_CUTI !=', NULL);
        $data['daftar_cuti'] = $this->db->get('karyawan')->result_array();
        $data['jabatan'] = $jabatan;

        // Jalur baru ke folder pimpinan
        $this->_render('dashboard/pimpinan/v_manajemen_cuti', $data);
    }

    /**
     * Form pengajuan cuti staff
     */
    public function ajukan_cuti()
    {
        // Mengambil nama staf dari session asli aplikasi Anda ('nama_staf')
        $nama_staf = $this->session->userdata('nama_staf'); 
        
        // Cari data karyawan berdasarkan NAMA_STAFF
        $data['staff'] = $this->db->get_where('karyawan', ['NAMA_STAFF' => $nama_staf])->row_array();
        $data['jabatan'] = $this->session->userdata('jabatan') ?? 'Kuasa Hukum';

        // Panggil view render dashboard
        $this->_render('dashboard/kuasa_hukum/v_ajukan_cuti', $data);
    }

    /**
     * Proses simpan pengajuan cuti staff ke database
     */
    public function proses_cuti()
    {
        // Mengambil nama staf dari input hidden form POST
        $nama_staf = $this->input->post('nama_staf');

        // Jika input form kosong, ambil dari session sebagai backup aman
        if (empty($nama_staf)) {
            $nama_staf = $this->session->userdata('nama_staf');
        }

        $data_update = [
            'TGL_MULAI_CUTI'   => $this->input->post('tgl_mulai'),
            'TGL_SELESAI_CUTI' => $this->input->post('tgl_selesai'),
            'ALASAN_CUTI'      => $this->input->post('alasan'),
            'STATUS_CUTI'      => 'Pending'
        ];

        // Eksekusi update data ke tabel 'karyawan' berdasarkan NAMA_STAFF
        $this->db->where('NAMA_STAFF', $nama_staf);
        $this->db->update('karyawan', $data_update); 

        $this->session->set_flashdata('success', 'Pengajuan cuti berhasil dikirim.');
        redirect('dashboard/ajukan_cuti');
    }
    
    /**
     * Aksi persetujuan cuti oleh Pimpinan
     */
    public function aksi_cuti()
    {
        // Ambil data kiriman dari form POST secara aman
        $status = $this->input->post('status');
        $nama_staf = $this->input->post('nama_staf');

        if ($status === 'setuju') {
            $status_baru = 'Disetujui';
        } elseif ($status === 'tolak') {
            $status_baru = 'Ditolak';
        } else {
            redirect('dashboard/cuti');
            return;
        }

        // Update status cuti karyawan di database
        $this->db->where('NAMA_STAFF', $nama_staf);
        $this->db->update('karyawan', ['STATUS_CUTI' => $status_baru]);

        $this->session->set_flashdata('success', 'Status cuti berhasil diperbarui menjadi: ' . $status_baru);
        redirect('dashboard/cuti');
    }
    
    // ================= MODUL LAPORAN EXECUTIVE (PIMPINAN) =================
    
    /**
     * Laporan Dokumen Surat
     */
    public function laporan_surat()
    {
        // Keamanan tingkat server: Pastikan hanya Pimpinan yang bisa akses
        $jabatan = $this->session->userdata('jabatan');
        if ($jabatan !== 'Pimpinan') {
            $this->session->set_flashdata('error', 'Akses ditolak! Halaman ini khusus Pimpinan.');
            redirect('dashboard');
            return;
        }

        // 1. Hitung total rangkuman statistik kategori surat
        $data['total_masuk']     = $this->db->where('JNS_SURAT', 'Surat Masuk')->count_all_results('surat');
        $data['total_keluar']    = $this->db->where('JNS_SURAT', 'Surat Keluar')->count_all_results('surat');
        $data['total_disposisi'] = $this->db->where('JNS_SURAT', 'Disposisi Tim')->count_all_results('surat');
        $data['total_semua']     = $this->db->count_all('surat');

        // 2. Ambil data surat master lengkap dengan join nama staff penerima disposisi
        $this->db->select('surat.*, karyawan.NAMA_STAFF');
        $this->db->from('surat');
        $this->db->join('karyawan', 'karyawan.TELP_STAFF = surat.TELP_STAFF', 'left');
        $this->db->order_by('surat.TGL_REGISTRASI', 'DESC');
        $data['daftar_surat'] = $this->db->get()->result_array();

        $data['title'] = 'Laporan Dokumen Surat';

        // Render menggunakan folder pimpinan
        $this->_render('dashboard/pimpinan/v_laporan_surat', $data);
    }

    /**
     * Laporan Perkara & Agenda Sidang
     */
    public function laporan_perkara()
    {
        // Keamanan server: Validasi khusus Pimpinan
        $jabatan = $this->session->userdata('jabatan');
        if ($jabatan !== 'Pimpinan') {
            $this->session->set_flashdata('error', 'Akses ditolak! Halaman ini khusus Pimpinan.');
            redirect('dashboard');
            return;
        }

        // 1. Hitung statistik perkara berdasarkan STATUS_perkara (Sinkronisasi dengan Dashboard Utama)
        $data['total_baru']    = $this->db->where('STATUS_perkara', 'Baru')->count_all_results('perkara');
        $data['total_aktif']   = $this->db->where('STATUS_perkara', 'Proses')->count_all_results('perkara');
        $data['total_selesai'] = $this->db->where('STATUS_perkara', 'Selesai')->count_all_results('perkara');
        $data['total_ditunda'] = $this->db->where('STATUS_perkara', 'Ditunda')->count_all_results('perkara');

        // 2. Ambil data gabungan perkara lengkap dengan nama Kuasa Hukum yang meng-handle
        $this->db->select('perkara.*, karyawan.NAMA_STAFF');
        $this->db->from('perkara');
        $this->db->join('karyawan', 'karyawan.TELP_STAFF = perkara.TELP_STAFF', 'left');
        $this->db->order_by('perkara.TGL_MASUK', 'DESC');
        $data['daftar_perkara'] = $this->db->get()->result_array();

        $data['title'] = 'Laporan Perkara & Agenda Sidang';

        // Render ke folder pimpinan
        $this->_render('dashboard/pimpinan/v_laporan_perkara', $data);
    }

    /**
     * Laporan Keuangan Internal
     */
    public function laporan_keuangan()
    {
        // Validasi Hak Akses Ketat di Server (Keuangan & Pimpinan)
        $jabatan = $this->session->userdata('jabatan');
        if (!in_array(strtolower($jabatan), ['keuangan', 'pimpinan'])) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak memiliki otoritas.');
            redirect('dashboard');
            return;
        }

        // 1. HITUNG STATISTIK KEUANGAN SECARA REALTIME
        // Total Dana Cair untuk Operasional (Disinkronkan dengan status 'Validasi Selesai')
        $this->db->select_sum('JMLH_PENGAJUAN_OPS');
        $this->db->where('STATUS_VERIFIKASI_OPS', 'Validasi Selesai'); 
        $data['total_ops_cair'] = $this->db->get('keuangan')->row()->JMLH_PENGAJUAN_OPS ?? 0;

        // Total Dana Pending Operasional (Masih di Pimpinan/Keuangan)
        $this->db->select_sum('JMLH_PENGAJUAN_OPS');
        $this->db->group_start();
        $this->db->where('STATUS_VERIFIKASI_OPS', 'Pending Pimpinan');
        $this->db->or_where('STATUS_VERIFIKASI_OPS', 'Pending keuangan');
        $this->db->group_end();
        $data['total_ops_pending'] = $this->db->get('keuangan')->row()->JMLH_PENGAJUAN_OPS ?? 0;

        // Jumlah Perkara Lunas dan Belum Bayar
        $data['perkara_lunas'] = $this->db->where('STATUS_BAYAR_KLIEN', 'Lunas')->count_all_results('keuangan');
        $data['perkara_belum'] = $this->db->where('STATUS_BAYAR_KLIEN', 'Belum Bayar')->count_all_results('keuangan');

        // 2. AMBIL SEMUA DATA ARUS KEUANGAN GABUNGAN DENGAN DATA PERKARA KLIEN (Kolom: JUDUL_perkara)
        $this->db->select('keuangan.*, perkara.NO_PERKARA, perkara.JUDUL_perkara, perkara.NAMA_KLIEN');
        $this->db->from('keuangan');
        $this->db->join('perkara', 'perkara.NO_PERKARA = keuangan.NO_PERKARA', 'left');
        $this->db->order_by('keuangan.NO_TRANSAKSI', 'DESC');
        $data['daftar_keuangan'] = $this->db->get()->result_array();

        $data['title'] = 'Laporan Keuangan Internal';

        // Render menggunakan folder admin/umum agar aman dibuka Keuangan maupun Pimpinan
        $this->_render('dashboard/admin/v_laporan_keuangan', $data);
    }

    /**
     * Proses Validasi Berkas Operasional oleh Kuasa Hukum
     */
    public function proses_validasi($no_perkara) {
        // 1. Update status menjadi 'Validasi Selesai' agar hilang dari antrean
        $this->db->where('NO_PERKARA', $no_perkara);
        $this->db->update('keuangan', [
            'STATUS_VERIFIKASI_OPS' => 'Validasi Selesai',
            'TTD_KUASA_HUKUM'       => 'TERTANDA_SISTEM_KH'
        ]);

        // 2. Beri notifikasi
        $this->session->set_flashdata('sukses', 'Perkara berhasil divalidasi!');
        
        // 3. Kembali ke dashboard
        redirect('dashboard');
    }

	
    // ================= FUNGSI DIRECT ANTI MEMANTUL =================

    /**
     * Alihan langsung ke halaman pengajuan operasional dana
     */
    public function pengajuan_ops_dana() {
        $this->keuangan('pengajuan_ops');
    }

    /**
     * Alihan langsung ke halaman verifikasi berkas perkara klien
     */
    public function verifikasi_berkas_klien() {
        $this->keuangan('verifikasi');
    }

    /**
     * Alihan langsung ke halaman monitoring pembayaran invoice klien
     */
    public function pembayaran_invoice_klien() {
        $this->keuangan('pembayaran');
    }
    
    /**
     * Proses simpan dan penerbitan tagihan invoice final untuk klien
     */
    private function proses_simpan_tagihan() {
        // 1. Ambil ID Transaksi dari URL secara fleksibel
        $seg_4 = $this->uri->segment(4);
        $seg_5 = $this->uri->segment(5);
        $no_transaksi = ($seg_4 !== 'simpan_tagihan_final') ? $seg_4 : $seg_5;
        
        // 2. Siapkan data untuk update (Sinkronisasi nilai status 'Validasi Selesai')
        $data_update = [
            'NO_INVOICE'            => $this->security->xss_clean($this->input->post('no_invoice')),
            'TTL_TAGIHAN_KLIEN'     => $this->security->xss_clean($this->input->post('ttl_tagihan')),
            'STATUS_VERIFIKASI_OPS' => 'Validasi Selesai', 
            'STATUS_BAYAR_KLIEN'    => 'Belum Bayar' 
        ];

        // 3. Jalankan Operasi Update ke Database
        if (!empty($no_transaksi)) {
            $this->db->where('NO_TRANSAKSI', urldecode($no_transaksi));
            $this->db->update('keuangan', $data_update);
            
            // Memberikan notifikasi sukses (Flashdata)
            $this->session->set_flashdata('pesan', 'Tagihan berhasil diterbitkan!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menerbitkan invoice. Parameter transaksi kosong.');
        }
        
        // 4. Redirect kembali ke halaman rekap pembayaran keuangan
        redirect('dashboard/keuangan/pembayaran');
    }
}
/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */