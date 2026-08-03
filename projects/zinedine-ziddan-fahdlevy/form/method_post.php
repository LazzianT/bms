<div class="content-card">
                    <h2>Method POST</h2>
                    
                    <div class="form-box">
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="username">Username :</label>
                                <input type="text" id="username" name="username" class="form-control" required placeholder="Masukkan username">
                            </div>
                            
                            <div class="form-group">
                                <label for="umur">Umur :</label>
                                <input type="number" id="umur" name="umur" class="form-control" required placeholder="Masukkan umur">
                            </div>
                            
                            <button type="submit" class="btn-submit">Submit</button>
                        </form>
                    </div>

                    <?php   
                        if(isset($_POST["username"])){
                            $username = $_POST["username"];
                            $umur = $_POST["umur"];
                            
                            echo "<div class='result-box'>";
                            echo "Nama anda adalah <strong>$username</strong> dan umur anda <strong>$umur</strong> Tahun";
                            echo "</div>";
                        }
                    ?>
                </div>
