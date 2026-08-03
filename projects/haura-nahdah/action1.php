<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Biodata</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>

<?php
    $nama = $_POST['nama'];
    $umur = $_POST['umur'];
    $gender = $_POST['gender'];
    $pendidikan = $_POST['pendidikan'];
    $alamat = $_POST['alamat'];
    $hobi = "-";

    if(isset($_POST['hobi'])){
        $hobi = implode(", ", $_POST['hobi']);}
?>

<div class="hasil-box">
    <h2><center>Biodata <?= $nama ?></center></h2>
        <table>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><?= $nama ?></td>
            </tr>

            <tr>
                <td>Umur</td>
                <td>:</td>
                <td><?= $umur ?></td>
            </tr>

            <tr>
                <td>Gender</td>
                <td>:</td>
                <td><?= $gender ?></td>
            </tr>

            <tr>
                <td>Hobi</td>
                <td>:</td>
                <td><?= $hobi ?></td>
            </tr>
            <tr>
                <td>Pendidikan</td>
                <td>:</td>
                <td><?= $pendidikan ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td><?= $alamat ?></td>
            </tr>
        </table>
<br>

<a href="23_latihan1.php">
    <button><b>Kembali</b></button>
</a>

</div>

</body>
</html>