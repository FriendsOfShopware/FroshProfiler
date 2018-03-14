<div class="sf-toolbar-block sf-toolbar-block-db sf-toolbar-status-normal">
    <a href="{url controller=profiler action=detail id=$sProfilerID panel=db}">
        <div class="sf-toolbar-icon">
            {fetchFile file="@Toolbar/_resources/svg/database.svg"}
            <span class="sf-toolbar-value">Database</span>
        </div>
    </a>
    <div class="sf-toolbar-info">
        <div class="sf-toolbar-info-piece">
            <b>Database Queries</b>
            <span class="sf-toolbar-status">{$sProfiler.db.totalQueries}</span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Query time</b>
            <span>{$sProfiler.db.queryTime|number_format:4} s</span>
        </div>
    </div>
</div>