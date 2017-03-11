{function name=getActiveIcon var=false}
    {if $var}
        {fetchSvg file="@Toolbar/_resources/svg/yes.svg"}
    {else}
        {fetchSvg file="@Toolbar/_resources/svg/no.svg"}
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
    <a href="{url controller=profiler action=php}">View full PHP configuration</a>
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