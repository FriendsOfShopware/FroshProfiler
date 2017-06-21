<h2>Cache</h2>

<div class="metrics">
    <div class="metric">
        <span class="value">{$sDetail.cache.calls}</span>
        <span class="label">Total calls</span>
    </div>
    <div class="metric">
        <span class="value">{$sDetail.cache.time|number_format:4} <span class="unit">ms</span></span>
        <span class="label">Total time</span>
    </div>
    <div class="metric-divider"></div>
    <div class="metric">
        <span class="value">{$sDetail.cache.read}</span>
        <span class="label">Total reads</span>
    </div>
    <div class="metric">
        <span class="value">{$sDetail.cache.write}</span>
        <span class="label">Total writes</span>
    </div>
    <div class="metric">
        <span class="value">{$sDetail.cache.delete}</span>
        <span class="label">Total deletes</span>
    </div>
    <div class="metric-divider"></div>
    <div class="metric">
        <span class="value">{$sDetail.cache.hit}</span>
        <span class="label">Total hits</span>
    </div>
    <div class="metric">
        <span class="value">{$sDetail.cache.hitMissed}</span>
        <span class="label">Total misses</span>
    </div>
    <div class="metric">
        <span class="value">
            {(($sDetail.cache.hit / $sDetail.cache.read) * 100)|number_format:0} <span class="unit">%</span>
        </span>
        <span class="label">Hits/reads</span>
    </div>
</div>