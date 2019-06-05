<?php

namespace CsvDuplicateDetector;

class CsvFile
{
    /**
     * @var string
     */
    public $csv;

    /**
     * @var array
     */
    public $headers;

    /**
     * CsvFile constructor.
     * @param $base64
     * @param array|null $csvHeaders
     */
    public $csvHeaders;

    public function __construct($base64, array $headers= null) {
        $this->csv = $this->getCsv($base64);
        $this->csvHeaders = $this->getCsvHeaders();
        try{
            $headers = $headers ?? $this->csvHeaders;
            $this->headers = $this->validateHeaders($headers);
        } catch (\Exception $e) {
            //TODO Display Error message
        }
    }

    private function getCsv($base64)
    {
        $file = base64_decode($base64);
        $csv = explode("\n", $file);
        $rows = [];
        foreach ($csv as $row) {
            $rows[] = str_getcsv($row);
        }
        return $rows;
    }

    private function getCsvHeaders()
    {
        return array_shift($this->csv);
    }

    private function validateHeaders(array $headers)
    {
        if(! array_diff($headers, $this->csvHeaders)) {
            return $headers;
        }

        throw new \Exception("headers do not match");
    }

    public function checkForDuplicates()
    {
        $check = [];
        foreach ($this->headers as $header) {
            $position = array_search($header, $this->csvHeaders);
            foreach ($this->csv as $key => $row) {
                $check[$key] = $row[$position];
            }
        }
        
        return array_count_values($check);
    }
}