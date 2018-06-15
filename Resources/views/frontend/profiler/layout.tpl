<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <meta name="robots" content="noindex,nofollow"/>
        <meta name="viewport" content="width=device-width,initial-scale=1"/>
        <title>Shopware Profiler</title>
        <link rel="icon" type="image/x-icon" sizes="16x16" href="{link file="frontend/profiler/_resources/favicon.ico"}">
        <link rel="stylesheet" href="{link file="frontend/profiler/_resources/css/detail.css"}"/>
    </head>
    <body{if {controllerAction} === "detail" && $sPanel == "time"} class="time"{/if}>
    <div id="header">
        <div class="container">
            <h1>
                {fetchFile file="@Toolbar/_resources/svg/shopware.svg"}
                Shopware <span>Profiler</span>
            </h1>
            <div class="search">
                <div class="form-row">
                    <input name="q" id="search-id" type="search" placeholder="search on developers.shopware.com">
                </div>
            </div>
        </div>
    </div>
        {block name="content"}{/block}
        {if $sPanel === "time"}
        <script src="{link file="frontend/profiler/_resources/js/Chart.bundle.js"}"></script>
        {/if}
        <script src="{link file="frontend/profiler/_resources/js/docsearch.min.js"}"></script>
        <script src="{link file="frontend/profiler/_resources/js/app.js"}"></script>
    </body>
</html>
