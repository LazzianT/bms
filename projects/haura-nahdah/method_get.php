<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penanganan Form</title>
</head>
<body>
    <form action="" method="GET">
        Nama : <input type="text" name="nama" /><br />
        Umur : <input type="text" name="umur" /><br />
        <input type="submit" name = "ok" value="OK">
    </form>

    <?php
        if (isset($_GET["ok"])) {
            echo $_GET["nama"]."<br />";
            echo $_GET["umur"];
        }
    ?>
</body>
</html>