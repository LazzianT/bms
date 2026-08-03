<div class="content-card">
                    <h2>Method GET</h2>
                    
                    <div class="form-box">
                        <form action="" method="GET">
                            <div class="form-group">
                                <label for="username">Username :</label>
                                <input type="text" id="username" name="username" class="form-control" required placeholder="Masukkan username">
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Password :</label>
                                <input type="password" id="password" name="password" class="form-control" required placeholder="Masukkan password">
                            </div>
                            
                            <button type="submit" class="btn-submit">Login</button>
                        </form>
                    </div>

                    <?php   
                        if(isset($_GET["username"])){
                            $username = $_GET["username"];
                            $password = $_GET["password"];
                            
                            echo "<div class='result-box'>";
                            echo "Username : <strong>$username</strong> dengan password <strong>$password</strong> <br><br>";
                            
                            if($username == 'Admin' && $password == "rahasia") {
                                echo "<span class='score-badge bg-lulus'>ANDA BERHASIL LOGIN</span>";
                            } else {
                                echo "<span class='score-badge bg-gagal'>ANDA GAGAL LOGIN</span>";
                            }
                            
                            echo "</div>";
                        }
                    ?>
                </div>
