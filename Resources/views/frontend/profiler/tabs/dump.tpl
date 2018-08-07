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

        <a class="text-small sf-toggle" data-toggle-selector="#sf-trace-{$dump@index}" data-toggle-alt-content="Hide code">Show code</a>
    </h3>

    <div class="sf-dump-compact hidden" id="sf-trace-{$dump@index}">
        <div class="trace">
            {if $dump.fileExcerpt}
                {$dump.fileExcerpt}
            {/if}
             {* else:  $dump.file|file_excerpt(dump.line) }} *}
        </div>
    </div>

    {$dump.data}
</div>
{foreachelse}
<div class="empty">
    <p>No content was dumped.</p>
</div>
{/foreach}
