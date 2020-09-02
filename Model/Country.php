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

    public function getCurrentCountry()
    {
        return $this->request->getCountryCode() ? $this->request->getCountryCode() : self::COUNTRIES[0];
    }
}