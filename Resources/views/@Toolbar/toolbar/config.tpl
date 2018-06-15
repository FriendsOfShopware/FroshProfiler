<div class="sf-toolbar-block sf-toolbar-block-config shopware-block sf-toolbar-status-normal sf-toolbar-block-right">
    <a href="{url controller=profiler action=detail id=$sProfilerID panel=config}">
        <div class="sf-toolbar-icon">
            <span class="sf-toolbar-label shopware-icon">
                {fetchFile file="@Toolbar/_resources/svg/shopware.svg"}
            </span>
            <span class="sf-toolbar-value">{if $sProfiler.php.shopware_version == '___VERSION___'}Git Version{else}{$sProfiler.php.shopware_version}{/if}</span>
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
                    &nbsp; <a href="{url controller=profiler action=php}" target="_blank">View phpinfo()</a>
                </span>
            </div>

            <div class="sf-toolbar-info-piece sf-toolbar-info-php-ext">
                <b>PHP Extensions</b>
                <span class="sf-toolbar-status {if $sProfiler.php.xdebug}sf-toolbar-status-green{else}sf-toolbar-status-red{/if}">xdebug</span>
                <span class="sf-toolbar-status {if $sProfiler.php.accel}sf-toolbar-status-green{else}sf-toolbar-status-red{/if}">accel</span>
            </div>

            <div class="sf-toolbar-info-piece">
                <b>PHP SAPI</b>
                <span>{$sProfiler.php.sapi}</span>
            </div>
        </div>
    </div>
</div>