<?php
namespace Controller;

$config = new \Resources\Config();
$request = new \Model\Request($config);
$country = new \Model\Country($request);
$date = new \Model\Date($country);
$fileReader = new \Model\FileReader($config, $date, $request);
$velocity = new \Model\Velocity($fileReader, $date, $request, $config);

if ($fileReader->hasData()) {
    $velocityData = $velocity->getVelocityData();
    $intervalTotals = $velocity->getIntervalData();
}

# View
require_once 'View/View.php';