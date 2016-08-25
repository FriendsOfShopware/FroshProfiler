{if $mode == "context"}
    {$data|dump}
{elseif $mode == 'bodyPlain'}
    <pre>
        {$data}
    </pre>
{else}
    {$data}
{/if}
