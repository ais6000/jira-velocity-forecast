<?php
namespace Model;

use Resources\Config;

class FileReader
{
    const TEMP_NAME = 'tmp_name';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Date
     */
    private $date;

    /**
     * @var array[]
     */
    private $sourceData;

    public function __construct(
        Config $config,
        Date $date,
        Request $request
    ) {
        $this->config = $config;
        $this->date = $date;

        if ($request->getFile()) {
            $this->setFile($request->getFile());
        }
    }

    /**
     * Uploaded file from $_FILES
     * @var array
     */
    private $file;

    /**
     * @param array $file
     */
    private function setFile(array $file)
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

        return !empty($this->file[self::TEMP_NAME]);
    }

    /**
     * @return array
     */
    public function getData() : array
    {
        if (!$this->hasData()) {
            return [];
        }

        if ($this->sourceData === null) {
            $fileContents = \file($this->file[self::TEMP_NAME]);

            $this->sourceData = [];
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
                $this->sourceData[] = $rowData;
            }
        }

        return $this->sourceData;
    }
}
