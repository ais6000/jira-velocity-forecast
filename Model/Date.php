<?php
namespace Model;

class Date
{
    const API_URL = "https://date.nager.at/api/v2/PublicHolidays/"; // 2020/FI

    /**
     * @var Country
     */
    private $country;

    /**
     * @var array
     */
    private $holidays;

    public function __construct(
        Country $country
    ) {
        $this->country = $country;
        $this->holidays = [];
    }

    /**
     * @param string $dateString
     * @return bool
     */
    public function isDate(string $dateString) : bool
    {
        if (empty($dateString)) {
            return false;
        }
        $dateParts = \explode(" ", $dateString);
        $dateParts = \explode("-", $dateParts[0]);
        if (\count($dateParts) != 3) {
            return false;
        }

        return checkdate($dateParts[1], $dateParts[2], $dateParts[0]);
    }

    /**
     * @param string $date
     * @return bool
     */
    public function isHoliday(string $date) : bool
    {
        if ((\date('N', \strtotime($date)) >= 6)) {
            return true;
        }

        $year = \date("Y", \strtotime($date));
        if (!isset($this->holidays[$year])) {
            $handle = \curl_init(
                \sprintf(self::API_URL . "%s/%s", $year, \strtolower($this->country->getCurrentCountry()))
            );
            \curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            $holidayData = \curl_exec($handle);
            if ($holidayData === false || \curl_getinfo($handle, CURLINFO_HTTP_CODE) != 200) {
                $holidayData = "[]";
            }
            $holidayData = \json_decode($holidayData);

            $this->holidays[$year] = [];
            foreach ($holidayData as $holiday) {
                $this->holidays[$year][] = $holiday->date;
            }
        }

        if (\in_array($date, $this->holidays[$year])) {
            return true;
        }

        return false;
    }
}