<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'header.php'; 
?>

<div class="main-container">
    
    <?php 

    include 'sidebar.php'; 
    ?>

    <main class="content-area">
        <div class="card">
            <?php

            $page = isset($_GET['page']) ? $_GET['page'] : 'biodata';


            switch ($page) {
                case 'biodata':
                    echo '<h3>Biodata</h3>';
                    echo '<div class="biodata-wrapper">
                            <div class="foto-box">
                                <img src="photo.jpeg" alt="Foto Biodata" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">
                            </div>
                            <div class="info-box">
                                <table class="bio-table">
                                    <tr><td><strong>NPM</strong></td><td>:</td><td>202343501595</td></tr>
                                    <tr><td><strong>NAMA</strong></td><td>:</td><td>Syanaia Lulailika</td></tr>
                                    <tr><td><strong>KELAS</strong></td><td>:</td><td>X6K</td></tr>
                                    <tr><td><strong>Mata Kuliah</strong></td><td>:</td><td>Pemrograman Web Lanjut</td></tr>
                                </table>
                            </div>
                          </div>';
                    break;

                // --- PERTEMUAN 2 ---
                case 'hello-world':
                    echo '<h3>Hello World</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P2_HelloWorld.php'; echo '</div>';
                    break;
                case 'variabel':
                    echo '<h3>Variable</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P2_Variable.php'; echo '</div>';
                    break;
                case 'object':
                    echo '<h3>Variable Object</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P2_VariableObject.php'; echo '</div>';
                    break;
                case 'konstanta':
                    echo '<h3>Konstanta</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P2_Konstanta.php'; echo '</div>';
                    break;
                case 'op-aritmetika':
                    echo '<h3>Operator Aritmatika</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P2_OperatorAritmatika.php'; echo '</div>';
                    break;
                case 'op-perbandingan':
                    echo '<h3>Operator Perbandingan</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P2_OperatorPerbandingan.php'; echo '</div>';
                    break;
                case 'op-string':
                    echo '<h3>Operator String</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P2_OperatorString.php'; echo '</div>';
                    break;

                // --- PERTEMUAN 3 ---
                case 'kondisi-if':
                    echo '<h3>If-Else</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P3_IfElse.php'; echo '</div>';
                    break;
                case 'kondisi-elseif':
                    echo '<h3>If-Else if-Else</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P3_IfElseifElse.php'; echo '</div>';
                    break;
                case 'kondisi-switch':
                    echo '<h3>Switch</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P3_Switch.php'; echo '</div>';
                    break;
                case 'while-loop':
                    echo '<h3>While Loop</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P3_WhileLoop.php'; echo '</div>';
                    break;
                case 'do-while':
                    echo '<h3>Do While Loop</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P3_DoWhileLoop.php'; echo '</div>';
                    break;
                case 'for-loop':
                    echo '<h3>For Loop</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P3_ForLoop.php'; echo '</div>';
                    break;
                case 'foreach-loop':
                    echo '<h3>For Each Loop</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P3_ForEachLoop.php'; echo '</div>';
                    break;

                // --- PERTEMUAN 4---
                case 'p4-builtin':
                    echo '<h3>Built-In Function</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P4_BuiltIn.php'; echo '</div>';
                    break;  
                case 'p4-function1':
                    echo '<h3>Function Sendiri</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P4_Function1.php'; echo '</div>';
                    break; 
                 case 'p4-function2':
                    echo '<h3>Function dengan Parameter</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P4_Function2.php'; echo '</div>';
                    break;  
                 case 'p4-optional-argu':
                    echo '<h3>Function Optional Argument</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P4_OptionalArgu.php'; echo '</div>';
                    break;   
                 case 'p4-call-by-value':
                    echo '<h3>Function Call by Value</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P4_CallByValue.php'; echo '</div>';
                    break; 
                 case 'p4-call-by-reference':
                    echo '<h3>Function Call by Reference</h3><p>Hasil Output :</p>';
                    echo '<div class="code-output">'; include 'P4_CallByReference.php'; echo '</div>';
                    break;            
                    
                // --- PERTEMUAN 5 ---
                case 'p5-form-biodata':
                    echo '<h3>Latihan Form Biodata</h3><p>Form inputan untuk membuat biodata</p>';
                    echo '<div class="code-output">'; include 'P5_FormBiodata.php'; echo '</div>';
                    break;
                case 'p5-konversi-nilai':
                    echo '<h3>Latihan Konversi Nilai</h3><p>Form inputan untuk mengkonversi nilai angka ke nilai huruf</p>';
                    echo '<div class="code-output">'; include 'P5_KonversiNilai.php'; echo '</div>';
                    break;
                case 'p5-generator-tabel':
                    echo '<h3>Latihan Generator Tabel</h3><p>Form inputan untuk membuat tabel otomatis sesuai angka yang diinput</p>';
                    echo '<div class="code-output">'; include 'P5_BuatTabel.php'; echo '</div>';
                    break;

                default:
                    echo '<h3>404</h3><p>Halaman tidak ditemukan.</p>';
                    break;
            }
            ?>
        </div>
    </main>

</div>

<?php 

include 'footer.php'; 
?>