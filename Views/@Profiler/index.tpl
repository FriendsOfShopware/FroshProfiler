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
        function microtime() {
            return new Date().getTime();
        }
        $profilerStartTime = $('[data-profiler-start-time]');
        $profilerStartTime.html(((microtime() - parseFloat($profilerStartTime.data('profiler-start-time'))) / 1000).toString());
    </script>
{/block}