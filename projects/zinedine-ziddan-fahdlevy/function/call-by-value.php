<div class="content-card">
    <div class="content-title">
        <h2>Function Call by Value</h2>
    </div>
    
    <div class="content-body">
        <?php
            function jumlahkan($x, $y) {
                $hasil = $x + $y;
                return $hasil;
            }

            echo "Hasilnya = " . jumlahkan(10, 2) . "<br>";
            $bil = 0;
            $bil = jumlahkan(9, 9);
            echo "Hasilnya = " . $bil;
        ?>
    </div>
</div>