<div class="sf-toolbar-block sf-toolbar-block-twig sf-toolbar-status-normal ">
    <a href="{url controller=profiler action=detail id=$sProfilerID panel=template}">
        <div class="sf-toolbar-icon">
            {fetchFile file="@Toolbar/_resources/svg/template.svg"}
            <span class="sf-toolbar-value">Template</span>
        </div>
    </a>
    <div class="sf-toolbar-info">
        <div class="sf-toolbar-info-piece">
            <b>Template</b>
            <span>
                {foreach from=$sProfiler.template.template item=template}
                    {$template}
                {/foreach}
            </span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Template folders</b>
            <span>
                {foreach from=$sProfiler.template.template_dir item=template}
                    <span class="block">{$template}</span>
                {/foreach}
            </span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Plugin folders</b>
            <span>
                {foreach from=$sProfiler.template.plugin_dir item=template}
                    <span class="block">{$template}</span>
                {/foreach}
            </span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Cache folder</b>
            <span>
                {$sProfiler.template.cache_dir}
            </span>
        </div>
        <div class="sf-toolbar-info-piece">
            <b>Compile folder</b>
            <span>
                {$sProfiler.template.compile_dir}
            </span>
        </div>
    </div>
</div>