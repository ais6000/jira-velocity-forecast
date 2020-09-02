<?php
namespace Model;

use Resources\Config;

class Request
{
    const PARAM_COUNTRY_CODE = 'c';
    const PARAM_FILE = 'file';
    const PARAM_METRIC = 'metric';
    const PARAM_DAYS = 'days';

    /**
     * @var Config;
     */
    private $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @return string|null
     */
    public function getCountryCode()
    {
        return $_GET[self::PARAM_COUNTRY_CODE] ?? null;
    }

    /**
     * @return string
     */
    public function getBaseUrl() : string
    {
        $baseUrl = "?";
        if ($this->getCountryCode()) {
            $baseUrl .= self::PARAM_COUNTRY_CODE . "=" . $this->getCountryCode();
        }

        return $baseUrl;
    }

    /**
     * @return array|null
     */
    public function getFile()
    {
        return $_FILES[self::PARAM_FILE] ?? null;
    }

    /**
     * @return string|null
     */
    public function getMetric()
    {
        return $_POST[self::PARAM_METRIC] ?? null;
    }

    /**
     * @return string|null
     */
    public function getDays()
    {
        return $_POST[self::PARAM_DAYS] ?? null;
    }
}