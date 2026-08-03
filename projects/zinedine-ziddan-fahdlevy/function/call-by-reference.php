<div class="content-card">
                    <div class="content-title">
                        <h2>Function Call by Reference</h2>
                    </div>
                    
                    <div class="content-body">
                        <?php
                            function tambahSatu(&$nilai) {
                                $nilai++;
                            }

                            $a = 10;
                            tambahSatu($a);
                            echo $a;
                        ?>
                    </div>
                </div>
