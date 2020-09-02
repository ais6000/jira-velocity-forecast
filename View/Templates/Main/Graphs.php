<?php if (isset($velocityData)): ?>
    <?php $metricTitle = $request->getMetric() == $config->getConfig('metric_story_points') ? "story points" : "number of stories"; ?>
    <div class="alert alert-success" role="alert">
        <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-check-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
            <path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
            <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
        </svg>
        Analysed <?= $velocity->getIssueCount() ?> stories from between
        <?= \date("j.n.Y", \strtotime($velocity->getEdgeDates()[0])) ?> -
        <?= \date("j.n.Y", \strtotime($velocity->getEdgeDates()[1])) ?>
    </div>
    <div class="row">
        <div class="col"><a href="<?= $request->getBaseUrl() ?>">Reset</a></div>
    </div>
    <div class="row">
        <div class="col" id="graph-histogram"></div>
    </div>
    <div class="row">
        <div class="col" id="graph-trend"></div>
    </div>
    <div class="row">
        <div class="col"><a href="<?= $request->getBaseUrl() ?>">Reset</a></div>
    </div>
    <script>
        // Histogram
        var x_histogram = [];
        <?php foreach ($velocityData as $key => $value): ?>x_histogram.push(<?= $value ?>);<?php endforeach; ?>
        var trace_histogram = {
            x: x_histogram,
            type: 'histogram',
            xbins: {
                size: 1
            }
        };
        var layout_histogram = {
            xaxis: {title: "Total <?= $metricTitle ?> completed in <?= $request->getDays() ?> working days"},
            yaxis: {title: "Occurrences (<?= $config->getConfig('iterations') ?> iterations)"},
            bargap: 0.05,
            title: 'Velocity probability'
        };
        Plotly.newPlot('graph-histogram', [trace_histogram], layout_histogram);

        // Trend
        var x_trend = [];
        var y_trend = [];
        <?php foreach ($intervalTotals as $intervalTitle => $intervalTotal): ?>
        x_trend.push('<?= $intervalTitle ?>');
        y_trend.push(<?= (int)$intervalTotal ?>);
        <?php endforeach; ?>
        var trace_trend = {
            x: x_trend,
            y: y_trend,
            mode: 'lines'
        };
        var layout_trend = {
            yaxis: {
                title: "<?= \ucfirst($metricTitle) ?> completed",
                rangemode: 'tozero'
            },
            title: 'Trend'
        };
        Plotly.newPlot('graph-trend', [trace_trend], layout_trend);
    </script>
<?php endif; ?>