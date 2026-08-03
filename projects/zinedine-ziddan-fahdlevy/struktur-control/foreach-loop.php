<div class="content-card">
                    <div class="content-title">
                        <h2>Foreach Loop</h2>
                    </div>
                    
                    <div class="content-body">
                        <?php
                            $list_hari = array(
                                "Senin",
                                "Selasa",
                                "Rabu",
                                "Kamis",
                                "Jumat",
                                "Sabtu",
                                "Minggu"
                            );
                            foreach ($list_hari as $hari) {
                                echo $hari . ", ";
                            }
                        ?>
                    </div>
                </div>
