{if !empty($sUsedSnippets)}
    {literal}
    <a class="btn is--primary btn-snippet-fixed" href="#" onclick="$.modal.open($('#usedSnippets').html(), {width: $(window).width(), height: $(window).height() - 100, title: 'Used snippets'}); return false;">{/literal}Used snippets on this site ({$sUsedSnippets|count})</a>
    <div id="usedSnippets" style="display: none">
        <table class="table-fill">
            <thead>
            <tr>
                <td>Namespace</td>
                <td>Name</td>
                <td>Content</td>
            </tr>
            </thead>
            <tbody>
            {foreach from=$sUsedSnippets item=snippet}
                <tr>
                    <td>{$snippet.namespace}</td>
                    <td>{$snippet.name}</td>
                    <td>{$snippet.content}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
{/if}

<style>{fetchFile file="@Toolbar/_resources/css/toolbar.css"}</style>
<div class="sf-toolbarreset clear-fix">
    {foreach from=$sProfilerCollectors item=sProfilerCollector}
        {if !empty($sProfilerCollector->getToolbarTemplate())}
            {include file=$sProfilerCollector->getToolbarTemplate()}
        {/if}
    {/foreach}
    <a class="hide-button" title="Close Toolbar" onclick="closeToolbar()">
        {fetchFile file="@Toolbar/_resources/svg/close.svg"}
    </a>
</div>
<script type="application/javascript">
    function applyProfilerHeader() {
        if (window.CSRF) {
            var ajaxBeforeSend = window.CSRF._ajaxBeforeSend;

            window.CSRF._ajaxBeforeSend = function (event, request) {
                ajaxBeforeSend.apply(window.CSRF, arguments);
                request.setRequestHeader('X-Profiler', '{$sProfilerID}');
            };
        }
    }

    applyProfilerHeader();

    if (typeof document.asyncReady !== 'undefined') {
        document.asyncReady(applyProfilerHeader);
    }


    function closeToolbar()
    {
        document.querySelector('.sf-toolbarreset').remove();
        document.cookie = "disableProfile=1";
    }
</script>
