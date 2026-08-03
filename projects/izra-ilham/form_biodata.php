<style>
    .lat1-container { display: flex; gap: 20px; flex-wrap: wrap; text-align: left; margin-top: 10px; }
    .lat1-box { border: 1px solid #cbd5e1; padding: 15px; width: 48%; min-width: 280px; background-color: #fff; border-radius: 6px; box-sizing: border-box; }
    .lat1-box h3 { margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #334155; padding-bottom: 5px; color: #1e293b; font-size: 15px; }
    .lat1-table { width: 100%; border-collapse: collapse; }
    .lat1-table td { padding: 6px 2px; vertical-align: top; color: #334155; font-size: 13px; }
    .lat1-table td:first-child { width: 90px; font-weight: 600; }
    .lat1-input, .lat1-select, .lat1-textarea { width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; font-family: inherit; }
    .lat1-textarea { height: 60px; resize: none; }
    .lat1-btn-group { display: flex; gap: 10px; }
    .lat1-btn { padding: 6px 20px; cursor: pointer; font-weight: bold; background-color: #3b82f6; color: white; border: none; border-radius: 4px; }
    .lat1-btn:hover { background-color: #2563eb; }
    .lat1-btn-reset { padding: 6px 20px; cursor: pointer; font-weight: bold; background-color: #ef4444; color: white; border: none; border-radius: 4px; font-size: 13px; }
    .lat1-btn-reset:hover { background-color: #dc2626; }
</style>

<div class="lat1-container">
    <div class="lat1-box">
        <h3>Input Data</h3>
        <form id="biodataForm" action="" method="POST">
            <table class="lat1-table">
                <tr>
                    <td>Nama</td>
                    <td><input type="text" name="nama" id="formNama" class="lat1-input" required value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"></td>
                </tr>
                <tr>
                    <td>Umur</td>
                    <td><input type="text" name="umur" id="formUmur" class="lat1-input" required value="<?php echo isset($_POST['umur']) ? htmlspecialchars($_POST['umur']) : ''; ?>"></td>
                </tr>
                <tr>
                    <td>Gender</td>
                    <td>
                        <input type="radio" name="gender" value="Pria" id="pria" required <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Pria') ? 'checked' : ''; ?>> <label for="pria">Pria</label><br>
                        <input type="radio" name="gender" value="Wanita" id="wanita" required <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Wanita') ? 'checked' : ''; ?>> <label for="wanita">Wanita</label>
                    </td>
                </tr>
                <tr>
                    <td>Hobi</td>
                    <td>
                        <?php $hobbies = isset($_POST['hobi']) ? $_POST['hobi'] : []; ?>
                        <input type="checkbox" name="hobi[]" class="formHobi" value="Travelling" id="h1" <?php echo in_array('Travelling', $hobbies) ? 'checked' : ''; ?>> <label for="h1">Travelling</label><br>
                        <input type="checkbox" name="hobi[]" class="formHobi" value="Shopping" id="h2" <?php echo in_array('Shopping', $hobbies) ? 'checked' : ''; ?>> <label for="h2">Shopping</label>
                    </td>
                </tr>
                <tr>
                    <td>Pendidikan</td>
                    <td>
                        <select name="pendidikan" id="formPendidikan" class="lat1-select">
                            <?php
                            $opsi = ['SD', 'SMP', 'SMA', 'S1'];
                            $terpilih = isset($_POST['pendidikan']) ? $_POST['pendidikan'] : 'SD';
                            foreach ($opsi as $o) {
                                $sel = ($o == $terpilih) ? 'selected' : '';
                                echo "<option value='$o' $sel>$o</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td><textarea name="alamat" id="formAlamat" class="lat1-textarea" required><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <div class="lat1-btn-group">
                            <button type="submit" name="submit_lat1" class="lat1-btn">OK</button>
                            <button type="button" class="lat1-btn-reset" onclick="resetFormLatihan()">Reset</button>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <div class="lat1-box" id="outputBox">
        <h3>Biodata</h3>
        <div id="outputContent">
            <?php
            if (isset($_POST['submit_lat1'])) {
                $nama = htmlspecialchars($_POST['nama']);
                $umur = htmlspecialchars($_POST['umur']);
                $gender = isset($_POST['gender']) ? $_POST['gender'] : '-';
                $hobi = isset($_POST['hobi']) ? implode(', ', $_POST['hobi']) : '-';
                $pendidikan = $_POST['pendidikan'];
                $alamat = nl2br(htmlspecialchars($_POST['alamat']));

                echo "<table class='lat1-table'>";
                echo "<tr><td>Nama</td><td>: $nama</td></tr>";
                echo "<tr><td>Umur</td><td>: $umur</td></tr>";
                echo "<tr><td>Gender</td><td>: $gender</td></tr>";
                echo "<tr><td>Hobi</td><td>: $hobi</td></tr>";
                echo "<tr><td>Pendidikan</td><td>: $pendidikan</td></tr>";
                echo "<tr><td>Alamat</td><td>: $alamat</td></tr>";
                echo "</table>";
            } else {
                echo "<p style='color: #64748b; font-style: italic; font-size: 13px; margin: 0;'>Belum ada data. Silakan isi form di sebelah kiri lalu klik OK.</p>";
            }
            ?>
        </div>
    </div>
</div>

<script>
function resetFormLatihan() {
    // 1. Kosongkan input text & textarea
    document.getElementById('formNama').value = '';
    document.getElementById('formUmur').value = '';
    document.getElementById('formAlamat').value = '';
    
    // 2. Kosongkan pilihan Radio Button (Gender dari awal kosong)
    document.getElementById('pria').checked = false;
    document.getElementById('wanita').checked = false;
    
    // 3. Kosongkan pilihan Checkbox Hobi
    var checkboxes = document.getElementsByClassName('formHobi');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = false;
    }
    
    // 4. Kembalikan seleksi pendidikan ke pilihan pertama (SD)
    document.getElementById('formPendidikan').selectedIndex = 0;
    
    // 5. Kembalikan teks box kanan menjadi kosong/default tanpa memicu pindah halaman
    document.getElementById('outputContent').innerHTML = "<p style='color: #64748b; font-style: italic; font-size: 13px; margin: 0;'>Belum ada data. Silakan isi form di sebelah kiri lalu klik OK.</p>";
}
</script>