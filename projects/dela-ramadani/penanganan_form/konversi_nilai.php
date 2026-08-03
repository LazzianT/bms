<style>
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
        margin-bottom: 15px;
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
        background-color: #555;
    }
    .result-box {
        border-radius: 8px;
        padding: 25px;
        font-size: 16px;
        margin-top: 20px;
    }
    .bg-lulus {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    .bg-gagal {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    .score-badge {
        font-size: 24px;
        font-weight: bold;
    }
    .konversi-container h2{
        text-decoration: none;
        color: #deb1cf; 
        font-size: 25px;
        margin-bottom: 25px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
</style>

<div class="konversi-container">
    <h2>Konversi Nilai Angka ke Huruf</h2>
    
    <div class="form-box">
        <form action="" method="POST">
            <div class="form-group">
                <label>Masukkan Nilai Angka (0 - 100) :</label>
                <input type="number" name="nilai_angka" class="form-control" min="0" max="100" required placeholder="Contoh: 85">
            </div>
            <button type="submit" name="konversi" class="btn-submit">Konversi Nilai</button>
        </form>
    </div>

    <?php
    if (isset($_POST['konversi'])) {
        $nilai = $_POST['nilai_angka'];
        $nilai_huruf = "";
        $status = "";
        $kelas_css = "";

        if ($nilai >= 85 && $nilai <= 100) {
            $nilai_huruf = "A";
            $status = "Sangat Memuaskan";
            $kelas_css = "bg-lulus";
        } elseif ($nilai >= 70 && $nilai <= 84) {
            $nilai_huruf = "B";
            $status = "Memuaskan";
            $kelas_css = "bg-lulus";
        } elseif ($nilai >= 60 && $nilai < 70) {
            $nilai_huruf = "C";
            $status = "Cukup";
            $kelas_css = "bg-lulus";
        } elseif ($nilai >= 50 && $nilai < 60) {
            $nilai_huruf = "D";
            $status = "Kurang";
            $kelas_css = "bg-gagal";
        } else {
            $nilai_huruf = "E";
            $status = "Gagal / Mengulang";
            $kelas_css = "bg-gagal";
        }

        echo "<div class='result-box $kelas_css'>";
        echo "<h3>Hasil Konversi Nilai :</h3><br>";
        echo "Nilai      : <strong>$nilai</strong><br>";
        echo "Grade      : <span class='score-badge'>$nilai_huruf</span><br>";
        echo "Keterangan : <strong>$status</strong>";
        echo "</div>";
    }
    ?>
</div>