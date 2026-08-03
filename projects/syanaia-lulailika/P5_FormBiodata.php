<?php
$nama = $umur = $gender = $hobi = $pendidikan = $alamat = "";
$tampilkan = false;

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $nama = isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '';
    $umur = isset($_POST['umur']) ? htmlspecialchars($_POST['umur']) : '';
    $gender = isset($_POST['gender']) ? htmlspecialchars($_POST['gender']) : '';
    
    $hobi_array = isset($_POST['hobi']) ? $_POST['hobi'] : [];
    $hobi = !empty($hobi_array) ? implode(", ", $hobi_array) : "Tidak Ada";
    
    $pendidikan = isset($_POST['pendidikan']) ? htmlspecialchars($_POST['pendidikan']) : '';
    $alamat = isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : '';
    $tampilkan = true;
}
?>

<style>
    .p5-layout-container {
        display: flex;
        gap: 25px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .p5-card-form, .p5-card-output {
        flex: 1;
        min-width: 300px;
        background-color: #ffffff;
        border: 1px solid #fcd1e1;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(209, 71, 122, 0.02);
    }

    .p5-table {
        width: 100%;
        border-collapse: collapse;
    }

    .p5-table td {
        padding: 10px 6px;
        vertical-align: middle;
        font-size: 0.95rem;
    }

    .p5-table td:first-child {
        width: 110px;
        font-weight: 600;
        color: #4a4a4a;
    }

    .p5-table td:nth-child(2) {
        width: 15px;
        color: #d1477a;
    }

    .p5-input-text, .p5-select, .p5-textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #fcd1e1;
        border-radius: 6px;
        outline: none;
        font-size: 0.9rem;
        background-color: #fff9fb;
        transition: border-color 0.2s;
    }

    .p5-input-text:focus, .p5-select:focus, .p5-textarea:focus {
        border-color: #d1477a;
        background-color: #ffffff;
    }

    .p5-radio-group, .p5-checkbox-group {
        display: inline-flex;
        align-items: center;
        gap: 15px;
        font-size: 0.9rem;
    }

    .p5-radio-group input, .p5-checkbox-group input {
        accent-color: #d1477a;
        cursor: pointer;
    }

    .p5-btn-group {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .p5-btn-submit {
        background-color: #d1477a;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .p5-btn-submit:hover {
        background-color: #b03a64;
    }

    .p5-btn-reset {
        background-color: #f5f5f5;
        color: #666666;
        border: 1px solid #cccccc;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .p5-btn-reset:hover {
        background-color: #e8e8e8;
        color: #333333;
    }

    .p5-output-box {
        border: 2px dashed #f4b2cf;
        border-radius: 8px;
        padding: 15px;
        background-color: #fff6f9;
    }

    .p5-output-title {
        color: #d1477a;
        font-size: 1.25rem;
        margin-bottom: 15px;
        border-bottom: 2px solid #fcd1e1;
        padding-bottom: 5px;
    }

    .p5-empty-state {
        color: #aaaaaa;
        text-align: center;
        padding: 40px 0;
        font-style: italic;
        font-size: 0.95rem;
    }
</style>

<div class="p5-layout-container">
    <div class="p5-card-form">
        <form method="POST" action="">
            <table class="p5-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><input type="text" name="nama" class="p5-input-text" autocomplete="off" required placeholder="Masukkan nama lengkap"></td>
                </tr>
                <tr>
                    <td>Umur</td>
                    <td>:</td>
                    <td><input type="number" name="umur" class="p5-input-text" required placeholder="Contoh: 20"></td>
                </tr>
                <tr>
                    <td>Gender</td>
                    <td>:</td>
                    <td>
                        <div class="p5-radio-group">
                            <label><input type="radio" name="gender" value="Pria" required> Pria</label>
                            <label><input type="radio" name="gender" value="Wanita"> Wanita</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Hobi</td>
                    <td>:</td>
                    <td>
                        <div class="p5-checkbox-group">
                            <label><input type="checkbox" name="hobi[]" value="Travelling"> Travelling</label>
                            <label><input type="checkbox" name="hobi[]" value="Shopping"> Shopping</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Pendidikan</td>
                    <td>:</td>
                    <td>
                        <select name="pendidikan" class="p5-select">
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA/SMK">SMA/SMK</option>
                            <option value="S1">S1</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td><textarea name="alamat" rows="3" class="p5-textarea" required placeholder="Tuliskan alamat rumah..."></textarea></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        <div class="p5-btn-group">
                            <button type="submit" class="p5-btn-submit">OK</button>
                            <button type="reset" class="p5-btn-reset" onclick="handleResetForm()">Reset</button>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <div class="p5-card-output">
        <div id="p5-output-wrapper">
            <?php if ($tampilkan): ?>
                <div class="p5-output-box">
                    <h3 class="p5-output-title">Hasil Biodata</h3>
                    <table class="p5-table">
                        <tr><td>Nama</td><td>:</td><td style="font-weight: bold; color: #222;"><?php echo $nama; ?></td></tr>
                        <tr><td>Umur</td><td>:</td><td><?php echo $umur; ?> Tahun</td></tr>
                        <tr><td>Gender</td><td>:</td><td><?php echo $gender; ?></td></tr>
                        <tr><td>Hobi</td><td>:</td><td><?php echo $hobi; ?></td></tr>
                        <tr><td>Pendidikan</td><td>:</td><td><span style="background: #ffe4e1; color: #d1477a; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 0.85rem;"><?php echo $pendidikan; ?></span></td></tr>
                        <tr><td>Alamat</td><td>:</td><td style="line-height: 1.4; color: #555;"><?php echo $alamat; ?></td></tr>
                    </table>
                </div>
            <?php else: ?>
                <div class="p5-empty-state">
                    <p>Belum ada data yang dikirim.</p>
                    <p style="font-size: 0.8rem; margin-top: 5px;">Silakan isi form di sebelah kiri lalu klik OK.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function handleResetForm() {
    const outputWrapper = document.getElementById('p5-output-wrapper');
    
    outputWrapper.innerHTML = `
        <div class="p5-empty-state">
            <p>Belum ada data yang dikirim.</p>
            <p style="font-size: 0.8rem; margin-top: 5px;">Silakan isi form di sebelah kiri lalu klik OK.</p>
        </div>
    `;
}
</script>