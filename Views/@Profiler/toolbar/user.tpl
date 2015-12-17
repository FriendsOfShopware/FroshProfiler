<div class="sf-toolbar-block sf-toolbar-block-security sf-toolbar-status-normal">
    <a>
        <div class="sf-toolbar-icon">
            <span class="ion-person icon-toolbar"></span>
            <span class="sf-toolbar-value">{if !empty($sProfiler.user.loggedin)}{$sProfiler.user.billingaddress.firstname} {$sProfiler.user.billingaddress.lastname}{else}Guest{/if}</span>
        </div>
    </a>
    {if !empty($sProfiler.user.loggedin)}
        <div class="sf-toolbar-info">
            <div class="sf-toolbar-info-piece">
                <b>Logged in as</b>
                <span>{$sProfiler.user.billingaddress.firstname} {$sProfiler.user.billingaddress.lastname}</span>
            </div>
            <div class="sf-toolbar-info-piece">
                <b>ID:</b>
                <span>{$sProfiler.user.additional.user.id}</span>
            </div>
            <div class="sf-toolbar-info-piece">
                <b>Customernumber:</b>
                <span>{$sProfiler.user.billingaddress.customernumber}</span>
            </div>
        </div>
    {/if}
</div>