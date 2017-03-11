<div class="sf-toolbar-block sf-toolbar-block-db sf-toolbar-status-normal">
    <a href="{url controller=profiler action=detail id=$sProfilerID panel=events}">
        <div class="sf-toolbar-icon">
            {fetchFile file="@Toolbar/_resources/svg/event.svg"}
            <span class="sf-toolbar-value">Events</span>
        </div>
    </a>
    <div class="sf-toolbar-info">
        <div class="sf-toolbar-info-piece">
            <b>registered events</b>
            <span class="sf-toolbar-status">{$sProfiler.events.eventAmount}</span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>called events</b>
            <span class="sf-toolbar-status">{count($sProfiler.events.calledEvents)}</span>
        </div>
    </div>
</div>