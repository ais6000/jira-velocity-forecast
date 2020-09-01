<?php
/**
 * TODO:
 *  Extract into models
 *  Transform into controller class
 */
namespace Controller;

$config = new \Resources\Config();

$baseUrl = isset($_GET['c']) ? "?c=" . $_GET['c'] : "?";
$currentCountry = isset($_GET['c']) ? $_GET['c'] : $config->getConfig('countries')[0];

$date = new \Model\Date($currentCountry);
$fileReader = new \Model\FileReader($config, $date);
if (isset($_FILES['file'])) {
    $fileReader->setFile($_FILES['file']);
}

if ($fileReader->hasData()) {
    $sourceData = $fileReader->getData();

    $edgeDates = [null, null];
    foreach ($sourceData as $rowData) {
        if ($edgeDates[0] === null || $edgeDates[0] > $rowData[0]) {
            $edgeDates[0] = $rowData[0];
        }
        if ($edgeDates[1] === null || $edgeDates[1] < $rowData[1]) {
            $edgeDates[1] = $rowData[0];
        }
    }

    if (!empty($sourceData)) {
        $dates = [];
        $intervalTotals = [];
        $intervalData = [];
        $holidays = [];
        $currentDate = $edgeDates[0];
        $intervalCounter = 0;
        while ($currentDate <= $edgeDates[1]) {
            $intervalCounter++;
            if (!$date->isHoliday($currentDate)) {
                if (!isset($dates[$currentDate])) {
                    $dates[$currentDate] = [
                        'story_points' => 0,
                        'number_of_stories' => 0,
                    ];
                }

                foreach ($sourceData as $dataRow) {
                    if ($dataRow[0] != $currentDate) {
                        continue;
                    }
                    $dates[$currentDate]['number_of_stories']++;
                    $dates[$currentDate]['story_points'] += (int)$dataRow[1];
                }
            }

            $dailyData = $dates[$currentDate]['story_points'] ?? 0;
            if ($_POST['metric'] == $config->getConfig('metric_num_of_stories')) {
                $dailyData = $dates[$currentDate]['number_of_stories'] ?? 0;
            }
            $intervalData[] = $dailyData;
            if ($intervalCounter >= (int)$_POST['days'] * 1.4) {
                $intervalTitle = \date("Y-m-d", \strtotime($currentDate));
                $intervalTotals[$intervalTitle] = \array_sum($intervalData);
                $intervalData = [];
                $intervalCounter = 0;
            }

            $currentDate = \date("Y-m-d", \strtotime("+1 day", \strtotime($currentDate)));
        }

        $velocityData = [];
        for ($iterator = 0; $iterator < $config->getConfig('iterations'); $iterator++) {
            $velocityData[$iterator] = 0;
            for ($dayCounter = 0; $dayCounter < (int)$_POST['days']; $dayCounter++) {
                $value = $dates[\array_rand($dates)];
                $velocityData[$iterator] += $_POST['metric'] == $config->getConfig('metric_story_points') ? $value['story_points'] : $value['number_of_stories'];
            }
        }
    }
}

$hasData = isset($velocityData);
$invalidFile = $fileReader->hasData() && empty($velocityData);