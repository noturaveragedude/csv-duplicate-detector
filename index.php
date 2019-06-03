<h2>
    Upload CSV
</h2>
<form enctype="multipart/form-data" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <!-- Name of input element determines name in $_FILES array -->
    Check this file for duplicates: <input name="csv_file" type="file" />
    <input type="submit" value="Send File" />
</form>

<?php 

if (isset($_FILES['csv_file'])) {
    // print_r($_FILES['csv_file']); die();
    $lines = file($_FILES['csv_file']['tmp_name'], FILE_IGNORE_NEW_LINES);
    
    $rows = array_map('str_getcsv', $lines);
    $header = array_shift($rows);
    $csv    = array();
    foreach($rows as $row) {
        $csv[] = array_combine($header, $row);
    }
    print_r($csv); die();
    
    $table = [];
    foreach($csv as $row) {
        $table[] = $row['BVN'];
    }
    
    $values = array_count_values($table);

    foreach ($values as $key => $value) {
        if ($value > 1) {
            echo "The BVN " . $key . " is duplicated " . $value . " times\n";
        }
    }
}

