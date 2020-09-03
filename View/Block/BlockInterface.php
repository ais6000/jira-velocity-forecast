<?php
namespace View\Block;

interface BlockInterface
{
    /**
     * @return string
     */
    public function render() : string;
}