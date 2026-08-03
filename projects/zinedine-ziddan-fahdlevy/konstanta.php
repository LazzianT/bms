<div class="content-card">
                    <div class="content-title">
                        <h2>Konstanta</h2>
                    </div>
                    
                    <div class="content-body">
                        <?php
                            define ('JUDUL', 'Hitung Luas Lingkaran');
                            define ("PHI", 3.14) ;
                            echo JUDUL;
                            $r = 10;
                            echo "<br/>Jari-jari : $r<br/>";
                            $luas= PHI * $r * $r;
                            echo "Luas Lingkaran = $luas";
                        ?>
                    </div>
                </div>
