<?php
    $bill = 100;
    $bil2 = 20;
    $teks1 = "PHP";
    $teks2 = "php";

    printf("%d == %d hasilnya %d<br/>",$bill, $bil2, $bill == $bil2);
    //100 == 20 hasilnya 0
    printf("%d != %d hasilnya %d<br/>",$bill, $bil2, $bill != $bil2);
    //100 != 20 hasilnya 1
    printf("%d >= %d hasilnya %d<br/>",$bill, $bil2, $bill >= $bil2);
    //100 >= 20 hasilnya 1
    printf("%s == %s hasilnya %d<br/>",$teks1, $teks2, $teks1 == $teks2);
    //PHP == php hasilnya 0
    printf("%s != %s hasilnya %d<br/>",$teks1, $teks2, $teks1 != $teks2);
    //PHP != php hasilnya 1
?>