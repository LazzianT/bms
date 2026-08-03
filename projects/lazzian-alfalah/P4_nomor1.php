<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Latihan Form Biodata</title>
    <style>
        .container { display: flex; gap: 40px; font-family: sans-serif; }
        .box { border: 1px solid #999; padding: 20px; width: 350px; min-height: 300px; }
        .form-group { margin-bottom: 10px; display: flex; align-items: flex-start; }
        .label { width: 90px; }
        .colon { width: 15px; }
        .input-field { flex: 1; }
        .radio-group, .checkbox-group { display: flex; flex-direction: column; }
        .radio-group label, .checkbox-group label { display: flex; align-items: center; margin-bottom: 5px; cursor: pointer; }
        .radio-group input, .checkbox-group input { margin-right: 5px; }
        textarea { width: 100%; height: 80px; resize: none; }
        select { width: 100px; }
        button { margin-left: 105px; padding: 2px 15px; cursor: pointer; }
        .result-title { font-weight: bold; font-size: 1.2em; margin-bottom: 15px; }
        .result-item { margin-bottom: 5px; display: flex; }
    </style>
</head>
<body>

<div class="container">
    <div class="box">
        <form action="" method="POST">
            <div class="form-group">
                <span class="label">Nama</span><span class="colon">:</span>
                <input type="text" name="nama" class="input-field" required>
            </div>
            
            <div class="form-group">
                <span class="label">Umur</span><span class="colon">:</span>
                <input type="text" name="umur" class="input-field" required>
            </div>
            
            <div class="form-group">
                <span class="label">Gender</span><span class="colon">:</span>
                <div class="input-field radio-group">
                    <label><input type="radio" name="gender" value="Pria" required> Pria</label>
                    <label><input type="radio" name="gender" value="Wanita"> Wanita</label>
                </div>
            </div>
            
            <div class="form-group">
                <span class="label">Hobi</span><span class="colon">:</span>
                <div class="input-field checkbox-group">
                    <label><input type="checkbox" name="hobi[]" value="Travelling"> Travelling</label>
                    <label><input type="checkbox" name="hobi[]" value="Shopping"> Shopping</label>
                </div>
            </div>
            
            <div class="form-group">
                <span class="label">Pendidikan</span><span class="colon">:</span>
                <select name="pendidikan" class="input-field">
                    <option value="SD">SD</option>
                    <option value="SMP">SMP</option>
                    <option value="SMA">SMA</option>
                    <option value="S1">S1</option>
                </select>
            </div>
            
            <div class="form-group">
                <span class="label">Alamat</span><span class="colon">:</span>
                <textarea name="alamat" class="input-field" required></textarea>
            </div>
            
            <button type="submit" name="submit">OK</button>
        </form>
    </div>

    <div class="box">
        <div class="result-title">Biodata</div>
        <?php
        if (isset($_POST['submit'])) {
            $nama = htmlspecialchars($_POST['nama']);
            $umur = htmlspecialchars($_POST['umur']);
            $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
            $pendidikan = $_POST['pendidikan'];
            $alamat = nl2br(htmlspecialchars($_POST['alamat']));
            
            // Handle checkbox hobi
            $hobi_list = isset($_POST['hobi']) ? implode(', ', $_POST['hobi']) : '-';

            echo "<div class='result-item'><span class='label'>Nama</span><span class='colon'>:</span><span>$nama</span></div>";
            echo "<div class='result-item'><span class='label'>Umur</span><span class='colon'>:</span><span>$umur</span></div>";
            echo "<div class='result-item'><span class='label'>Gender</span><span class='colon'>:</span><span>$gender</span></div>";
            echo "<div class='result-item'><span class='label'>Hobi</span><span class='colon'>:</span><span>$hobi_list</span></div>";
            echo "<div class='result-item'><span class='label'>Pendidikan</span><span class='colon'>:</span><span>$pendidikan</span></div>";
            echo "<div class='result-item'><span class='label'>Alamat</span><span class='colon'>:</span><span>$alamat</span></div>";
        }
        ?>
    </div>
</div>

</body>
</html>