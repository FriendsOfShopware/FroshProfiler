<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <meta name="robots" content="noindex,nofollow"/>
        <meta name="viewport" content="width=device-width,initial-scale=1"/>
        <title>Shopware Profiler</title>
        <link rel="icon" type="image/x-icon" sizes="16x16" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAEvSURBVDhPpZNNKwZRGIb9DJIlC1aW8hNk4+sXKGVh4yv/wA+wseEVKcWKvFZM8rGyQdkpRREbibfe6HLumTMz55yZUW/uumt6zn2unvOcM23ta/AflwK6ajBah/lzWLiAMfOtWlm2AJgzm14bFPTylayFeQ+wcWfTVs+fUDM1dTF1AjOn0L9TARDd1fJVdduuY4CCbturt36odxuGD2BwDzqcuhwDNDBXA7t5YOQQmj92wej+HSaPA4DO6Kp7Mw/oJso0Hf0BULspQMdbuYbGt1200p4MEB4heoLO9Rwi92zB0iXcvCUZvY0MEA5Rqj9AnxmeC0k9cZTfUAyQw2uUPprJjbhDDZ0B5PAhuYoeYWjf3yx7ALnqKas2e+Zn5QJA1vnSn2nRTHu8lZ+pNcMvftQb0vt46xsAAAAASUVORK5CYII=">
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
