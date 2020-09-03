<?php
namespace View\Block;

use Model\FileReader;
use Model\Request;
use Model\Velocity;
use Resources\Config;
use View\Model\GraphsJs;

class Graphs implements BlockInterface
{
    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Velocity
     */
    private $velocity;

    public function __construct(
        FileReader $fileReader,
        Request $request,
        Config $config,
        Velocity $velocity
    ) {
        $this->fileReader = $fileReader;
        $this->request = $request;
        $this->config = $config;
        $this->velocity = $velocity;
    }

    /**
     * @inheritDoc
     */
    public function render() : string
    {
        $graphsContent = "";
        if ($this->fileReader->hasData()) {
            $graphJs = new GraphsJs($this->request, $this->config, $this->velocity);
            $graphsContent .= \str_replace(
                [
                    "{{baseUrl}}",
                    "{{issueCount}}",
                    "{{firstDate}}",
                    "{{lastDate}}",
                    "{{graphHistogram}}",
                    "{{graphTrend}}",
                ],
                [
                    $this->request->getBaseUrl(),
                    $this->velocity->getIssueCount(),
                    \date("j.n.Y", \strtotime($this->velocity->getEdgeDates()[0])),
                    \date("j.n.Y", \strtotime($this->velocity->getEdgeDates()[1])),
                    $graphJs->getHistogram(),
                    $graphJs->getTrend(),
                ],
                \file_get_contents("View/Templates/Main/Graphs.tpl")
            );
        }

        return $graphsContent;
    }
}
