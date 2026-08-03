<?php
echo "<h3>Call By Reference</h3>";
function nilaiKuadrat(&$nilai) {
    $nilai = $nilai * $nilai;
}

$bil = 3;
echo "Nilai = " . $bil . "<br>";
nilaiKuadrat($bil);
echo "Nilai = " . $bil . "<br>";
?>