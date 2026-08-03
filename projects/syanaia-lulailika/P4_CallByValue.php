<?php
   function jumlahkan($x, $y)
   {
      $hasil = $x + $y;
      return $hasil;
   }

   echo "Hasilnya = " . jumlahkan(10, 2);
   echo "<br>";

   $bil = 0;
   $bil = jumlahkan(9, 9);

   echo "Hasilnya = " . $bil;
?>