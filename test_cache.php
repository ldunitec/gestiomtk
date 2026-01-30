<?php
$path = __DIR__ . '/bootstrap/cache';

echo "<h2>Prueba de permisos en: $path</h2>";

if (!is_dir($path)) {
    echo "<p style='color:red;'>❌ La carpeta no existe.</p>";
} else {
    echo "<p style='color:green;'>✅ La carpeta existe.</p>";

    if (is_writable($path)) {
        echo "<p style='color:green;'>✅ Laravel PUEDE escribir en bootstrap/cache.</p>";

        // Intentamos crear un archivo temporal
        $testFile = $path . '/test.txt';
        if (@file_put_contents($testFile, "probando escritura") !== false) {
            echo "<p style='color:green;'>✅ Se pudo crear un archivo de prueba.</p>";
            unlink($testFile);
        } else {
            echo "<p style='color:red;'>❌ No se pudo crear archivo dentro de bootstrap/cache.</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Laravel NO tiene permisos de escritura en bootstrap/cache.</p>";
    }
}
