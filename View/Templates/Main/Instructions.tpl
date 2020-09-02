<div class="card" style="margin-top: 40px;">
    <div class="card-header">
        <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-patch-question" fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM8.05 9.6c.336 0 .504-.24.554-.627.04-.534.198-.815.847-1.26.673-.475 1.049-1.09 1.049-1.986 0-1.325-.92-2.227-2.262-2.227-1.02 0-1.792.492-2.1 1.29A1.71 1.71 0 0 0 6 5.48c0 .393.203.64.545.64.272 0 .455-.147.564-.51.158-.592.525-.915 1.074-.915.61 0 1.03.446 1.03 1.084 0 .563-.208.885-.822 1.325-.619.433-.926.914-.926 1.64v.111c0 .428.208.745.585.745z"/>
            <path fill-rule="evenodd" d="M10.273 2.513l-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911l-1.318.016z"/>
        </svg>
        <b>What is it and how do I use it?</b>
    </div>
    <div class="card-body">
        <p>
            This tool analyses user stories that have been completed in the past to forecast future velocity, or, total work output.
            Any filtering parameters can be applied in Jira to achieve a forecast based on those parameters.
            For example providing data targeted to a specific team will result in a forecast to that team. The resulting
            graph can be used, for example, in planning future sprints of any length.
        </p>
        <p>
            Result is achieved through <a href="https://en.wikipedia.org/wiki/Monte_Carlo_method" target="_blank">Monte Carlo simulation</a>
            which randomizes the provided Jira data and iterates through the data several times ({{iterations-count}} by default)
            to achieve, not just the average, but a wider range of values and their probabilities for future output.
            The end result is a histogram graph in the form of a "bell curve", where high bars in the middle represent
            the most probable values you should look for.
        </p>
        <p>
            Additionally, the tool will present a trend graph to visualise changes in velocity over time, within the time window included
            in the Jira output file. With the trend information it is easier to adapt the probability data with changes occurring
            with time when analysing a longer period. For example, when analysing a whole year's worth of data, there might be a natural
            factor affecting recent output, when compared with earliest available data, that will not be reflected in the histogram.
        </p>
        <ul style="list-style-type: none;">
            <li>1. Log in to your Jira account and navigate to <span class="text-info">https://&lt;path-to-jira&gt;/issues</span></li>
            <li>2. Use filters to build the data set suitable for your need. Only completed issues should be included</li>
            <li><ul><li>Advanced filter example: <code>"resolution = Done AND assignee in (membersOf("Name of your team")) ORDER BY resolved DESC"</code></li></ul></li>
            <li><ul><li><b>Important!</b> Make sure <code>"resolution = Done"</code> filter is always active to filter out incomplete issues!</li></ul></li>
            <li>3. Select just the following columns to be visible, in this order: <code>"Resolved", "Story Points"</code></li>
            <li><ul><li>Practically any columns may be visible, but make sure to have the first two columns from left as described above</li></ul></li>
            <li><ul><li>If you do not have valid story point data available, you can ignore the story points column. Just make sure to always use "Number of stories" metric with the tool</li></ul></li>
            <li>4. Export -> CSV (Current fields) -> Delimiter: ","</li>
        </ul>
        The content of your csv file should look like this:
        <div style="background-color: #eee; padding: 10px;">
            <code>
                Resolved,Custom field (Story Points)<br />
                2020-08-10 15:20,3.0<br />
                2020-08-10 13:13,4.0<br />
                2020-08-07 13:17,2.0<br />
                ...
            </code>
        </div><br />
        <p>
            Country selector at the top right corner is used to include national holidays, together with weekends, during data analysis.
        </p>
    </div>
</div>