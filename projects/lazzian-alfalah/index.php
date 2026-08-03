<?php include_once 'header.php'; ?>

<div class="main-layout">
    
    <?php include_once 'sidebar.php'; ?>

    <div class="content-area">
        <?php
        // Default ke biodata jika parameter page kosong
        $page = isset($_GET['page']) ? $_GET['page'] : 'biodata';
        $file = $page . ".php"; 

        // Proteksi agar file sistem (layout) tidak ter-include berulang
        $sistem_files = ['header.php', 'sidebar.php', 'footer.php', 'home.php'];

        if (file_exists($file) && !in_array($file, $sistem_files)) {
            include_once $file; 
        } else {
            echo "<div class='placeholder-box'><h3 class='mono'>Error 404</h3><p>File <code>$file</code> tidak ditemukan!</p></div>";
        }
        ?>
    </div>

</div>

<?php include_once 'footer.php'; ?>