<div class="content-card">
                    <div class="content-title">
                        <h2>Object</h2>
                    </div>
                    
                    <div class="content-body">
                        <?php
                            $murid = new \stdClass;
                            $murid->nama = "Upin"; 
                            $murid->usia = 5;
                            $murid->hobi = array ("membaca", "mewarnai") ;
                            echo "$murid->nama berusia $murid->usia tahun <br/>";
                            echo "Hobinya : "; echo $murid->hobi[0];
                            echo " dan ";
                            echo $murid->hobi[1];
                        ?>
                    </div>
                </div>
