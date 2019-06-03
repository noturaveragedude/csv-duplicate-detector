<?php

class CsvFile
{
    /**
     * @var string
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
    private $csvHeaders;


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
        return str_getcsv($base64);
    }

    private function getCsvHeaders()
    {
        return array_shift($this->csv);
    }

    private function validateHeaders(array $headers)
    {
        if(! array_diff($headers, $this->fileHeaders)) {
            return $headers;
        }

        throw new \Exception("headers do not match");
    }

    public function checkForDuplicates()
    {

    }

}