{if $sProfiler.dump.count}
<div class="sf-toolbar-block sf-toolbar-block-dump sf-toolbar-status-normal">
    <a href="{url controller=profiler action=detail id=$sProfilerID panel=dump}">
        <div class="sf-toolbar-icon">
            {fetchFile file="@Toolbar/_resources/svg/dump.svg"}
            <span class="sf-toolbar-value">{$sProfiler.dump.count}</span>
        </div>
    </a>
    <div class="sf-toolbar-info">

        {foreach $sProfiler.dump.html as $dump}
            <div class="sf-toolbar-info-piece">
                <span>
                    {if $dump.file}
                        {if $dump.fileLink}
                            <a href="{$dump.fileLink}" title="{$dump.file}">{$dump.name}</a>
                        {else}
                            <abbr title="{$dump.file}">{$dump.name}</abbr>
                        {/if}
                    {else}
                        {$dump.name}
                    {/if}
                </span>

                <span class="sf-toolbar-file-line">line {$dump.line}</span>

                {$dump.data}
            </div>
        {/foreach}

    </div>
</div>
{/if}
