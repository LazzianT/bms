<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Membuat Tabel Dinamis</title>
    <style>
        .container { display: flex; gap: 40px; font-family: sans-serif; }
        .box { border: 1px solid #999; padding: 20px; min-width: 300px; min-height: 200px; }
        .title { font-weight: bold; font-size: 1.1em; margin-bottom: 15px; }
        .form-group { margin-bottom: 10px; display: flex; }
        .label { width: 100px; }
        .colon { width: 15px; }
        input[type="number"] { width: 150px; }
        button { margin-left: 115px; padding: 2px 10px; cursor: pointer; }
        
        /* Style untuk tabel hasil */
        table { border-collapse: collapse; margin-top: 10px; }
        td { border: 1px solid #777; padding: 4px 8px; font-size: 0.9em; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <div class="box">
        <div class="title">Membuat Tabel</div>
        <form action="" method="POST">
            <div class="form-group">
                <span class="label">Jumlah Baris</span><span class="colon">:</span>
                <input type="number" name="baris" min="1" required>
            </div>
            <div class="form-group">
                <span class="label">Jumlah Kolom</span><span class="colon">:</span>
                <input type="number" name="kolom" min="1" required>
            </div>
            <button type="submit" name="create">Create</button>
        </form>
    </div>

    <div class="box">
        <div class="title">Tabel hasil :</div>
        <?php
        if (isset($_POST['create'])) {
            $jumlah_baris = intval($_POST['baris']);
            $jumlah_kolom = intval($_POST['kolom']);

            echo "<table>";
            // Perulangan untuk Baris (tr)
            for ($i = 1; $i <= $jumlah_baris; $i++) {
                // echo "tr";
                // Perulangan untuk Kolom (td) di dalam setiap baris
                for ($j = 1; $j <= $jumlah_kolom; $j++) {
                    echo "<td>baris $i , kolom $j</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
    </div>
</div>

</body>
</html>