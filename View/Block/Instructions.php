<?php
namespace View\Block;

use Model\FileReader;
use Resources\Config;

class Instructions implements BlockInterface
{
    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        FileReader $fileReader,
        Config $config
    ) {
        $this->fileReader = $fileReader;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function render() : string
    {
        $instructionsContent = "";
        if (!$this->fileReader->hasData()) {
            $instructionsContent .= \str_replace(
                "{{iterations-count}}",
                $this->config->getConfig('iterations'),
                \file_get_contents(ROOT . "View/Templates/Main/Instructions.tpl")
            );
        }

        return $instructionsContent;
    }
}
