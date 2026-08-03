<?php

$exclude_files = [
    '/Applications/MAMP/htdocs/web lanjut/tugas/component/appbar.php',
    '/Applications/MAMP/htdocs/web lanjut/tugas/component/sidebar.php',
    '/Applications/MAMP/htdocs/web lanjut/tugas/view.php',
    '/Applications/MAMP/htdocs/web lanjut/tugas/biodata.php',
    '/Applications/MAMP/htdocs/web lanjut/tugas/update_files.php'
];


function process_file($file) {
    global $exclude_files;
    
    if (in_array($file, $exclude_files)) {
        echo "Skipping: " . basename($file) . "\n";
        return;
    }

    $content = file_get_contents($file);
    
    
    if (strpos($content, 'style.css') !== false) {
        echo "Already updated: " . basename($file) . "\n";
        return;
    }
    
    
    $content = preg_replace('/<style>.*?<\/style>/s', '', $content);
    
    
    $content = preg_replace('/(<title>.*?<\/title>)/', '$1<link rel="stylesheet" href="' . get_relative_path($file, '/Applications/MAMP/htdocs/web lanjut/tugas/style.css') . '">', $content);
    
    
    $footer = '        <div class="footer">
            <p>&copy; 2026 Zinedine Ziddan Fahdlevy - Pemrograman Web Lanjut</p>
        </div>';
    $content = str_replace('    </div>', $footer . '
    </div>', $content);
    
    file_put_contents($file, $content);
    echo "Updated: " . basename($file) . "\n";
}


function get_relative_path($from, $to) {
    $from_dir = dirname($from);
    $to_dir = dirname($to);
    $from_parts = explode(DIRECTORY_SEPARATOR, rtrim($from_dir, DIRECTORY_SEPARATOR));
    $to_parts = explode(DIRECTORY_SEPARATOR, rtrim($to_dir, DIRECTORY_SEPARATOR));
    
    while (count($from_parts) && count($to_parts) && ($from_parts[0] == $to_parts[0])) {
        array_shift($from_parts);
        array_shift($to_parts);
    }
    
    $result = str_repeat('../', count($from_parts)) . implode('/', $to_parts) . '/' . basename($to);
    return $result;
}


$files = glob('/Applications/MAMP/htdocs/web lanjut/tugas*.php');
$files = array_merge($files, glob('/Applications/MAMP/htdocs/web lanjut/tugas/*.php'));


foreach ($files as $file) {
    process_file($file);
}
echo "\nAll files processed!";
?>