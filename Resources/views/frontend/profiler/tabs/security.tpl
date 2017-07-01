{function name=getActiveIcon var=false}
    {if $var}
        {fetchFile file="@Toolbar/_resources/svg/yes.svg"}
    {else}
        {fetchFile file="@Toolbar/_resources/svg/no.svg"}
    {/if}
{/function}

<h2>Security</h2>

<div class="metrics">
    <div class="metric">
        <span class="value">{getActiveIcon var=$sDetail.user.loggedin}</span>
        <span class="label">Authenticated</span>
    </div>
    {if $sDetail.user.loggedin}
        <div class="metric">
            <span class="value">{$sDetail.user.additional.user.firstname} {$sDetail.user.additional.user.lastname}</span>
            <span class="label">Name</span>
        </div>
        <div class="metric">
            <span class="value">{$sDetail.user.additional.user.customernumber}</span>
            <span class="label">Customernumber</span>
        </div>
        <div class="metric">
            <span class="value">{$sDetail.user.additional.user.customergroup}</span>
            <span class="label">Customergroup</span>
        </div>
    {/if}
</div>

{if !empty($sDetail.user.data)}
    <h2>User Data</h2>
    {$sDetail.user.data|dump}
{/if}

<h2>Password encoders <small>({$sDetail.user.encoders|count})</small></h2>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Encoder class</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$sDetail.user.encoders item=encoder key=key}
            <tr>
                <td class="font-normal text-small text-muted nowrap">{$key}</td>
                <td class="font-normal">{$encoder}</td>
            </tr>
        {/foreach}
    </tbody>
</table>