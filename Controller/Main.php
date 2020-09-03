<?php
namespace Controller;

class Main
{
    public function execute()
    {
        // Models
        $config = new \Resources\Config();
        $request = new \Model\Request($config);
        $country = new \Model\Country($request);
        $date = new \Model\Date($country);
        $fileReader = new \Model\FileReader($config, $date, $request);
        $velocity = new \Model\Velocity($fileReader, $date, $request, $config);

        // Blocks
        $formBlock = new \View\Block\Form($fileReader, $request, $config);
        $graphsBlock = new \View\Block\Graphs($fileReader, $request, $config, $velocity);
        $instructionsBlock = new \View\Block\Instructions($fileReader, $config);
        $titleBlock = new \View\Block\Title($fileReader, $country);
        $warningsBlock = new \View\Block\Warnings($fileReader, $velocity);

        // View
        $view = new \View\View(
            $formBlock,
            $graphsBlock,
            $instructionsBlock,
            $titleBlock,
            $warningsBlock
        );
        $view->execute();
    }
}
