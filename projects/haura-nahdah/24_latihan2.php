<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konversi Nilai</title>
</head>
<body>

<!-- FORM INPUT -->
<table border="1" cellpadding="5" style="width:600px;" align="center">
    <tr>
        <td colspan="2" align="center"><b>Table Konversi Nilai</b></td>
    </tr>
    <tr>
        <td style="vertical-align:middle;">Nilai Angka</td>
        <td style="vertical-align:middle;"> <input type="text" id="nilai" style="width:99%;"></td>
    </tr>
    <tr>
        <td colspan="2" align="center" style="vertical-align:middle; height:20px;">
            <button onclick="konversi()">Convert</button>
        </td>
    </tr>
</table>

<br>

<!-- OUTPUT HASIL -->
<div id="hasil" align="center"></div>

<script>
function konversi() {
    var nilai = parseInt(document.getElementById("nilai").value);
    var huruf = "";

    if (nilai >= 85 && nilai <= 100) {
        huruf = "A";
    } else if (nilai >= 70 && nilai <= 84) {
        huruf = "B";
    } else if (nilai >= 60 && nilai < 70) {
        huruf = "C";
    } else if (nilai >= 50 && nilai < 60) {
        huruf = "D";
    } else if (nilai < 50) {
        huruf = "E";
    } else {
        huruf = "Nilai tidak valid! Masukkan angka 0-100";
    }

    document.getElementById("hasil").innerHTML = "<b> Hasil konversi nilai ke huruf adalah : " + huruf + "</b>";
}
</script>

</body>
</html>