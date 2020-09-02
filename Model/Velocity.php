<?php
namespace Model;

use Resources\Config;

class Velocity
{
    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var Date
     */
    private $date;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string[]
     */
    private $edgeDates;

    /**
     * @var array
     */
    private $velocityDates;

    public function __construct(
        FileReader $fileReader,
        Date $date,
        Request $request,
        Config $config
    ) {
        $this->fileReader = $fileReader;
        $this->date = $date;
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * @return int
     */
    public function getIssueCount()
    {
        return \count($this->fileReader->getData());
    }

    /**
     * @return string[]
     */
    public function getEdgeDates()
    {
        if ($this->edgeDates === null) {
            $sourceData = $this->fileReader->getData();
            $this->edgeDates = [null, null];
            foreach ($sourceData as $rowData) {
                if ($this->edgeDates[0] === null || $this->edgeDates[0] > $rowData[0]) {
                    $this->edgeDates[0] = $rowData[0];
                }
                if ($this->edgeDates[1] === null || $this->edgeDates[1] < $rowData[0]) {
                    $this->edgeDates[1] = $rowData[0];
                }
            }
        }

        return $this->edgeDates;
    }

    public function getVelocityData()
    {
        $dates = $this->getVelocityDates();

        $velocityData = [];
        for ($iterator = 0; $iterator < $this->config->getConfig('iterations'); $iterator++) {
            $velocityData[$iterator] = 0;
            for ($dayCounter = 0; $dayCounter < (int)$this->request->getDays(); $dayCounter++) {
                $value = $dates[\array_rand($dates)];
                $velocityData[$iterator] += $this->request->getMetric() == $this->config->getConfig('metric_story_points') ? $value['story_points'] : $value['number_of_stories'];
            }
        }

        return $velocityData;
    }

    public function getIntervalData()
    {
        $intervalTotals = [];
        $dates = $this->getVelocityDates();

        $intervalData = [];
        $intervalCounter = 0;
        foreach ($dates as $currentDate => $dateVelocity) {
            $intervalCounter++;
            $dailyData = $dateVelocity['story_points'] ?? 0;
            if ($this->request->getMetric() == $this->config->getConfig('metric_num_of_stories')) {
                $dailyData = $dateVelocity['number_of_stories'] ?? 0;
            }
            $intervalData[] = $dailyData;
            if ($intervalCounter >= (int)$this->request->getDays() * 1.4) {
                $intervalTitle = \date("Y-m-d", \strtotime($currentDate));
                $intervalTotals[$intervalTitle] = \array_sum($intervalData);
                $intervalData = [];
                $intervalCounter = 0;
            }
        }

        return $intervalTotals;
    }

    /**
     * @return array
     */
    private function getVelocityDates()
    {
        if ($this->velocityDates === null) {
            $sourceData = $this->fileReader->getData();
            if (empty($sourceData)) {
                return [];
            }

            $this->velocityDates = [];
            $currentDate = $this->getEdgeDates()[0];
            while ($currentDate <= $this->getEdgeDates()[1]) {
                if (!$this->date->isHoliday($currentDate)) {
                    if (!isset($this->velocityDates[$currentDate])) {
                        $this->velocityDates[$currentDate] = [
                            'story_points' => 0,
                            'number_of_stories' => 0,
                        ];
                    }

                    foreach ($sourceData as $dataRow) {
                        if ($dataRow[0] != $currentDate) {
                            continue;
                        }
                        $this->velocityDates[$currentDate]['number_of_stories']++;
                        $this->velocityDates[$currentDate]['story_points'] += (int)$dataRow[1];
                    }
                }

                $currentDate = \date("Y-m-d", \strtotime("+1 day", \strtotime($currentDate)));
            }
        }

        return $this->velocityDates;
    }
}