{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_header_css_screen" append}
    <link rel="stylesheet" href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
{/block}

{block name="frontend_index_header_javascript_jquery" append}
    <div id="sfToolbarClearer-ca75a3" style="height: 38px;"></div>
    <div id="sfToolbarMainContent-ca75a3" class="sf-toolbarreset clear-fix" data-no-turbolink>
        {foreach from=$sProfilerCollectors item=sProfilerCollector}
            {if !empty($sProfilerCollector->getToolbarTemplate())}
                {include file=$sProfilerCollector->getToolbarTemplate()}
            {/if}
        {/foreach}
        <a class="hide-button" title="Close Toolbar" tabindex="-1" accesskey="D" onclick="
            var p = this.parentNode;
            p.style.display = 'none';
            (p.previousElementSibling || p.previousSibling).style.display = 'none';
            document.getElementById('sfMiniToolbar-ca75a3').style.display = 'block';
            Sfjs.setPreference('toolbar/displayState', 'none');
        ">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" height="24" viewBox="0 0 24 24"
                 enable-background="new 0 0 24 24" xml:space="preserve">
<path fill="#AAAAAA" d="M21.1,18.3c0.8,0.8,0.8,2,0,2.8c-0.4,0.4-0.9,0.6-1.4,0.6s-1-0.2-1.4-0.6L12,14.8l-6.3,6.3
    c-0.4,0.4-0.9,0.6-1.4,0.6s-1-0.2-1.4-0.6c-0.8-0.8-0.8-2,0-2.8L9.2,12L2.9,5.7c-0.8-0.8-0.8-2,0-2.8c0.8-0.8,2-0.8,2.8,0L12,9.2
    l6.3-6.3c0.8-0.8,2-0.8,2.8,0c0.8,0.8,0.8,2,0,2.8L14.8,12L21.1,18.3z"/>
</svg>

        </a>
    </div>
    <script>
        function microtime(get_as_float) {
            var now = new Date().getTime() / 1000;
            var s = parseInt(now, 10);

            return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s;
        }
        $profilerStartTime = $('[data-profiler-start-time]');
        $profilerStartTime.html((Math.floor((microtime(true) -  parseFloat($profilerStartTime.data('profiler-start-time'))) * 100) / 100).toString());
    </script>
{/block}