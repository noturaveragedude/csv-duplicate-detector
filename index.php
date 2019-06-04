<h2>
    Upload CSV
</h2>
<form enctype="multipart/form-data" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <!-- Name of input element determines name in $_FILES array -->
    Check this file for duplicates: <input name="csv_file" type="file" />
    <select name="level">
        <option value="strict">strict</option>
        <!-- <option value="loose">loose</option> -->
    <input type="submit" value="Send File" />
</form>

<?php 
include_once 'DuplicateDetector.php';
include_once 'CsvFile.php';

if (isset($_FILES['csv_file'])) {
    $csv = new CsvFile($_FILES['csv_file']);
    $dectector = (new DuplicateDetector($csv, $_POST['level']))->detect();
    echo "{$dectector->totals()} Duplicates Found" . "<br>";
    foreach ($dectector->duplicates() as $message) {
        echo "{$message}" . "<br>";
    }
}

