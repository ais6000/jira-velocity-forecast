<?php
namespace View\Block;

use Model\FileReader;
use Model\Request;
use Resources\Config;

class Form implements BlockInterface
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

    public function __construct(
        FileReader $fileReader,
        Request $request,
        Config $config
    ) {
        $this->fileReader = $fileReader;
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function render() : string
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
}
