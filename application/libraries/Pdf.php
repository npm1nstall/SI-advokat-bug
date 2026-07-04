<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Mengarah ke file utama TCPDF yang baru saja Anda copy
require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class Pdf extends TCPDF {
    function __construct() {
        parent::__construct();
    }
}