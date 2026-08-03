<?php
$nilai_angka = "";
$nilai_huruf = "";
$status_konversi = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['proses_nilai']))
{
    $nilai_angka = isset($_POST['nilai_angka']) ? floatval($_POST['nilai_angka']) : 0;
    $status_konversi = true;

    if ($nilai_angka >= 85 && $nilai_angka <= 100) {
        $nilai_huruf = "A";
    } elseif ($nilai_angka >= 70 && $nilai_angka <= 84) {
        $nilai_huruf = "B";
    } elseif ($nilai_angka >= 60 && $nilai_angka < 70) {
        $nilai_huruf = "C";
    } elseif ($nilai_angka >= 50 && $nilai_angka < 60) {
        $nilai_huruf = "D";
    } else {
        $nilai_huruf = "E";
    }
}
?>

<style>
    .p5-nilai-container {
        max-width: 450px;
        background-color: #ffffff;
        border: 1px solid #fcd1e1;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 4px 10px rgba(209, 71, 122, 0.02);
        margin-top: 10px;
        font-family: inherit; 
    }

    .p5-form-group {
        margin-bottom: 20px;
    }

    .p5-form-group label {
        display: block;
        font-weight: 600;
        color: #d1477a;
        margin-bottom: 10px;
        font-size: 1rem;
    }

    .p5-input-nilai {
        width: 100%;
        padding: 12px;
        border: 1px solid #fcd1e1;
        border-radius: 8px;
        outline: none;
        font-size: 1rem;
        font-family: inherit;
        color: #4a4a4a;
        background-color: #fff9fb;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .p5-input-nilai:focus {
        border-color: #d1477a;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(209, 71, 122, 0.1);
    }

    .p5-nilai-btn-group {
        display: flex;
        gap: 12px;
    }

    .p5-btn-konversi {
        flex: 2;
        background-color: #d1477a;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        font-family: inherit;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .p5-btn-konversi:hover {
        background-color: #b03a64;
    }

    .p5-btn-nilai-reset {
        flex: 1;
        background-color: #f5f5f5;
        color: #666666;
        border: 1px solid #cccccc;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .p5-btn-nilai-reset:hover {
        background-color: #e8e8e8;
        color: #333333;
    }

    .p5-nilai-output-box {
        margin-top: 25px;
        padding: 20px;
        background-color: #fff6f9;
        border-left: 4px solid #d1477a;
        border-radius: 8px;
    }

    .p5-nilai-output-box strong {
        color: #d1477a;
        font-size: 0.95rem;
        display: block;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .p5-text-result {
        font-size: 1.1rem;
        margin: 8px 0;
        color: #d1477a;
        display: flex;
        align-items: center;
    }

    .p5-grade-badge {
        font-size: 1.1rem;
        font-weight: 700;
        color: #d1477a;
        margin-left: 8px;
    }
</style>

<div class="p5-nilai-container">
    <form method="POST" action="">
        <div class="p5-form-group">
            <label for="nilai_angka">Masukkan Nilai Angka :</label>
            <input type="number" step="any" min="0" max="100" name="nilai_angka" id="nilai_angka" class="p5-input-nilai" value="<?php echo $nilai_angka; ?>" required placeholder="Contoh: 85" autocomplete="off">
        </div>
        
        <div class="p5-nilai-btn-group">
            <button type="submit" name="proses_nilai" class="p5-btn-konversi">Konversi Nilai</button>
            <button type="reset" class="p5-btn-nilai-reset" onclick="handleResetNilai()">Reset</button>
        </div>
    </form>

    <div id="p5-nilai-output-wrapper">
        <?php if ($status_konversi): ?>
            <div class="p5-nilai-output-box">
                <strong>Hasil Konversi Ke Layar:</strong>
                <div class="p5-text-result">Nilai Angka: <b style="margin-left: 6px; color: #d1477a;"><?php echo $nilai_angka; ?></b></div>
                <div class="p5-text-result">Nilai Huruf: <span class="p5-grade-badge"><?php echo $nilai_huruf; ?></span></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function handleResetNilai() {
    document.getElementById('p5-nilai-output-wrapper').innerHTML = '';
}
</script>