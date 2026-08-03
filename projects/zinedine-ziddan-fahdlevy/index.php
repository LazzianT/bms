<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Pemrograman Web Lanjut - Zinedine Ziddan Fahdlevy</title>
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="style.css?v=9">
</head>

<body>
    <div class="app-container">
        <?php include 'component/appbar.php'; ?>
        <div class="main-container">
            <?php include 'component/sidebar.php'; ?>
            
            <div class="content">
                <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 'hello-world';
                
                $page = str_replace(['../', '..\\', './'], '', $page);
                
                $file_path = $page . '.php';
                
                if (file_exists($file_path) && !is_dir($file_path) && $page !== 'index') {
                    include $file_path;
                } else {
                    echo "<div class='content-card'>";
                    echo "<h2>Error 404</h2>";
                    echo "<p>Halaman '$page' tidak ditemukan.</p>";
                    echo "</div>";
                }
                ?>
                <div class="footer">
                    <p>&copy; 2026 Zinedine Ziddan Fahdlevy - Pemrograman Web Lanjut</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
