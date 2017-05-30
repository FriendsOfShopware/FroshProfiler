<div class="sf-toolbar-block sf-toolbar-block-request sf-toolbar-status-normal">
    <a href="{url controller=profiler action=detail id=$sProfilerID}">
        <div class="sf-toolbar-icon"><span class="sf-toolbar-status {if $sProfiler.response.httpResponse == 200}sf-toolbar-status-green{else}sf-toolbar-status-red{/if}">{$sProfiler.response.httpResponse}</span>
            <span class="sf-toolbar-label">@</span>
            <span class="sf-toolbar-value sf-toolbar-info-piece-additional">{controllerName}</span>
        </div>
    </a>
    <div class="sf-toolbar-info">
        <div class="sf-toolbar-info-piece">
            <b>HTTP status</b>
            <span>{$sProfiler.response.httpResponse}</span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Controller</b>
                <span>
                    Shopware_Controllers_{$sProfiler.request.moduleName|ucfirst}_{$sProfiler.request.controllerName|ucfirst}&nbsp;::&nbsp;{$sProfiler.request.actionName|ucfirst}
                </span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Controller class</b>
            <span>Shopware_Controllers_{$sProfiler.request.moduleName|ucfirst} _{$sProfiler.request.controllerName|ucfirst}</span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Is logged in?</b>
            <span>{if !empty($sProfiler.user.loggedin)}yes{else}no{/if}</span>
        </div>
    </div>
</div>
<div class="sf-toolbar-block sf-toolbar-block-time sf-toolbar-status-normal">
    <a href="{url controller=profiler action=detail id=$sProfilerID panel=time}">
        <div class="sf-toolbar-icon">
            {fetchFile file="@Toolbar/_resources/svg/time.svg"}

            <span class="sf-toolbar-value">{$sProfilerTime}</span>
            <span class="sf-toolbar-label">s</span>
        </div>
    </a>
    <div class="sf-toolbar-info">
        <div class="sf-toolbar-info-piece">
            <b>Total time</b>
            <span>{$sProfilerTime}</span>
            <span>s</span>
        </div>
    </div>
</div>
