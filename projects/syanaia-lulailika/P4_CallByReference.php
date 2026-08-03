<?php
   function nilaiKuadrat(&$nilai)
   {
      $nilai = $nilai * $nilai;
   }

   $bil = 3;

   echo "Nilai = " . $bil;
   echo "<br>";

   nilaiKuadrat($bil);

   echo "Nilai = " . $bil;
?>