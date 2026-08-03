<?php
$baris = 0;
$kolom = 0;
$buat_tabel = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generate_table']))
{
    $baris = isset($_POST['baris']) ? intval($_POST['baris']) : 0;
    $kolom = isset($_POST['kolom']) ? intval($_POST['kolom']) : 0;
    if ($baris > 0 && $kolom > 0) {
        $buat_tabel = true;
    }
}
?>

<style>
    .p5-tabel-layout {
        display: flex;
        gap: 25px;
        flex-wrap: wrap;
        margin-top: 10px;
        font-family: inherit;
    }

    .p5-tabel-card-form, .p5-tabel-card-output {
        flex: 1;
        min-width: 320px;
        background-color: #ffffff;
        border: 1px solid #fcd1e1;
        border-radius: 10px;
        padding: 22px;
        box-shadow: 0 4px 10px rgba(209, 71, 122, 0.02);
    }

    .p5-tabel-form {
        width: 100%;
        border-collapse: collapse;
    }

    .p5-tabel-form td {
        padding: 8px 4px;
        vertical-align: middle;
        font-size: 0.95rem;
    }

    .p5-tabel-form td:first-child {
        width: 110px;
        font-weight: 600;
        color: #4a4a4a;
    }

    .p5-tabel-form td:nth-child(2) {
        width: 15px;
        color: #d1477a;
    }

    .p5-tabel-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #fcd1e1;
        border-radius: 8px;
        outline: none;
        font-size: 0.95rem;
        font-family: inherit;
        background-color: #fff9fb;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .p5-tabel-input:focus {
        border-color: #d1477a;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(209, 71, 122, 0.1);
    }

    .p5-tabel-btn-group {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .p5-btn-create {
        background-color: #d1477a;
        color: white;
        border: none;
        padding: 11px 22px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        font-family: inherit;
        cursor: pointer;
        transition: background 0.2s;
    }

    .p5-btn-create:hover {
        background-color: #b03a64;
    }

    .p5-btn-tabel-reset {
        background-color: #f5f5f5;
        color: #666666;
        border: 1px solid #cccccc;
        padding: 11px 22px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.2s;
    }

    .p5-btn-tabel-reset:hover {
        background-color: #e8e8e8;
        color: #333333;
    }

    .p5-tabel-title {
        color: #d1477a;
        font-size: 1.15rem;
        margin-top: 0;
        margin-bottom: 15px;
        font-weight: 600;
        border-bottom: 2px solid #fcd1e1;
        padding-bottom: 6px;
    }

    .p5-generated-table {
        border-collapse: collapse;
        width: 100%;
        text-align: center;
        font-size: 0.85rem;
        margin-top: 5px;
    }

    .p5-generated-table td {
        border: 1px solid #fcd1e1;
        padding: 10px;
        color: #555555;
        background-color: #fffbfd;
        transition: background 0.2s;
    }

    .p5-generated-table tr:hover td {
        background-color: #fff0f5;
        color: #d1477a;
    }

    .p5-tabel-empty {
        color: #aaaaaa;
        text-align: center;
        padding: 45px 0;
        font-style: italic;
        font-size: 0.95rem;
    }
</style>

<div class="p5-tabel-layout">
    <div class="p5-tabel-card-form">
        <h4 style="margin-top: 0; color: #d1477a; margin-bottom: 15px;">Konfigurasi Ukuran</h4>
        <form method="POST" action="">
            <table class="p5-form-group p5-tabel-form">
                <tr>
                    <td>Jumlah Baris</td>
                    <td>:</td>
                    <td><input type="number" name="baris" min="1" class="p5-tabel-input" required placeholder="Contoh: 5" autocomplete="off"></td>
                </tr>
                <tr>
                    <td>Jumlah Kolom</td>
                    <td>:</td>
                    <td><input type="number" name="kolom" min="1" class="p5-tabel-input" required placeholder="Contoh: 3" autocomplete="off"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        <div class="p5-tabel-btn-group">
                            <button type="submit" name="generate_table" class="p5-btn-create">Create Table</button>
                            <button type="reset" class="p5-btn-tabel-reset" onclick="handleResetTabel()">Reset</button>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <div class="p5-tabel-card-output">
        <div id="p5-tabel-output-wrapper">
            <?php if ($buat_tabel): ?>
                <h4 class="p5-tabel-title">Tabel Hasil :</h4>
                <div style="overflow-x: auto;"> <table class="p5-generated-table">
                        <?php
                        for ($i = 1; $i <= $baris; $i++) {
                            echo "<tr>";
                            for ($j = 1; $j <= $kolom; $j++) {
                                echo "<td>baris $i, kolom $j</td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            <?php else: ?>
                <div class="p5-tabel-empty">
                    <p>Belum ada tabel yang dibuat.</p>
                    <p style="font-size: 0.8rem; margin-top: 5px;">Tentukan jumlah baris & kolom lalu klik Create Table.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function handleResetTabel() {
    document.getElementById('p5-tabel-output-wrapper').innerHTML = `
        <div class="p5-tabel-empty">
            <p>Belum ada tabel yang dibuat.</p>
            <p style="font-size: 0.8rem; margin-top: 5px;">Tentukan jumlah baris & kolom lalu klik Create Table.</p>
        </div>
    `;
}
</script>