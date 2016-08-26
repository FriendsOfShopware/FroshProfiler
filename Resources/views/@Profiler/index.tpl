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
<script type="application/javascript">
    var ajaxBeforeSend = window.CSRF._ajaxBeforeSend;

    window.CSRF._ajaxBeforeSend = function (event, request) {
        ajaxBeforeSend.apply(window.CSRF, [event, request]);
        request.setRequestHeader('X-Profiler', '{$sProfilerID}');
    };
</script>
