<div class="sidebar">
    <h5>Profil</h5>
    <ul>
        <li><a href="index.php?page=home" class="<?php echo (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active' : ''; ?>">Biodata</a></li>
    </ul>
    
    <h5>Materi</h5>
    <ul>
        <li><a href="index.php?page=helloworld" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'helloworld') ? 'active' : ''; ?>">Hello World</a></li>
        <li><a href="index.php?page=menampilkan_data" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'menampilkan_data') ? 'active' : ''; ?>">Menampilkan Data</a></li>
        <li><a href="index.php?page=konstanta" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'konstanta') ? 'active' : ''; ?>">Konstanta</a></li>
        <li><a href="index.php?page=variabel" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'variabel') ? 'active' : ''; ?>">Variabel</a></li>
        <li><a href="index.php?page=variabel_object" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'variabel_object') ? 'active' : ''; ?>">Variabel Object</a></li>
        <li><a href="index.php?page=op_aritmatika" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'op_aritmatika') ? 'active' : ''; ?>">Operator Aritmetika</a></li>
        <li><a href="index.php?page=op_pembandingan" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'op_pembandingan') ? 'active' : ''; ?>">Operator Perbandingan</a></li>
        <li><a href="index.php?page=op_string" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'op_string') ? 'active' : ''; ?>">Operator String</a></li>
    </ul>
    
    <h5>Kondisi & Loop</h5>
    <ul>
        <li><a href="index.php?page=if_else" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'if_else') ? 'active' : ''; ?>">Kondisi If Else</a></li>
        <li><a href="index.php?page=if_elseif_else" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'if_elseif_else') ? 'active' : ''; ?>">Kondisi If Elseif</a></li>
        <li><a href="index.php?page=switch_case" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'switch_case') ? 'active' : ''; ?>">Switch Case</a></li>
        <li><a href="index.php?page=for_loop" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'for_loop') ? 'active' : ''; ?>">For Loop</a></li>
        <li><a href="index.php?page=while_loop" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'while_loop') ? 'active' : ''; ?>">While Loop</a></li>
        <li><a href="index.php?page=do_while_loop" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'do_while_loop') ? 'active' : ''; ?>">Do While Loop</a></li>
        <li><a href="index.php?page=foreach_loop" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'foreach_loop') ? 'active' : ''; ?>">Foreach Loop</a></li>
    </ul>
    
    <h5>Functions</h5>
    <ul>
        <li><a href="index.php?page=func_tanpa_parameter" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'func_tanpa_parameter') ? 'active' : ''; ?>">Func Tanpa Parameter</a></li>
        <li><a href="index.php?page=func_dengan_parameter" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'func_dengan_parameter') ? 'active' : ''; ?>">Func Dengan Parameter</a></li>
        <li><a href="index.php?page=func_optional_arguments" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'func_optional_arguments') ? 'active' : ''; ?>">Func Optional Arguments</a></li>
        <li><a href="index.php?page=func_call_by_value" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'func_call_by_value') ? 'active' : ''; ?>">Func Call By Value</a></li>
        <li><a href="index.php?page=func_call_by_reference" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'func_call_by_reference') ? 'active' : ''; ?>">Func Call By Reference</a></li>
        <li><a href="index.php?page=func_Builtin" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'func_Builtin') ? 'active' : ''; ?>">Func Built-in</a></li>
    </ul>

    <h5>Form</h5>
    <ul>
        <li><a href="index.php?page=form_biodata" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'latihan1') ? 'active' : ''; ?>">Form Biodata</a></li>
        <li><a href="index.php?page=form_konversi_nilai" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'latihan2') ? 'active' : ''; ?>">Form Konversi Nilai</a></li>
        <li><a href="index.php?page=form_membuat_tabel" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'latihan3') ? 'active' : ''; ?>">Form Membuat Tabel</a></li>
    </ul>
</div>