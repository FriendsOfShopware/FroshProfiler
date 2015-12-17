<div class="sf-toolbar-block sf-toolbar-block-request sf-toolbar-status-normal">
    <a>
        <div class="sf-toolbar-icon"><span class="sf-toolbar-status sf-toolbar-status-green">200</span>
            <span class="sf-toolbar-label">@</span>
            <span class="sf-toolbar-value sf-toolbar-info-piece-additional">{controllerName}</span>
        </div>
    </a>
    <div class="sf-toolbar-info">
        <div class="sf-toolbar-info-piece">
            <b>HTTP status</b>
            <span>{$sProfiler.response->getHttpResponseCode()}</span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Controller</b>
                <span>
                    Shopware_Controllers_{$sProfiler.request->getModuleName()|ucfirst}_{$sProfiler.request->getControllerName()|ucfirst}&nbsp;::&nbsp;{controllerAction}
                </span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Controller class</b>
            <span>Shopware_Controllers_{$sProfiler.request->getModuleName()|ucfirst} _{$sProfiler.request->getControllerName()|ucfirst}</span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Is logged in?</b>
            <span>{if !empty($sProfiler.user.loggedin)}yes{else}no{/if}</span>
        </div>
    </div>
</div>
<div class="sf-toolbar-block sf-toolbar-block-time sf-toolbar-status-normal">
    <a>
        <div class="sf-toolbar-icon">
            <span class="ion-clock icon-toolbar"></span>

            <span class="sf-toolbar-value" data-profiler-start-time="{$sProfiler.startTime}"></span>
            <span class="sf-toolbar-label">s</span>
        </div>
    </a>
    <div class="sf-toolbar-info">
        <div class="sf-toolbar-info-piece">
            <b>Total time</b>
            <span data-profiler-start-time="{$sProfiler.startTime}"></span>
            <span>s</span>
        </div>
    </div>
</div>