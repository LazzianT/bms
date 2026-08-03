<div class="content-card">
                    <div class="content-title">
                        <h2>Operator Pembandingan</h2>
                    </div>
                    
                    <div class="content-body">
                        <?php
                            $bill = 100;
                            $bil2 = 20;
                            $teks1 = "PHP";
                            $teks2 = "php";
                            printf("%d == %d hasilnya %d<br/>",$bill, $bil2, $bill == $bil2);
                            printf("%d != %d hasilnya %d<br/>",$bill, $bil2, $bill != $bil2);
                            printf("%d >= %d hasilnya %d<br/>",$bill, $bil2, $bill >= $bil2);
                            printf("%s == %s hasilnya %d<br/>",$teks1, $teks2, $teks1 == $teks2);
                            printf("%s != %s hasilnya %d <br/>",$teks1, $teks2, $teks1 != $teks2);
                        ?>
                    </div>
                </div>
