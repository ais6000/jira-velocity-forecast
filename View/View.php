<?php
namespace View;

use View\Block\Form;
use View\Block\Graphs;
use View\Block\Instructions;
use View\Block\Title;
use View\Block\Warnings;

class View
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @var Graphs
     */
    private $graphs;

    /**
     * @var Instructions
     */
    private $instructions;

    /**
     * @var Title
     */
    private $title;

    /**
     * @var Warnings
     */
    private $warnings;

    public function __construct(
        Form $form,
        Graphs $graphs,
        Instructions $instructions,
        Title $title,
        Warnings $warnings
    ) {
        $this->form = $form;
        $this->graphs = $graphs;
        $this->instructions = $instructions;
        $this->title = $title;
        $this->warnings = $warnings;
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
        $content .= $this->title->render();
        $content .= $this->warnings->render();
        $content .= $this->form->render();
        $content .= $this->instructions->render();
        $content .= $this->graphs->render();

        return \str_replace("{{content}}", $content, \file_get_contents("View/Templates/Main.tpl"));
    }
}
