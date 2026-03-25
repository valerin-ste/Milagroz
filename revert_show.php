<?php
$filePath = 'c:/xampp/htdocs/Milagroz/resources/views/admin/empleados/show.blade.php';
$content = file_get_contents($filePath);

// Pattern to match the delete form block
$pattern = '/<form action="{{ route\(\'admin.documentos.destroy\', \$doc->id\) }}" method="POST" class="d-inline" onsubmit="return confirm\(\'¿Eliminar este archivo permanentemente\?\'\);">\s*@csrf @method\(\'DELETE\'\)\s*<button type="submit" class="btn btn-sm btn-light border shadow-sm" title="Eliminar">\s*<i class="fas fa-trash-alt text-danger"><\/i>\s*<\/button>\s*<\/form>/';

$newContent = preg_replace($pattern, '', $content);

if ($newContent !== null && $newContent !== $content) {
    file_put_contents($filePath, $newContent);
    echo "Successfully removed delete forms from show.blade.php\n";
} else {
    // Try other variant with slightly different confirmation message
    $pattern2 = '/<form action="{{ route\(\'admin.documentos.destroy\', \$doc->id\) }}" method="POST" class="d-inline" onsubmit="return confirm\(\'¿Eliminar archivo\?\'\);">\s*@csrf @method\(\'DELETE\'\)\s*<button type="submit" class="btn btn-sm btn-light border shadow-sm" title="Eliminar">\s*<i class="fas fa-trash-alt text-danger"><\/i>\s*<\/button>\s*<\/form>/';
    $newContent = preg_replace($pattern2, '', $content);
    if ($newContent !== null && $newContent !== $content) {
        file_put_contents($filePath, $newContent);
        echo "Successfully removed delete forms (variant 2) from show.blade.php\n";
    } else {
        echo "Could not find the delete form pattern in show.blade.php\n";
    }
}
?>
