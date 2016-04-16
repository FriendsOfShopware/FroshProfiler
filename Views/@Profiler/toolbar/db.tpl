<div class="sf-toolbar-block sf-toolbar-block-db sf-toolbar-status-normal">
    <a href="{url controller=Profiler action=detail id=$sProfilerID panel=db}">
        <div class="sf-toolbar-icon">
            <span class="ion-erlenmeyer-flask icon-toolbar"></span>
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
            <span>{$sProfiler.db.queryTime} ms</span>
        </div>
    </div>
</div>