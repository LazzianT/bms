<?php
$murid = new \stdClass;
$murid->nama = "Upin";
$murid->usia = 5;
$murid->hobi = array("membaca","mewarnai");

echo "$murid->nama berusia $murid->usia tahun <br/>";
echo "Hobinya : ";
echo $murid->hobi[0];
echo " dan ";
echo $murid->hobi[1];
?>