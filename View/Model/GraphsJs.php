<?php
namespace View\Model;

use Model\Request;
use Model\Velocity;
use Resources\Config;

class GraphsJs
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Velocity
     */
    private $velocity;

    public function __construct(
        Request $request,
        Config $config,
        Velocity $velocity
    ) {
        $this->request = $request;
        $this->config = $config;
        $this->velocity = $velocity;
    }

    /**
     * @return string
     */
    public function getHistogram() : string
    {
        $output = "";
        $velocityData = $this->velocity->getVelocityData();
        $metricTitle = "number of stories";
        if ($this->request->getMetric() == $this->config->getConfig('metric_story_points')) {
            $metricTitle = "story points";
        }

        $output .= "var x_histogram = [];";
        foreach ($velocityData as $value) {
            $output .= "x_histogram.push(" . $value . ");";
        }
        $output .= "var trace_histogram = {
                x: x_histogram,
                type: 'histogram',
                xbins: {
                    size: 1
                }
            };
            var layout_histogram = {
                xaxis: {title: \"Total " . $metricTitle . " completed in " . $this->request->getDays() . " working days\"},
                yaxis: {title: \"Occurrences (" . $this->config->getConfig('iterations') . " iterations)\"},
                bargap: 0.05,
                title: 'Velocity probability'
            };
            Plotly.newPlot('graph-histogram', [trace_histogram], layout_histogram);";

        return $output;
    }

    /**
     * @return string
     */
    public function getTrend() : string
    {
        $output = "";
        $intervalTotals = $this->velocity->getIntervalData();
        $metricTitle = "number of stories";
        if ($this->request->getMetric() == $this->config->getConfig('metric_story_points')) {
            $metricTitle = "story points";
        }

        $output .= "var x_trend = []; var y_trend = [];";
        foreach ($intervalTotals as $intervalTitle => $intervalTotal) {
            $output .= "x_trend.push('". $intervalTitle . "'); y_trend.push(" . (int)$intervalTotal . ");";
        }
        $output .= "var trace_trend = {
                x: x_trend,
                y: y_trend,
                mode: 'lines'
            };
            var layout_trend = {
                yaxis: {
                    title: \"" . \ucfirst($metricTitle) ." completed\",
                    rangemode: 'tozero'
                },
                title: 'Trend'
            };
            Plotly.newPlot('graph-trend', [trace_trend], layout_trend);";

        return $output;
    }
}