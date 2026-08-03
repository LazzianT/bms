<?php
    $murid = new \stdClass;
    $murid->nama = "Syanaia";
    $murid->usia = 22;
    $murid->hobi = array("membaca","menonton film");

    echo "$murid->nama berusia $murid->usia tahun <br/>";
    echo "Hobinya : ";
    echo $murid->hobi[0];
    echo " dan ";
    echo $murid->hobi[1];
?>