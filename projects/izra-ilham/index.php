<?php
include 'header.php';
include 'sidebar.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

$allowed_pages = [
    'home', 'helloworld', 'menampilkan_data', 'konstanta', 'variabel', 'variabel_object',
    'op_aritmatika', 'op_pembandingan', 'op_string',
    'if_else', 'if_elseif_else', 'switch_case',
    'for_loop', 'while_loop', 'do_while_loop', 'foreach_loop',
    'func_tanpa_parameter', 'func_dengan_parameter', 'func_optional_arguments', 
    'func_call_by_value', 'func_call_by_reference', 'func_Builtin',
    'form_biodata', 'form_konversi_nilai', 'form_membuat_tabel'
];

echo '<div class="main-content">';

if ($page == 'home') {
    if (file_exists("home.php")) {
        include "home.php";
    }
} else {
    if (in_array($page, $allowed_pages) && file_exists("$page.php")) {
        echo '<div class="output-container">';
        $judul_modul = ucwords(str_replace('_', ' ', $page));
        echo '<h2>Modul: ' . $judul_modul . '</h2>';
        echo '<div class="console-box">';
        
        include "$page.php"; // Memanggil isi script php tugasmu
        
        echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="output-container"><h2>404 - Modul Tidak Ditemukan</h2></div>';
    }
}

echo '</div>';

include 'footer.php';
?>