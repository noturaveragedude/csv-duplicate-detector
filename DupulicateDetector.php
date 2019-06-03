<?php 
use CsvFile;

class DuplicateDetector
{
    public function __construct(CsvFile $file) {
        $this->file = $file;
    }
}
