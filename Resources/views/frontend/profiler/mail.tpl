{if $mode == "context"}
    {$data|dump}
{elseif $mode == 'bodyPlain'}
    <pre>
        {$data|escape}
    </pre>
{else}
    <iframe src="data:text/html;base64,{$data|base64_encode}" height="100%" width="100%" sandbox></iframe>
{/if}