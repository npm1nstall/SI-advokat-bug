<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM Kantor Advokat</title>
    
    <!-- 

    HEADER TEMPLATE
    File: v_header.php 
    Fungsi: Load CSS + Set class body beda role + Wrapper layout
    Dipanggil di: Semua view via $this->_render()

    -->
    
    <!-- CDN boostrap css -->
   https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css
    
    <!-- CDN FontAwesome 5 Icons buat icon menu, button, dll -->
    https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css
    
    <!-- Load CSS custom kamu. ?v=2.0 buat force refresh cache browser -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css?v=2.0');?>">

</head>
<?php
/**
 * LOGIC ROLE UNTUK STYLING CSS
 * Ambil data session: jabatan staff atau klien_logged_in
 * Hasil: class body jadi role-admin, role-kuasa-hukum, role-keuangan, role-klien
 * Gunanya: Bisa styling beda warna sidebar/header per role di style.css
 */
$jabatan = $this->session->userdata('jabatan');
$role = $this->session->userdata('klien_logged_in') ? 'klien' : strtolower(str_replace(' ', '-', $jabatan));
?>

<!-- Class role-xxx dipake buat CSS spesifik per role -->
<body class="role-<?= $role; ?>">
    <!-- Wrapper flexbox buat layout sidebar + content -->
    <div class="d-flex" id="wrapper">