<?php
namespace View\Block;

use Model\Country;
use Model\FileReader;

class Title implements BlockInterface
{
    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var Country
     */
    private $country;

    public function __construct(
        FileReader $fileReader,
        Country $country
    ) {
        $this->fileReader = $fileReader;
        $this->country = $country;
    }

    /**
     * @inheritDoc
     */
    public function render() : string
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
}
