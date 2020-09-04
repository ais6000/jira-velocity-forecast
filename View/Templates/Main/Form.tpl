 <form action="{{baseUrl}}" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-xl form-item-container">
            <label for="file">1. CSV file exported from Jira</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="file" name="{{paramFile}}">
                <label class="custom-file-label" for="file">Choose file</label>
            </div>
        </div>
        <div class="col-xl form-item-container">
            <label for="days">2. Number of working days to forecast for:</label>
            <input type="text" class="form-control" id="days" name="{{paramDays}}" placeholder="10 equals two week sprint" value="10" required>
        </div>
        <div class="col-xl form-item-container">
            <label for="metric">3. Forecast metric</label>
            <select class="custom-select d-block w-100" id="metric" name="{{paramMetric}}">
                <option value="{{metricNumOfStories}}">Number of stories</option>
                <option value="{{metricStoryPoints}}">Story points</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <button type="submit" class="btn btn-primary btn-lg btn-block">Calculate</button>
        </div>
    </div>
</form>
