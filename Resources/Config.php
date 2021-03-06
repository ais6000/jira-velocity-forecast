<?php
namespace Resources;

class Config
{
    /**
     * @var array
     */
    private $config = [
        'iterations' => 50000,
        'metric_story_points' => '1',
        'metric_num_of_stories' => '2',
    ];

    /**
     * @param string $option
     * @return null|mixed
     */
    public function getConfig(string $option)
    {
        return $this->config[$option] ?? null;
    }
}
