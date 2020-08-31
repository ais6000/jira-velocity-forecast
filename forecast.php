<?php
const ITERATIONS = 50000;
const AVG_STORY_POINT = 2;
const METRIC_STORY_POINTS = "1";
const METRIC_NUM_OF_STORIES = "2";
$baseUrl = isset($_GET['c']) ? "?c=" . $_GET['c'] : "?";
$countries = ['FI', 'SE', 'GB', 'US', 'AE', 'NO', 'DE', 'ZA', 'BE', 'NL', 'EE'];
$currentCountry = isset($_GET['c']) ? $_GET['c'] : $countries[0];

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
        if (!isset($rowData[1]) || !isDate($rowData[0])) {
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
            if (!\isHoliday($currentDate)) {
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

        $data = [];
        for ($iterator = 0; $iterator < ITERATIONS; $iterator++) {
            $data[$iterator] = 0;
            for ($dayCounter = 0; $dayCounter < (int)$_POST['days']; $dayCounter++) {
                $value = $dates[\array_rand($dates)];
                $data[$iterator] += $_POST['metric'] == METRIC_STORY_POINTS ? $value['story_points'] : $value['number_of_stories'];
            }
        }
    }
}

function isDate($dateString)
{
    if (empty($dateString)) {
        return false;
    }
    $dateParts = \explode(" ", $dateString);
    $dateParts = \explode("-", $dateParts[0]);
    if (\count($dateParts) != 3) {
        return false;
    }

    return checkdate($dateParts[1], $dateParts[2], $dateParts[0]);
}
function isHoliday($date)
{
    $apiUrl = "https://date.nager.at/api/v2/PublicHolidays/"; // 2020/FI
    global $holidays, $currentCountry;

    if ((\date('N', \strtotime($date)) >= 6)) {
        return true;
    }

    $year = \date("Y", \strtotime($date));
    if (!isset($holidays[$year])) {
        $ch = \curl_init(\sprintf($apiUrl . "%s/%s", $year, \strtolower($currentCountry)));
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $holidayData = \curl_exec($ch);
        if ($holidayData === false || \curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            $holidayData = "[]";
        }
        $holidayData = \json_decode($holidayData);

        $holidays[$year] = [];
        foreach ($holidayData as $holiday) {
            $holidays[$year][] = $holiday->date;
        }
    }

    if (\in_array($date, $holidays[$year])) {
        return true;
    }

    return false;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Jira velocity forecast tool">
    <meta name="author" content="Sami Asmundi">
    <meta name="robots" content="noindex">
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z"
          crossorigin="anonymous" />
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css"
          integrity="sha512-Cv93isQdFwaKBV+Z4X8kaVBYWHST58Xb/jVOcV9aRsGSArZsgAnFIhMpDoMDcFNoUtday1hdjn0nGp3+KZyyFw=="
          crossorigin="anonymous" />
    <title>Jira velocity forecast tool</title>
</head>
<body>
<div class="container">
    <div class="row" style="margin-bottom: 40px;">
        <div class="col-9">
            <h1>Jira velocity forecast tool</h1>
        </div>
        <?php if (!isset($data)): ?>
            <div class="col-3 text-right">
                <div class="dropdown show" style="margin: 5px 15px 0 0;">
                    <a class="btn btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border: 1px solid silver;">
                        <span class="flag-icon flag-icon-<?= \strtolower($currentCountry) ?>"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <?php foreach ($countries as $country): ?>
                            <a class="dropdown-item" href="?c=<?= $country ?>">
                                <span class="flag-icon flag-icon-<?= \strtolower($country) ?>"></span> <?= $country ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php if (isset($_FILES['file']) && (empty($data) || empty($_FILES['file']['tmp_name']))): ?>
        <div class="alert alert-danger" role="alert">
            <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-shield-exclamation" fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                <path fill-rule="evenodd" d="M5.443 1.991a60.17 60.17 0 0 0-2.725.802.454.454 0 0 0-.315.366C1.87 7.056 3.1 9.9 4.567 11.773c.736.94 1.533 1.636 2.197 2.093.333.228.626.394.857.5.116.053.21.089.282.11A.73.73 0 0 0 8 14.5c.007-.001.038-.005.097-.023.072-.022.166-.058.282-.111.23-.106.525-.272.857-.5a10.197 10.197 0 0 0 2.197-2.093C12.9 9.9 14.13 7.056 13.597 3.159a.454.454 0 0 0-.315-.366c-.626-.2-1.682-.526-2.725-.802C9.491 1.71 8.51 1.5 8 1.5c-.51 0-1.49.21-2.557.491zm-.256-.966C6.23.749 7.337.5 8 .5c.662 0 1.77.249 2.813.525a61.09 61.09 0 0 1 2.772.815c.528.168.926.623 1.003 1.184.573 4.197-.756 7.307-2.367 9.365a11.191 11.191 0 0 1-2.418 2.3 6.942 6.942 0 0 1-1.007.586c-.27.124-.558.225-.796.225s-.526-.101-.796-.225a6.908 6.908 0 0 1-1.007-.586 11.192 11.192 0 0 1-2.417-2.3C2.167 10.331.839 7.221 1.412 3.024A1.454 1.454 0 0 1 2.415 1.84a61.11 61.11 0 0 1 2.772-.815z"/>
                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
            </svg>
            Please select a valid csv file!
        </div>
    <?php endif; ?>
    <?php if (!isset($data)): ?>
        <form action="<?= $baseUrl ?>" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-xl" style="height: 90px;">
                    <label for="file">1. CSV file exported from Jira</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="file" name="file">
                        <label class="custom-file-label" for="file">Choose file</label>
                    </div>
                </div>
                <div class="col-xl" style="height: 90px;">
                    <label for="days">2. Number of working days to forecast for:</label>
                    <input type="text" class="form-control" id="days" name="days" placeholder="10 equals two week sprint" value="10" required>
                </div>
                <div class="col-xl" style="height: 90px;">
                    <label for="metric">3. Forecast metric</label>
                    <select class="custom-select d-block w-100" id="metric" name="metric">
                        <option value="<?= METRIC_NUM_OF_STORIES ?>">Number of stories</option>
                        <option value="<?= METRIC_STORY_POINTS ?>">Story points</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Calculate</button>
                </div>
            </div>
        </form>

        <div class="card" style="margin-top: 40px;">
            <div class="card-header">
                <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-patch-question" fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM8.05 9.6c.336 0 .504-.24.554-.627.04-.534.198-.815.847-1.26.673-.475 1.049-1.09 1.049-1.986 0-1.325-.92-2.227-2.262-2.227-1.02 0-1.792.492-2.1 1.29A1.71 1.71 0 0 0 6 5.48c0 .393.203.64.545.64.272 0 .455-.147.564-.51.158-.592.525-.915 1.074-.915.61 0 1.03.446 1.03 1.084 0 .563-.208.885-.822 1.325-.619.433-.926.914-.926 1.64v.111c0 .428.208.745.585.745z"/>
                    <path fill-rule="evenodd" d="M10.273 2.513l-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911l-1.318.016z"/>
                </svg>
                <b>What is it and how do I use it?</b>
            </div>
            <div class="card-body">
                <p>
                    This tool analyses user stories that have been completed in the past to forecast future velocity, or, total work output.
                    Any filtering parameters can be applied in Jira to achieve a forecast based on those parameters.
                    For example providing data targeted to a specific team will result in a forecast to that team. The resulting
                    graph can be used, for example, in planning future sprints of any length.
                </p>
                <p>
                    Result is achieved through <a href="https://en.wikipedia.org/wiki/Monte_Carlo_method" target="_blank">Monte Carlo simulation</a>
                    which randomizes the provided Jira data and iterates through the data several times (<?= ITERATIONS ?> by default)
                    to achieve, not just the average, but a wider range of values and their probabilities for future output.
                    The end result is a histogram graph in the form of a "bell curve", where high bars in the middle represent
                    the most probable values you should look for.
                </p>
                <p>
                    Additionally, the tool will present a trend graph to visualise changes in velocity over time, within the time window included
                    in the Jira output file. With the trend information it is easier to adapt the probability data with changes occurring
                    with time when analysing a longer period. For example, when analysing a whole year's worth of data, there might be a natural
                    factor affecting recent output, when compared with earliest available data, that will not be reflected in the histogram.
                </p>
                <ul style="list-style-type: none;">
                    <li>1. Log in to your Jira account and navigate to https://&lt;path-to-jira&gt;/issues</a></li>
                    <li>2. Use filters to build the data set suitable for your need. Only completed issues should be included</li>
                    <li><ul><li>Advanced filter example: <i>"resolution = Done AND assignee in (membersOf("Name of your team")) ORDER BY resolved DESC"</i></li></ul></li>
                    <li><ul><li><b>Important!</b> Make sure <i>"resolution = Done"</i> filter is always active to filter out incomplete issues!</li></ul></li>
                    <li>3. Select just the following columns to be visible, in this order: "Resolved", "Story Points"</li>
                    <li><ul><li>Practically any columns may be visible, but make sure to have the first two columns from left as described above</li></ul></li>
                    <li><ul><li>If you do not have valid story point data available, you can ignore the story points column. Just make sure to always use "Number of stories" metric with the tool</li></ul></li>
                    <li>4. Export -> CSV (Current fields) -> Delimiter: ","</li>
                </ul>
                The content of your csv file should look like this:
                <div style="background-color: #eee; padding: 10px;">
                    <code>
                        Resolved,Custom field (Story Points)<br />
                        2020-08-10 15:20,3.0<br />
                        2020-08-10 13:13,4.0<br />
                        2020-08-07 13:17,2.0<br />
                        ...
                    </code>
                </div><br />
                <p>
                    Country selector at the top right corner is used to include national holidays, together with weekends, during data analysis.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($data)): ?>
        <div class="alert alert-success" role="alert">
            <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-check-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                <path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
            </svg>
            Analysed <?= $numberOfStories ?> stories from between
            <?= \date("j.n.Y", \strtotime($edgeDates[0])) ?> -
            <?= \date("j.n.Y", \strtotime($edgeDates[1])) ?>
        </div>
        <div class="row">
            <div class="col"><a href="<?= $baseUrl ?>">Reset</a></div>
        </div>
        <div class="row">
            <div class="col" id="graph-histogram"></div>
        </div>
        <div class="row">
            <div class="col" id="graph-trend"></div>
        </div>
        <div class="row">
            <div class="col"><a href="<?= $baseUrl ?>">Reset</a></div>
        </div>
        <script>
            // Histogram
            var x_histogram = [];
            <?php foreach ($data as $key => $value): ?>x_histogram.push(<?= $value ?>);<?php endforeach; ?>
            var trace_histogram = {
                x: x_histogram,
                type: 'histogram',
                xbins: {
                    size: 1
                }
            };
            var layout_histogram = {
                xaxis: {title: "Total <?= $metricTitle ?> completed in <?= $_POST['days'] ?> working days"},
                yaxis: {title: "Occurrences (<?= ITERATIONS ?> iterations)"},
                bargap: 0.05,
                title: 'Velocity probability'
            };
            Plotly.newPlot('graph-histogram', [trace_histogram], layout_histogram);

            // Trend
            var x_trend = [];
            var y_trend = [];
            <?php foreach ($intervalTotals as $intervalTitle => $intervalTotal): ?>
            x_trend.push('<?= $intervalTitle ?>');
            y_trend.push(<?= (int)$intervalTotal ?>);
            <?php endforeach; ?>
            var trace_trend = {
                x: x_trend,
                y: y_trend,
                mode: 'lines'
            };
            var layout_trend = {
                yaxis: {
                    title: "<?= \ucfirst($metricTitle) ?> completed",
                    rangemode: 'tozero'
                },
                title: 'Trend'
            };
            Plotly.newPlot('graph-trend', [trace_trend], layout_trend);
        </script>
    <?php endif; ?>
</div>
</body>
</html>
