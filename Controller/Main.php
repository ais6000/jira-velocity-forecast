<?php
namespace Controller;

class Main
{
    public function execute()
    {
        $config = new \Resources\Config();
        $request = new \Model\Request($config);
        $country = new \Model\Country($request);
        $date = new \Model\Date($country);
        $fileReader = new \Model\FileReader($config, $date, $request);
        $velocity = new \Model\Velocity($fileReader, $date, $request, $config);

        $view = new \View\View($fileReader, $date, $request, $config, $country, $velocity);
        $view->execute();
    }
}