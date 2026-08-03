<?php
define("JUDUL", "Hitung Luas Lingkaran");
define("PHI", 3.14);


echo JUDUL . "<br>";
$r = 10;
echo "Jari-jari: $r <br>";
$luas = PHI * $r * $r;
echo "Luas Lingkaran: $luas";
?>