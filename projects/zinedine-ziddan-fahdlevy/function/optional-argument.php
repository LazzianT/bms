<div class="content-card">
                    <div class="content-title">
                        <h2>Function Optional Argument</h2>
                    </div>
                    
                    <div class="content-body">
                        <?php 
                            function salam($nama = "PHP") {
                                echo "Halo " . $nama . "<br>";
                            }

                            salam("Mahasiswa");
                            salam();
                        ?>
                    </div>
                </div>
