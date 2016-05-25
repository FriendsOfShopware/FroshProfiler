<link rel="stylesheet" href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
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
