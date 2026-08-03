<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konversi Nilai Angka ke Huruf</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        .box { border: 1px solid #ccc; padding: 20px; width: 300px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="number"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 8px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; border-radius: 3px; }
        button:hover { background-color: #0056b3; }
        .result { margin-top: 20px; padding: 10px; border-left: 4px solid #28a745; background-color: #f8f9fa; }
    </style>
</head>
<body>

<div class="box">
    <h3>Form Konversi Nilai</h3>
    <form action="" method="POST">
        <div class="form-group">
            <label for="nilai">Masukkan Nilai Angka:</label>
            <input type="number" id="nilai" name="nilai_angka" min="0" max="100" required>
        </div>
        <button type="submit" name="proses">Konversi</button>
    </form>

    <?php
    if (isset($_POST['proses'])) {
        $nilai = floatval($_POST['nilai_angka']);
        $huruf = '';

        // Logika IF-ELSE sesuai range pada gambar
        if ($nilai >= 85 && $nilai <= 100) {
            $huruf = 'A';
        } elseif ($nilai >= 70 && $nilai <= 84) {
            $huruf = 'B';
        } elseif ($nilai >= 60 && $nilai < 70) {
            $huruf = 'C';
        } elseif ($nilai >= 50 && $nilai < 60) {
            $huruf = 'D';
        } elseif ($nilai < 50 && $nilai >= 0) {
            $huruf = 'E';
        } else {
            $huruf = 'Tidak Valid (Di luar range 0-100)';
        }

        echo "<div class='result'>";
        echo "Nilai Angka: <strong>$nilai</strong><br>";
        echo "Hasil Konversi Huruf: <strong>$huruf</strong>";
        echo "</div>";
    }
    ?>
</div>

</body>
</html>