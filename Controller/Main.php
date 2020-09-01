<?php
namespace Controller;

$baseUrl = isset($_GET['c']) ? "?c=" . $_GET['c'] : "?";
$currentCountry = isset($_GET['c']) ? $_GET['c'] : COUNTRIES[0];

$date = new \Model\Date($currentCountry);

if (isset($_FILES['file']) && !empty($_FILES['file']['tmp_name'])) {
    $metricTitle = $_POST['metric'] == METRIC_STORY_POINTS ? "story points" : "number of stories";
    $edgeDates = [null, null];
    $fileContents = \file($_FILES['file']['tmp_name']);
    $numberOfStories = \count($fileContents) - 1;
    $sourceData = [];
    foreach ($fileContents as $rowNumber => $row) {
        if (!$rowNumber) {
            continue;
        }
        $rowData = \explode(",", $row);
        if (!isset($rowData[1]) || !$date->isDate($rowData[0])) {
            continue;
        }
        $rowData[0] = \date("Y-m-d", \strtotime($rowData[0]));
        $rowData[1] = (int)$rowData[1];
        if (!$rowData[1]) {
            $rowData[1] = AVG_STORY_POINT;
        }
        $sourceData[] = $rowData;
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
            if ($_POST['metric'] == METRIC_NUM_OF_STORIES) {
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
        for ($iterator = 0; $iterator < ITERATIONS; $iterator++) {
            $velocityData[$iterator] = 0;
            for ($dayCounter = 0; $dayCounter < (int)$_POST['days']; $dayCounter++) {
                $value = $dates[\array_rand($dates)];
                $velocityData[$iterator] += $_POST['metric'] == METRIC_STORY_POINTS ? $value['story_points'] : $value['number_of_stories'];
            }
        }
    }
}

$hasData = isset($velocityData);
$invalidFile = isset($_FILES['file']) && (empty($velocityData) || empty($_FILES['file']['tmp_name']));