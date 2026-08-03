<?php include 'header.php'; ?>

<div class="main-layout">
    
    <?php include 'sidebar.php'; ?>

    <div class="content-area">
        <?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            $file = $page . ".php"; 

            if (file_exists($file)) {
                include $file; 
            } else {
                echo "<div class='placeholder-box'><h3>File <code>$file</code> tidak ditemukan!</h3></div>";
            }
        } else {
            echo "
            <div class='placeholder-box'>
                <h3 style='margin-bottom: 0.5rem; color: #111;'>Sistem Siap Eksekusi (Mode INCLUDE)</h3>
                <p>Silakan pilih salah satu modul program di bilah kiri untuk menampilkan hasil eksekusi kode PHP.</p>
            </div>";
        }
        ?>
    </div>

</div>

<?php include 'footer.php'; ?>