<?php
echo "<h3>Function dengan Optional Arguments</h3>";
function salamDefault($nama = "PHP") {
    echo "Halo " . $nama . "<br>";
}

salamDefault("Mahasiswa");
salamDefault();
?>