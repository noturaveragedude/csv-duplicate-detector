<h2>
    Upload CSV
</h2>
<form enctype="multipart/form-data" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <!-- <input type="hidden" name="MAX_FILE_SIZE" value="30000" /> -->
    <!-- Name of input element determines name in $_FILES array -->
    Check this file for duplicates: <input name="csv_file" required type="file" />
    <input type="submit" value="Send File" />
</form>


<?php
require_once 'autoload.php';

use CsvDuplicateDetector\CsvFile;

if (isset($_FILES['csv_file'])) {
    $file = file_get_contents($_FILES['csv_file']['tmp_name']);
    $csvFile = new CsvFile(base64_encode($file), ['BVN']);
    $values = $csvFile->checkForDuplicates();

    foreach ($values as $key => $value) {
        if ($value > 1) {
            echo "The BVN " . $key . " is duplicated " . $value . " times\n";
        }
    }
}
