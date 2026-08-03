<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Biodata</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>

<div class="container">
    <div class="form-box">
        <h2><center>Form Biodata</h2><br>
        <form action="action1.php" method="post">

            <div class="form-group">
                <label>Nama</label>
                <span>:</span>
                <input type="text" name="nama" required>
            </div>

            <div class="form-group">
                <label>Umur</label>
                <span>:</span>
                <input type="number" name="umur" required>
            </div>

            <div class="form-group">
                <label>Gender</label>
                <span>:</span>

                <div class="option">
                    <input type="radio" name="gender" value="Pria" required> Pria <br>
                    <input type="radio" name="gender" value="Wanita"> Wanita
                </div>
            </div>

            <div class="form-group">
                <label>Hobi</label>
                <span>:</span>

                <div class="option">
                    <input type="checkbox" name="hobi[]" value="Travelling"> Travelling <br>
                    <input type="checkbox" name="hobi[]" value="Shopping"> Shopping
                </div>
            </div>

            <div class="form-group">
                <label>Pendidikan </label>
                <span>:</span>

                <select name="pendidikan">
                    <option>SD</option>
                    <option>SMP</option>
                    <option>SMA / SMK</option>
                    <option>S1</option>
                    <option>S2</option>
                </select>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <span>:</span>

                <textarea name="alamat"></textarea>
            </div>

            <button type="submit"><b>Submit</b></button>

        </form>

    </div>

</div>

</body>
</html>