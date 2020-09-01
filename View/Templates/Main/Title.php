<div class="row" style="margin-bottom: 40px;">
    <div class="col-9">
        <h1>Jira velocity forecast tool</h1>
    </div>
    <?php if (!$hasData): ?>
        <div class="col-3 text-right">
            <div class="dropdown show" style="margin: 5px 15px 0 0;">
                <a class="btn btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border: 1px solid silver;">
                    <span class="flag-icon flag-icon-<?= \strtolower($currentCountry) ?>"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <?php foreach (COUNTRIES as $country): ?>
                        <a class="dropdown-item" href="?c=<?= $country ?>">
                            <span class="flag-icon flag-icon-<?= \strtolower($country) ?>"></span> <?= $country ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
