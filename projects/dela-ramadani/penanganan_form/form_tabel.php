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
        margin-top: 25px;
    }
    .table-generated {
        width: 70%;
        margin-top: 15px;
        background-color: #ffffff;
    }
    .table-generated, .table-generated td {
        border: 2px solid #888888;
        padding: 10px;
        text-align: center;
        font-size: 14px;
    }
    .tabel-container h2{
        text-decoration: none;
        color: #deb1cf; 
        font-size: 25px;
        margin-bottom: 25px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
</style>

<div class="tabel-container">
    <h2>Membuat Tabel</h2>
    
    <div class="form-box">
        <form action="" method="POST">
            <div class="form-group">
                <label>Jumlah Baris :</label>
                <input type="number" name="jumlah_baris" class="form-control" min="1" required placeholder="Contoh: 5">
            </div>
            
            <div class="form-group">
                <label>Jumlah Kolom :</label>
                <input type="number" name="jumlah_kolom" class="form-control" min="1" required placeholder="Contoh: 3">
            </div>
            
            <button type="submit" name="create" class="btn-submit">Create</button>
        </form>
    </div>

    <?php
    if (isset($_POST['create'])) {
        $baris = $_POST['jumlah_baris'];
        $kolom = $_POST['jumlah_kolom'];

        echo "<div class='result-box'>";
        echo "<h3>Tabel Hasil ($baris Baris x $kolom Kolom) :</h3>";
        
        echo "<table class='table-generated'>";
        
        for ($i = 1; $i <= $baris; $i++) {
            echo "<tr>";
            
            for ($j = 1; $j <= $kolom; $j++) {
                echo "<td>baris $i, kolom $j</td>";
            }
            
            echo "</tr>";
        }
        
        echo "</table>";
        echo "</div>";
    }
    ?>
</div>