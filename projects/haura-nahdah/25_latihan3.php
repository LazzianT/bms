<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Membuat Tabel</title>
</head>
<body>

<!-- FORM INPUT -->
<form method="post">
<table border="1" cellpadding="6" style="width:700px;" align="center">
    <tr>
        <td colspan="2"><b><center>Form Pembuatan Tabel</center></b></td>
    </tr>
    <tr>
        <td>Masukkan Jumlah Baris</td>
        <td><input type="text" name="baris" style="width:99%;"></td>
    </tr>
    <tr>
        <td>Masukkan Jumlah Kolom</td>
        <td><input type="text" name="kolom" style="width:99%;"></td>
    </tr>
    <tr>
        <td colspan="2" style="text-align:center;">
            <button type="submit">Create</button>
        </td>
    </tr>
</table>
</form>

<br>

<!-- OUTPUT TABEL -->
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $baris = (int) $_POST['baris'];
    $kolom = (int) $_POST['kolom'];

    echo "<b><center>Tabel Hasil :</center></b><br>";
    echo "<table border='1' cellpadding='5' align='center'>";

    // for loop luar = perulangan baris
    for ($i = 1; $i <= $baris; $i++) {

        echo "<tr>";

        // for loop dalam = perulangan kolom
        for ($j = 1; $j <= $kolom; $j++) {
            echo "<td>baris $i , kolom $j</td>";
        }

        echo "</tr>";
    }

    echo "</table>";
}
?>

</body>
</html>

