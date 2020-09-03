<?php
namespace View\Block;

use Model\FileReader;
use Model\Velocity;

class Warnings implements BlockInterface
{
    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var Velocity
     */
    private $velocity;

    public function __construct(
        FileReader $fileReader,
        Velocity $velocity
    ) {
        $this->fileReader = $fileReader;
        $this->velocity = $velocity;
    }

    /**
     * @inheritDoc
     */
    public function render() : string
    {
        $warningsContent = "";
        if ($this->fileReader->hasData() && empty($this->velocity->getVelocityData())) {
            $warningsContent .= \file_get_contents("View/Templates/Main/Warnings.tpl");
        }

        return $warningsContent;
    }
}
