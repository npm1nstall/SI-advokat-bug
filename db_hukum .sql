-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2026 at 09:41 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_hukum`
--

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `TELP_STAFF` varchar(15) NOT NULL,
  `NAMA_STAFF` varchar(50) NOT NULL,
  `JABATAN_STAFF` varchar(20) NOT NULL,
  `PASS_STAFF` varchar(32) NOT NULL,
  `TGL_MULAI_CUTI` datetime DEFAULT NULL,
  `TGL_SELESAI_CUTI` datetime DEFAULT NULL,
  `ALASAN_CUTI` text DEFAULT NULL,
  `STATUS_CUTI` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`TELP_STAFF`, `NAMA_STAFF`, `JABATAN_STAFF`, `PASS_STAFF`, `TGL_MULAI_CUTI`, `TGL_SELESAI_CUTI`, `ALASAN_CUTI`, `STATUS_CUTI`) VALUES
('081111111111', 'Budi Prasetyo, S.H.', 'Kuasa Hukum', '9c5fa085ce256c7c598f6710584ab25d', NULL, NULL, NULL, NULL),
('081234567890', 'Admin Utama', 'Admin', '0192023a7bbd73250516f069df18b500', NULL, NULL, NULL, NULL),
('081234567891', 'Pimpinan Kantor', 'Pimpinan', '7d3207a13dc221ac13c2f3dac3011f50', NULL, NULL, NULL, NULL),
('081234567892', 'Staf Keuangan', 'Keuangan', '87cbf810625de2ff054ac8b841e135df', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `keuangan`
--

CREATE TABLE `keuangan` (
  `TELP_STAFF` varchar(15) NOT NULL,
  `NO_TRANSAKSI` varchar(50) NOT NULL,
  `NO_PERKARA` varchar(50) NOT NULL,
  `NO_INVOICE` varchar(50) DEFAULT NULL,
  `TTL_TAGIHAN_KLIEN` decimal(12,2) DEFAULT NULL,
  `BUKTI_BAYAR_KLIEN` varchar(225) DEFAULT NULL,
  `STATUS_BAYAR_KLIEN` varchar(30) DEFAULT NULL,
  `TGL_PENGAJUAN_OPS` datetime DEFAULT NULL,
  `JMLH_PENGAJUAN_OPS` decimal(12,2) DEFAULT NULL,
  `KEPERLUAN_DANA_OPS` varchar(150) DEFAULT NULL,
  `STATUS_VERIFIKASI_OPS` varchar(20) DEFAULT NULL,
  `BUKTI_NOTA_KAS_KELUAR` varchar(255) DEFAULT NULL,
  `TTD_PIMPINAN` varchar(255) DEFAULT NULL,
  `TTD_KUASA_HUKUM` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `keuangan`
--

INSERT INTO `keuangan` (`TELP_STAFF`, `NO_TRANSAKSI`, `NO_PERKARA`, `NO_INVOICE`, `TTL_TAGIHAN_KLIEN`, `BUKTI_BAYAR_KLIEN`, `STATUS_BAYAR_KLIEN`, `TGL_PENGAJUAN_OPS`, `JMLH_PENGAJUAN_OPS`, `KEPERLUAN_DANA_OPS`, `STATUS_VERIFIKASI_OPS`, `BUKTI_NOTA_KAS_KELUAR`, `TTD_PIMPINAN`, `TTD_KUASA_HUKUM`) VALUES
('', 'TRX-20260619180429-212', 'PRK-20260619-208', 'INV/06/2026/01', 5000000.00, 'ae505d5707ff777501306043405c54f7.jpg', 'Lunas', '2026-06-20 18:59:49', 1000000.00, 'Biaya operasional tim hukum untuk transportasi, penggandaan berkas gugatan, dan logistik lapangan di Pengadilan Negeri.', 'Validasi Selesai', 'NKK/2026/1', 'APPROVED_BY_PIMPINAN', 'TERTANDA_SISTEM_KH'),
('', 'TRX-20260619175430-890', 'REG-20260619175430-30', NULL, NULL, NULL, 'Belum Bayar', NULL, NULL, NULL, 'Pending Admin', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `perkara`
--

CREATE TABLE `perkara` (
  `NO_PERKARA` varchar(50) NOT NULL,
  `TELP_STAFF` varchar(15) DEFAULT NULL,
  `JUDUL_PERKARA` varchar(255) NOT NULL,
  `TGL_MASUK` datetime NOT NULL,
  `BERKAS_PERKARA` varchar(255) NOT NULL,
  `STATUS_PERKARA` varchar(20) NOT NULL,
  `NAMA_KLIEN` varchar(100) NOT NULL,
  `TELP_KLIEN` varchar(20) NOT NULL,
  `ALAMAT_KLIEN` text NOT NULL,
  `TGL_PENUGASAN_TIM` datetime DEFAULT NULL,
  `CATATAN_DISPOSISI` text DEFAULT NULL,
  `TGL_SIDANG` datetime DEFAULT NULL,
  `AGENDA_SIDANG` varchar(100) DEFAULT NULL,
  `HASIL_SIDANG` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `perkara`
--

INSERT INTO `perkara` (`NO_PERKARA`, `TELP_STAFF`, `JUDUL_PERKARA`, `TGL_MASUK`, `BERKAS_PERKARA`, `STATUS_PERKARA`, `NAMA_KLIEN`, `TELP_KLIEN`, `ALAMAT_KLIEN`, `TGL_PENUGASAN_TIM`, `CATATAN_DISPOSISI`, `TGL_SIDANG`, `AGENDA_SIDANG`, `HASIL_SIDANG`) VALUES
('PRK-20260619-208', NULL, 'Suarat Kuasa Waris', '2026-06-19 18:04:29', 'Surat_Kuasa_Waris3.pdf', 'Baru', 'SUGENG', '0812222222222', 'Jl. Gula Jawa No 15', '2026-06-22 08:00:00', '', '2026-06-25 08:00:00', 'Sidang Pertama / Pemeriksaan Berkas', ''),
('REG-20260619175430-30', NULL, 'Pendaftaran Akun Baru', '2026-06-19 17:54:30', '', 'Baru', 'SUGENG', '0812222222222', 'JL. Gula Jawa No 15 ', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `surat`
--

CREATE TABLE `surat` (
  `NO_SURAT` varchar(100) NOT NULL,
  `NO_PERKARA` varchar(50) DEFAULT NULL,
  `TELP_STAFF` varchar(15) DEFAULT NULL,
  `JNS_SURAT` varchar(15) NOT NULL,
  `PERIHAL` varchar(100) NOT NULL,
  `TGL_SURAT` datetime NOT NULL,
  `TGL_REGISTRASI` datetime NOT NULL,
  `ARSIP_DIGITAL` varchar(255) NOT NULL,
  `TTD_ADMIN` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`TELP_STAFF`);

--
-- Indexes for table `keuangan`
--
ALTER TABLE `keuangan`
  ADD PRIMARY KEY (`NO_PERKARA`,`TELP_STAFF`,`NO_TRANSAKSI`);

--
-- Indexes for table `perkara`
--
ALTER TABLE `perkara`
  ADD PRIMARY KEY (`NO_PERKARA`);

--
-- Indexes for table `surat`
--
ALTER TABLE `surat`
  ADD PRIMARY KEY (`NO_SURAT`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
