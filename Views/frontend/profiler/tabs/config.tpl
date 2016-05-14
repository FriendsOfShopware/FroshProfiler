{function name=getActiveIcon var=false}
    {if $var}
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="28" height="28" viewBox="0 0 12 12" enable-background="new 0 0 12 12" xml:space="preserve">
            <path fill="#5E976E" d="M12,3.1c0,0.4-0.1,0.8-0.4,1.1L5.9,9.8c-0.3,0.3-0.6,0.4-1,0.4c-0.4,0-0.7-0.1-1-0.4L0.4,6.3
            C0.1,6,0,5.6,0,5.2c0-0.4,0.2-0.7,0.4-0.9C0.6,4,1,3.9,1.3,3.9c0.4,0,0.8,0.1,1.1,0.4l2.5,2.5l4.7-4.7c0.3-0.3,0.7-0.4,1-0.4
            c0.4,0,0.7,0.2,0.9,0.4C11.8,2.4,12,2.7,12,3.1z"></path>
        </svg>
    {else}
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="28" height="28" viewBox="0 0 12 12" enable-background="new 0 0 12 12" xml:space="preserve">
            <path fill="#B0413E" d="M10.4,8.4L8,6l2.4-2.4c0.8-0.8,0.7-1.6,0.2-2.2C10,0.9,9.2,0.8,8.4,1.6L6,4L3.6,1.6C2.8,0.8,2,0.9,1.4,1.4
            C0.9,2,0.8,2.8,1.6,3.6L4,6L1.6,8.4C0.8,9.2,0.9,10,1.4,10.6c0.6,0.6,1.4,0.6,2.2-0.2L6,8l2.4,2.4c0.8,0.8,1.6,0.7,2.2,0.2
            C11.1,10,11.2,9.2,10.4,8.4z"></path>
        </svg>
    {/if}
{/function}

<h2>Shopware Configuration</h2>

<div class="metrics">
    <div class="metric">
        <span class="value">{if $sDetail.php.shopware_version == '___VERSION___'}Git Version{else}{$sDetail.php.shopware_version}{/if}</span>
        <span class="label">Shopware version</span>
    </div>

    <div class="metric">
        <span class="value">{$sDetail.php.env}</span>
        <span class="label">Environment</span>
    </div>
</div>

<h2>PHP Configuration</h2>

<div class="metrics">
    <div class="metric">
        <span class="value">{$sDetail.php.version}</span>
        <span class="label">PHP version</span>
    </div>

    <div class="metric">
        <span class="value">
            {getActiveIcon var=$sDetail.php.httpcache}
        </span>
        <span class="label">HttpCache</span>
    </div>

    <div class="metric">
        <span class="value">
            {getActiveIcon var=$sDetail.php.xdebug}
        </span>
        <span class="label">Xdebug</span>
    </div>
</div>

<div class="metrics metrics-horizontal">
    <div class="metric">
        <span class="value">
            {getActiveIcon var=$sDetail.php.opcache}
        </span>
        <span class="label">OPcache</span>
    </div>

    <div class="metric">
        <span class="value">
            {getActiveIcon var=$sDetail.php.apc}
        </span>
        <span class="label">APC</span>
    </div>
</div>

<p>
    <a href="{url controller=Profiler action=php}">View full PHP configuration</a>
</p>

<h2>Enabled Bundles <small>({$sDetail.bundles|count})</small></h2>
<table>
    <thead>
        <tr>
            <th class="key">Name</th>
            <th>Path</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$sDetail.bundles item=bundle}
            <tr>
                <th scope="row" class="font-normal">{$bundle[0]}</th>
                <td class="font-normal">{$bundle[1]}</td>
            </tr>
        {/foreach}
    </tbody>
</table>