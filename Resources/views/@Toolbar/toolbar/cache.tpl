<div class="sf-toolbar-block sf-toolbar-block-cache sf-toolbar-status-normal">
    <a href="{url controller=profiler action=detail id=$sProfilerID panel=cache}">
        <div class="sf-toolbar-icon">
            {fetchFile file="@Toolbar/_resources/svg/cache.svg"}

            <span class="sf-toolbar-value">{$sProfiler.cache.calls}</span>
            <span class="sf-toolbar-info-piece-additional-detail">
                <span class="sf-toolbar-label">in</span>
                <span class="sf-toolbar-value">{$sProfiler.cache.time|number_format:4}</span>
                <span class="sf-toolbar-label">ms</span>
            </span>
        </div>
    </a>
    <div class="sf-toolbar-info">
        <div class="sf-toolbar-info-piece">
            <b>Cache Calls</b>
            <span>{$sProfiler.cache.calls}</span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Total time</b>
            <span>{$sProfiler.cache.time|number_format:4} ms</span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Cache hits</b>
            <span>{$sProfiler.cache.hit} / {$sProfiler.cache.read} ({(($sProfiler.cache.hit / $sProfiler.cache.read) * 100)|number_format:0}%)</span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Cache writes</b>
            <span>{$sProfiler.cache.write}</span>
        </div>
    </div>
</div>