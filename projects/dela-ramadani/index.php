<?php 
require_once 'header.php'; 
require_once 'sidebar.php'; 
?>

<div class="content">
    <?php 
    if (isset($_GET['page'])) {
        $page = $_GET['page'];

        switch ($page) {
            case 'biodata':
                include 'biodata.php';
                break;
            case 'menampilkan_data':
                include 'dasar_php/menampilkan_data.php';
                break;
            case 'helloworld':
                include 'dasar_php/helloworld.php';
                break;
            case 'variabel':
                include 'dasar_php/variabel.php';
                break;
            case 'variabel_object':
                include 'dasar_php/variabel_object.php';
                break;
            case 'konstanta':
                include 'dasar_php/konstanta.php';
                break;
            case 'operator_aritmatika':
                include 'dasar_php/operator_aritmatika.php';
                break;
            case 'operator_perbandingan':
                include 'dasar_php/operator_perbandingan.php';
                break;
            case 'operator_string':
                include 'dasar_php/operator_string.php';
                break;
            case 'if_else':
                include 'struktur_kontrol/if_else.php';
                break;
            case 'if_elseif_else':
                include 'struktur_kontrol/if_elseif_else.php';
                break;
            case 'switch_case':
                include 'struktur_kontrol/switch_case.php';
                break;
            case 'while_loop':
                include 'struktur_kontrol/while_loop.php';
                break;
            case 'do_while_loop':
                include 'struktur_kontrol/do_while_loop.php';
                break;
            case 'for_loop':
                include 'struktur_kontrol/for_loop.php';
                break;
            case 'foreach_loop':
                include 'struktur_kontrol/foreach_loop.php';
                break;
            case 'built-in_functions':
                include 'function_&_modularitas/built-in_functions.php';
                break;
            case 'membuat_function_sendiri':
                include 'function_&_modularitas/membuat_function_sendiri.php';
                break;
            case 'function_dengan_parameter':
                include 'function_&_modularitas/function_dengan_parameter.php';
                break;
            case 'optional_arguments':
                include 'function_&_modularitas/optional_arguments.php';
                break;
            case 'call_by_value':
                include 'function_&_modularitas/call_by_value.php';
                break;
            case 'call_by_reference':
                include 'function_&_modularitas/call_by_reference.php';
                break;
            case 'konversi_nilai':
                include 'penanganan_form/konversi_nilai.php';
                break;
            case 'form_biodata':
                include 'penanganan_form/form_biodata.php';
                break;
            case 'form_tabel':
                include 'penanganan_form/form_tabel.php';
                break;
            default:
                echo "<h3>Halaman tidak ditemukan!</h3>";
                break;
        }
    } else {
        include 'biodata.php'; 
    }
    ?>
</div>

<?php 
require_once 'footer.php'; 
?>