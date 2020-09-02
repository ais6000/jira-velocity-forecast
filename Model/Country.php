<?php
namespace Model;

class Country
{
    const COUNTRIES = ['FI', 'SE', 'GB', 'US', 'AE', 'NO', 'DE', 'ZA', 'BE', 'NL', 'EE'];

    private $request;

    public function __construct(
        Request $request
    ) {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getCurrentCountry() : string
    {
        return $this->request->getCountryCode() ?? self::COUNTRIES[0];
    }
}