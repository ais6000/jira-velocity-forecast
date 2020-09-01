<?php
namespace Model;

use Resources\Config;

class FileReader
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Date
     */
    private $date;

    public function __construct(
        Config $config,
        Date $date
    ) {
        $this->config = $config;
        $this->date = $date;
    }

    /**
     * Uploaded file from $_FILES
     * @var array
     */
    private $file;

    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return bool
     */
    public function hasData()
    {
        if ($this->file === null || !\is_array($this->file)) {
            return false;
        }

        return !empty($this->file['tmp_name']);
    }

    public function getData()
    {
        if (!$this->hasData()) {
            return [];
        }

        $fileContents = \file($this->file['tmp_name']);

        $data = [];
        foreach ($fileContents as $rowNumber => $row) {
            if (!$rowNumber) {
                continue;
            }
            $rowData = \explode(",", $row);
            if (!isset($rowData[1]) || !$this->date->isDate($rowData[0])) {
                continue;
            }
            $rowData[0] = \date("Y-m-d", \strtotime($rowData[0]));
            $rowData[1] = (int)$rowData[1];
            if (!$rowData[1]) {
                $rowData[1] = $this->config->getConfig('avg_story_point');
            }
            $data[] = $rowData;
        }

        return $data;
    }
}