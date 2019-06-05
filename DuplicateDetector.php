<?php

include 'CsvFile.php';

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
     * @var int
     */
    protected $total = 0;

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
            $this->file->position($column));
        }, $this->file->top());
        
        $this->getColumnDuplicates();

        $this->pushDuplicates();

        return $this;
    }

    private function fetchColumnData($column, $index)
    {
        $rowNumber = 2;

        foreach ($this->file->body() as $row) {
            $columnData[$column][$rowNumber] = $row[$index] ?? "";
            $rowNumber++;
        }

        return $columnData;
    }

    private function getColumnDuplicates()
    {
        $founds = [];

        $match = function($self, $picture) {
            return strtolower($self) == strtolower($picture);
        };

        $count_duplicate = function($value, $index, $data) use ($match, &$founds){
            $counter = 0;
            foreach($data as $row => $checkable) {
                if ($match($value, $checkable)) {
                    $counter++;
                    if ($counter > 1 && !in_array($index, $founds[$value])) {
                        $founds[$value][] = $row;
                    }
                }
            }
        };
        
        foreach ($this->file->top() as $index => $column) {
            $columnData = $this->columns[$index][$column];
            array_walk($columnData, $count_duplicate, $columnData);
            $this->columnDuplicates[$column] = $founds;
            $founds = [];

        }
    }

    private function pushDuplicates()
    {
        $map_duplicate_message = function($row, $index, $dupData) {
            $this->duplicates[] = "Possible Duplicate: '{$dupData[0]}', 
            Found in Header: {$dupData[1]}, On Line {$row}";
        };

        $map_loose_duplicate_message = function($row) {
            $this->duplicates[] = "Possible Duplicate: On Line {$row}. 
            Found in Headers: {$this->file->top_to_string()}";
        };

        if ($this->level == "loose" && count($this->file->top()) > 1) {
            $row_duplicates = [];
            foreach($this->file->top() as $head) {
                $duplicates = $this->columnDuplicates[$head] ?? 0;
                if ($duplicates) {
                    $row_duplicates[] = array_merge(...array_values($duplicates));
                }
            }
            $duplicate_on_all_rows = call_user_func_array("array_intersect", $row_duplicates);
            $this->total = count($duplicate_on_all_rows);
            array_walk($duplicate_on_all_rows, $map_loose_duplicate_message);

        } else {

            foreach ($this->file->top() as $head) {
                $duplicates = $this->columnDuplicates[$head] ?? 0;
                if ($duplicates) {
                    foreach($duplicates as $dup => $rows) {
                        $this->total += count($rows);
                        array_walk($rows, $map_duplicate_message, [$dup, $head]);
                    }
                }
            }
        }
        return $this;
    }

    public function duplicates()
    {
        return $this->duplicates;
    }

    public function totals()
    {
        return $this->total;
    }

}
