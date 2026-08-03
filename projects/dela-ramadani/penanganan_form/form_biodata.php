<style>
    /* Styling khusus form agar terlihat rapi dan tidak kaku */
    .form-box {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 25px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
        color: #495057;
    }
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-family: inherit;
        font-size: 14px;
    }
    .form-control:focus {
        border-color: #80bdff;
        outline: 0;
    }
    .radio-group, .checkbox-group {
        margin-top: 5px;
    }
    .radio-group label, .checkbox-group label {
        display: inline-block;
        font-weight: normal;
        margin-right: 15px;
        cursor: pointer;
    }
    .btn-submit {
        background-color: #deb1cf;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: background 0.2s;
    }
    .btn-submit:hover {
        background-color: #deb1cf;
    }
    .result-box {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 25px;
        color: #1d2124;
    }
    .result-box h3 {
        margin-bottom: 15px;
        border-bottom: 1px dashed #c1ccd7;
        padding-bottom: 5px;
    }
    .input-lainnya {
        width: 180px;
        padding: 4px 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 13px;
        margin-left: 5px;
        font-family: inherit;
    }
    .biodata-container h2{
       text-decoration: none;
       color: #deb1cf; 
       font-size: 30px;
    }
    .result-box h3{
        color: #deb1cf;
    }
</style>

<div class="biodata-container">
    <h2>Form Biodata</h2>
    
    <div class="form-box">
        <form action="" method="POST">
            <div class="form-group">
                <label>Nama :</label>
                <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama lengkap">
            </div>
            
            <div class="form-group">
                <label>Umur :</label>
                <input type="number" name="umur" class="form-control" required placeholder="Masukkan umur">
            </div>
            
            <div class="form-group">
                <label>Gender :</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="Pria" required> Pria</label>
                    <label><input type="radio" name="gender" value="Wanita"> Wanita</label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Hobi :</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="hobi[]" value="Travelling"> Travelling</label><br>
                    <label><input type="checkbox" name="hobi[]" value="Shopping"> Shopping</label><br>
                    <label style="display: inline-flex; align-items: center;">
                        <input type="checkbox" name="hobi_lainnya_aktif" value="Ya"> Lainnya: 
                        <input type="text" name="hobi_lainnya_text" class="input-lainnya" placeholder="Ketik Hobi Lainnya...">
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Pendidikan :</label>
                <select name="pendidikan" class="form-control" required>
                    <option value="">-- Pilih Pendidikan --</option>
                    <option value="SD">SD</option>
                    <option value="SMP">SMP</option>
                    <option value="SMA/SMK">SMA/SMK</option>
                    <option value="D3">D3</option>
                    <option value="S1">S1</option>
                    <option value="S2">S2</option>
                    <option value="S3">S3</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Alamat :</label>
                <textarea name="alamat" class="form-control" rows="3" required placeholder="Masukkan alamat lengkap"></textarea>
            </div>
            
            <button type="submit" name="proses" class="btn-submit">OK</button>
        </form>
    </div>

    <?php
    if (isset($_POST['proses'])) {
        $nama = $_POST['nama'];
        $umur = $_POST['umur'];
        $gender = $_POST['gender'];
        $pendidikan = $_POST['pendidikan'];
        $alamat = $_POST['alamat'];
        $daftar_hobi = [];

        if (isset($_POST['hobi'])) {
            $daftar_hobi = $_POST['hobi']; 
        }

        if (isset($_POST['hobi_lainnya_aktif']) && !empty(trim($_POST['hobi_lainnya_text']))) {
            $daftar_hobi[] = htmlspecialchars(trim($_POST['hobi_lainnya_text'])); 
        }
        if (!empty($daftar_hobi)) {
            $hobi_tampil = implode(", ", $daftar_hobi);
        } else {
            $hobi_tampil = "-";
        }
        
        echo "<div class='result-box'>";
        echo "<h3>Biodata</h3>";
        echo "<table class='table-biodata'>";
        echo "<tr><td width='30%'><strong>Nama</strong></td><td>: $nama</td></tr>";
        echo "<tr><td><strong>Umur</strong></td><td>: $umur Tahun</td></tr>";
        echo "<tr><td><strong>Gender</strong></td><td>: $gender</td></tr>";
        echo "<tr><td><strong>Hobi</strong></td><td>: $hobi_tampil</td></tr>";
        echo "<tr><td><strong>Pendidikan</strong></td><td>: $pendidikan</td></tr>";
        echo "<tr><td><strong>Alamat</strong></td><td>: $alamat</td></tr>";
        echo "</table>";
        echo "</div>";
    }
    ?>
</div>