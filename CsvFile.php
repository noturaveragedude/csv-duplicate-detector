<?php

class CsvFile
{
    /**
     * @var array
     */
    protected $csv;

    /**
     * @var array
     */
    protected $headers;

    /**
     * CsvFile constructor.
     * @param $base64
     * @param array|null $fileHeaders
     */
    protected $fileHeaders;


    public function __construct($file, array $headers = []) {
        $this->csv = $this->getCsv($file);
        $this->fileHeaders = array_shift($this->csv);
        $this->headers = $this->getHeaders($headers);
    }

    private function getCsv($file)
    {
        $lines = file($file['tmp_name'], FILE_IGNORE_NEW_LINES);
    
        return array_map('str_getcsv', $lines);
    }

    private function getHeaders(array $headers)
    {
        return ($headers != null && !in_array("", $headers)) ? 
            $this->validateHeaders($headers) : $this->fileHeaders;
    }

    private function validateHeaders(array $headers)
    {
        $trim_whitespace = function ($trimmable) {
            return trim($trimmable);
        };

        $headers = array_map($trim_whitespace, $headers);
        
        if(! array_diff($headers, $this->fileHeaders)) {
            return $headers;
        }

        throw new \Exception("headers do not match");
    }

    public function top()
    {
        return $this->headers;
    }

    public function body()
    {
        return $this->csv;
    }

    public function position($column)
    {
        return array_search($column, $this->fileHeaders);
    }

}