{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_header_css_screen" append}
    <link rel="stylesheet" href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
{/block}

{block name="frontend_index_header_javascript_jquery" append}
    <div class="sf-toolbarreset clear-fix">
        {foreach from=$sProfilerCollectors item=sProfilerCollector}
            {if !empty($sProfilerCollector->getToolbarTemplate())}
                {include file=$sProfilerCollector->getToolbarTemplate()}
            {/if}
        {/foreach}
        <a class="hide-button" title="Close Toolbar" onclick="document.querySelector('.sf-toolbarreset').remove();">
            <i class="ion-close-round"></i>
        </a>
    </div>
    <script>
        function microtime(get_as_float) {
            var now = new Date().getTime() / 1000;
            var s = parseInt(now, 10);

            return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s;
        }
        $profilerStartTime = $('[data-profiler-start-time]');
        $profilerStartTime.html((Math.floor((microtime(true) - parseFloat($profilerStartTime.data('profiler-start-time'))) * 100) / 100).toString());
    </script>
{/block}