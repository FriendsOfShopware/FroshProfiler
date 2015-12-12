<div class="sf-toolbar-block sf-toolbar-block-security sf-toolbar-status-normal">
    <a>
        <div class="sf-toolbar-icon">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" height="24"
                 viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                        <path fill="#AAAAAA"
                              d="M21,20.4V22H3v-1.6c0-3.7,2.4-6.9,5.8-8c-1.7-1.1-2.9-3-2.9-5.2c0-3.4,2.7-6.1,6.1-6.1s6.1,2.7,6.1,6.1c0,2.2-1.2,4.1-2.9,5.2C18.6,13.5,21,16.7,21,20.4z"/>
                    </svg>
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