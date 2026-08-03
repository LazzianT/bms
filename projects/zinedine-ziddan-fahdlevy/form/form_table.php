<div class="content-card">
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
