<div class="sf-toolbar-block sf-toolbar-block-config sf-toolbar-status-normal sf-toolbar-block-right">
    <a>
        <div class="sf-toolbar-icon">
            <span class="sf-toolbar-label">
                <img src="{link file="@Profiler/_public/img/logo.png"}">
            </span>
            <span class="sf-toolbar-value">{$sProfiler.php.shopware_version}</span>
        </div>
    </a>
    <div class="sf-toolbar-info">
        <div class="sf-toolbar-info-group">
            <div class="sf-toolbar-info-piece">
                <b>Environment</b>
                <span>{$sProfiler.php.env}</span>
            </div>
        </div>

        <div class="sf-toolbar-info-group">
            <div class="sf-toolbar-info-piece sf-toolbar-info-php">
                <b>PHP version</b>
                <span>
                    {$sProfiler.php.version}
                    &nbsp; <a href="{url controller=Profiler action=Phpinfo}">View phpinfo()</a>
                </span>
            </div>

            <div class="sf-toolbar-info-piece sf-toolbar-info-php-ext">
                <b>PHP Extensions</b>
                <span class="sf-toolbar-status {if $sProfiler.php.xdebug}sf-toolbar-status-green{else}sf-toolbar-status-red{/if}">xdebug</span>
                <span class="sf-toolbar-status {if $sProfiler.php.xdebug}sf-toolbar-status-green{else}sf-toolbar-status-red{/if}">accel</span>
            </div>

            <div class="sf-toolbar-info-piece">
                <b>PHP SAPI</b>
                <span>{$sProfiler.php.sapi}</span>
            </div>
        </div>
    </div>
</div>