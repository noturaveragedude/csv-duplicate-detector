<?php

use CsvFile;

class DuplicateDetector
{
    /**
     * @var CsvFile
     */
    protected $file;

    /**
     * @var string
     */
    protected $level;

    /**
     * @var array
     */
    protected $duplicates = [];

    /**
     * @var array
     */
    protected $columnDuplicates = [];

    /**
     * @var array
     */
    protected $columns = [];

    public function __construct(CsvFile $file, $level = "strict")
    {
        $this->file = $file;

        $this->level = $level;
    }

    public function detect()
    {
        $this->columns = array_map(function($column) {
            return $this->fetchColumnData($column, 
                array_search($column, $this->file->top()));
        }, $this->file->top());
        $this->getColumnDuplicates();
        $this->pushDuplicates();
    }

    private function fetchColumnData($column, $index)
    {
        $rowNumber = 1;

        foreach ($this->file->body() as $row) {
            $columnData[$column][$rowNumber] = $row[$index] ?? "";
            $rowNumber++;
        }

        return $columnData;
    }

    private function getColumnDuplicates()
    {
        foreach ($this->columns as $column => $data) {
            $occurence = array_count_values(array_values($data));
            foreach ($data as $rowNumber => $singleton) {
                $count = $occurence[$singleton] ?? 0;
                if ($count > 1) {
                   $duplicates[$column][$rowNumber] = $count;
                }
            }
            $this->columnDuplicates[] = $duplicates;

        }
    }
}
