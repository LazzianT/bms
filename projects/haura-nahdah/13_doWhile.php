<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas 3</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <table border="1" width="100%" cellpadding="15" cellspacing="6" align="center">
        
        <tr>
            <td colspan="2" align="center">
                <h2>202343501558 - Haura Nahdah - Y6K </h2>
            </td>
        </tr>
        
        <tr>
            <td width="25%" valign="top">
                <?php include "index.php"; ?>
            </td>

            <td width="95%" valign="top" align="center">
                <div class="browser-mockup">
                    <div class="browser-header">
                        <span class="dot red"></span>
                        <span class="dot yellow"></span>
                        <span class="dot green"></span>
                        <div class="address-bar">C:\xampp\htdocs\WebLanjut\TUGAS_PWL-3_Y6K_202343501558_HAURA</div>
                    </div>
                    <?php
                        // inisialisai nilai diawal 
                        $i = 0;

                        do {
                            // jalankan blok program
                            echo $i;
                            $i++;

                        } while ($i < 10);      // ulangi jika memenuhi syarat

                        //0123456789
                    ?> 
                </div>
            </td>
        </tr>
        
        <tr>
            <td colspan="2" align="center">
                <p><b>Tugas 3 Pemrograman Web Lanjut</b>
                <br><i>Copyright Haura Nahdah</p>
            </td>
        </tr>
        
    </table>

</body>
</html>
