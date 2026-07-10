<?php

$dir = __DIR__ . '/database/migrations';
$files = scandir($dir);

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $content = file_get_contents($dir . '/' . $file);
        
        $creates = [];
        preg_match_all("/Schema::create\(['\"]([^'\"]+)['\"]/", $content, $creates);
        
        $alters = [];
        preg_match_all("/Schema::table\(['\"]([^'\"]+)['\"]/", $content, $alters);
        
        echo "File: $file\n";
        if (!empty($creates[1])) {
            echo "  Creates: " . implode(', ', $creates[1]) . "\n";
        }
        if (!empty($alters[1])) {
            echo "  Alters: " . implode(', ', $alters[1]) . "\n";
        }
    }
}
