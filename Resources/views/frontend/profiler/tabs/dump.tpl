<h2>Dumped Contents</h2>

{foreach $sDetail.dump.html as $dump}
    <div class="sf-dump sf-reset">
        <h3>In
            {if $dump.file}
                {if $dump.fileLink}
                    <a href="{$dump.fileLink}" title="{$dump.file}">{$dump.name}</a>
                {else}
                    <abbr title="{$dump.file}">{$dump.name}</abbr>
                {/if}
            {else}
                {$dump.name}
            {/if}

            <small>line {$dump.line}</small>

            {if $dump.fileExcerpt}
                <a class="text-small sf-toggle" data-toggle-selector="#sf-trace-{$dump@index}" data-toggle-alt-content="Hide code">Show code</a>
            {/if}
        </h3>

        {if $dump.fileExcerpt}
            <div class="sf-dump-compact hidden" id="sf-trace-{$dump@index}">
                <div class="trace">
                    {$dump.fileExcerpt}
                </div>
            </div>
        {/if}

        {$dump.data}
    </div>
{foreachelse}
    <div class="empty">
        <p>No content was dumped.</p>
    </div>
{/foreach}
