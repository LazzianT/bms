<div class="content-card">
                    <h2>Hasil Method POST Terpisah</h2>
                    
                    <?php   
                        if(isset($_POST["username"])){
                            $username = $_POST["username"];
                            $umur = $_POST["umur"];
                            echo "<div class='result-box'>";
                            echo "Nama anda adalah <strong>$username</strong> dan umur anda <strong>$umur</strong> Tahun";
                            echo "</div>";
                        } else {
                            echo "<div class='result-box'>Tidak ada data yang dikirim.</div>";
                        }
                    ?>
                </div>
