<?php
function isActive($pageName) {
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 'hello-world';
    return ($currentPage === $pageName) ? 'active' : '';
}
?>
<div class="sidebar">
    <div class="sidebar-section">
        <h4><i class="ph-bold ph-book-open"></i> Dasar PHP</h4>
        <ul>
            <li><a href="?page=biodata" class="<?php echo isActive('biodata'); ?>">Biodata</a></li>
            <li><a href="?page=view" class="<?php echo isActive('view'); ?>">Menampilkan Data</a></li>
            <li><a href="?page=hello-world" class="<?php echo isActive('hello-world'); ?>">Hello World</a></li>
            <li><a href="?page=variables" class="<?php echo isActive('variables'); ?>">Variable</a></li>
            <li><a href="?page=variable_object" class="<?php echo isActive('variable_object'); ?>">Object</a></li>
            <li><a href="?page=konstanta" class="<?php echo isActive('konstanta'); ?>">Konstanta</a></li>
            <li><a href="?page=aritmatika" class="<?php echo isActive('aritmatika'); ?>">Operator Aritmatika</a></li>
            <li><a href="?page=pembanding" class="<?php echo isActive('pembanding'); ?>">Operator Pembandingan</a></li>
            <li><a href="?page=operasi_string" class="<?php echo isActive('operasi_string'); ?>">Operator String</a></li>
        </ul>
    </div>

    <div class="sidebar-section">
        <h4><i class="ph-bold ph-textbox"></i> Penanganan Form</h4>
        <ul>
            <li><a href="?page=form/method_get" class="<?php echo isActive('form/method_get'); ?>">Method GET</a></li>
            <li><a href="?page=form/method_post" class="<?php echo isActive('form/method_post'); ?>">Method POST</a></li>
            <li><a href="?page=form/method_post_terpisah" class="<?php echo isActive('form/method_post_terpisah'); ?>">Method POST Action terpisah</a></li>
            <li><a href="?page=form/form_biodata" class="<?php echo isActive('form/form_biodata'); ?>">Form Biodata</a></li>
            <li><a href="?page=form/form_table" class="<?php echo isActive('form/form_table'); ?>">Form Table</a></li>
            <li><a href="?page=form/konversi_nilai" class="<?php echo isActive('form/konversi_nilai'); ?>">Konversi Nilai</a></li>
        </ul>
    </div>
    
    <div class="sidebar-section">
        <h4><i class="ph-bold ph-git-branch"></i> Struktur Control</h4>
        <ul>
            <li><a href="?page=struktur-control/if-else" class="<?php echo isActive('struktur-control/if-else'); ?>">If Else</a></li>
            <li><a href="?page=struktur-control/if-elseif-else" class="<?php echo isActive('struktur-control/if-elseif-else'); ?>">If Elseif Else</a></li>
            <li><a href="?page=struktur-control/switch-case" class="<?php echo isActive('struktur-control/switch-case'); ?>">Switch Case</a></li>
            <li><a href="?page=struktur-control/for-loop" class="<?php echo isActive('struktur-control/for-loop'); ?>">For Loop</a></li>
            <li><a href="?page=struktur-control/while-loop" class="<?php echo isActive('struktur-control/while-loop'); ?>">While Loop</a></li>
            <li><a href="?page=struktur-control/do-while" class="<?php echo isActive('struktur-control/do-while'); ?>">Do While</a></li>
            <li><a href="?page=struktur-control/foreach-loop" class="<?php echo isActive('struktur-control/foreach-loop'); ?>">Foreach Loop</a></li>
        </ul>
    </div>
    
    <div class="sidebar-section">
        <h4><i class="ph-bold ph-brackets-curly"></i> Function</h4>
        <ul>
            <li><a href="?page=function/function-sendiri" class="<?php echo isActive('function/function-sendiri'); ?>">Function Sendiri</a></li>
            <li><a href="?page=function/function-parameter" class="<?php echo isActive('function/function-parameter'); ?>">Function Parameter</a></li>
            <li><a href="?page=function/call-by-value" class="<?php echo isActive('function/call-by-value'); ?>">Call by Value</a></li>
            <li><a href="?page=function/call-by-reference" class="<?php echo isActive('function/call-by-reference'); ?>">Call by Reference</a></li>
            <li><a href="?page=function/optional-argument" class="<?php echo isActive('function/optional-argument'); ?>">Optional Argument</a></li>
        </ul>
    </div>
</div>