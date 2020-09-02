<?php
namespace View;

use Model\Country;
use Model\Date;
use Model\FileReader;
use Model\Request;
use Model\Velocity;
use Resources\Config;
use View\Model\GraphsJs;

/**
 * @TODO: extract template getters into separate classes / interface
 */
class View
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
     * @var Country
     */
    private $country;

    /**
     * @var Velocity
     */
    private $velocity;

    public function __construct(
        FileReader $fileReader,
        Date $date,
        Request $request,
        Config $config,
        Country $country,
        Velocity $velocity
    ) {
        $this->fileReader = $fileReader;
        $this->date = $date;
        $this->request = $request;
        $this->config = $config;
        $this->country = $country;
        $this->velocity = $velocity;
    }

    public function execute()
    {
        $output = "";
        $output .= $this->getHead();
        $output .= $this->getMain();
        $output .= $this->getFoot();

        echo $output;
    }

    /**
     * @return string
     */
    private function getHead()
    {
        return \file_get_contents('View/Templates/Head.tpl');
    }

    /**
     * @return string
     */
    private function getFoot()
    {
        return \file_get_contents('View/Templates/Foot.tpl');
    }

    /**
     * @return string
     */
    private function getMain()
    {
        $content = "";
        $content .= $this->getTitle();
        $content .= $this->getWarnings();
        $content .= $this->getForm();
        $content .= $this->getInstructions();
        $content .= $this->getGraphs();

        return \str_replace("{{content}}", $content, \file_get_contents("View/Templates/Main.tpl"));
    }

    /**
     * @return string
     */
    private function getTitle() : string
    {
        $countrySelection = "";
        if (!$this->fileReader->hasData()) {
            $flagOptions = "";
            foreach (Country::COUNTRIES as $country) {
                $flagOptions .= \str_replace(
                    [
                        "{{country}}",
                        "{{countryLowercase}}",
                    ],
                    [
                        $country,
                        \strtolower($country),
                    ],
                    \file_get_contents("View/Templates/Main/CountrySelection/FlagOption.tpl")
                );
            }
            $countrySelection .= \str_replace(
                [
                    "{{countryLowercase}}",
                    "{{flagOptions}}"
                ],
                [
                    \strtolower($this->country->getCurrentCountry()),
                    $flagOptions,
                ],
                \file_get_contents("View/Templates/Main/CountrySelection.tpl")
            );
        }

        return \str_replace(
            "{{countrySelection}}",
            $countrySelection,
            \file_get_contents("View/Templates/Main/Title.tpl")
        );
    }

    /**
     * @return string
     */
    private function getWarnings() : string
    {
        $warningsContent = "";
        if ($this->fileReader->hasData() && empty($this->velocity->getVelocityData())) {
            $warningsContent .= \file_get_contents("View/Templates/Main/Warnings.tpl");
        }

        return $warningsContent;
    }

    /**
     * @return string
     */
    private function getForm() : string
    {
        $formContent = "";
        if (!$this->fileReader->hasData()) {
            $formContent .= \str_replace(
                [
                    "{{baseUrl}}",
                    "{{paramFile}}",
                    "{{paramDays}}",
                    "{{paramMetric}}",
                    "{{metricNumOfStories}}",
                    "{{metricStoryPoints}}",
                ],
                [
                    $this->request->getBaseUrl(),
                    Request::PARAM_FILE,
                    Request::PARAM_DAYS,
                    Request::PARAM_METRIC,
                    $this->config->getConfig('metric_num_of_stories'),
                    $this->config->getConfig('metric_story_points'),
                ],
                \file_get_contents("View/Templates/Main/Form.tpl")
            );
        }

        return $formContent;
    }

    /**
     * @return string
     */
    private function getInstructions() : string
    {
        $instructionsContent = "";
        if (!$this->fileReader->hasData()) {
            $instructionsContent .= \str_replace(
                "{{iterations-count}}",
                $this->config->getConfig('iterations'),
                \file_get_contents("View/Templates/Main/Instructions.tpl")
            );
        }

        return $instructionsContent;
    }

    /**
     * @return string
     */
    private function getGraphs() : string
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
