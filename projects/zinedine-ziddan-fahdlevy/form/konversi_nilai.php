<div class="content-card">
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
