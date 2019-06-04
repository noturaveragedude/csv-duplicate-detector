<?php
spl_autoload_register(function (String $class) {
    $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'CsvDuplicateDetector';
    $replaceRootPath = str_replace('CsvDuplicateDetector', $sourcePath, $class);
    $replaceDirectorySeparator = str_replace('\\', DIRECTORY_SEPARATOR, $replaceRootPath);
    $filePath = $replaceDirectorySeparator . '.php';
    if (file_exists($filePath)) {
        require($filePath);
    }
});
