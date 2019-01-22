<div class="sf-tabs">
    <div class="tab">
        <h3 class="tab-title">Request</h3>

        <div class="tab-content">
            <h3>GET Parameters</h3>

            {if empty($sDetail.request.get)}
                <div class="empty">
                    <p>No GET parameters</p>
                </div>
            {else}
                <table class="">
                    <thead>
                    <tr>
                        <th scope="col" class="key">Key</th>
                        <th scope="col">Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$sDetail.request.get key=key item=value}
                        <tr>
                            <td>{$key|escape}</td>
                            <td>{$value|escape}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            {/if}

            <h3>POST Parameters</h3>

            {if empty($sDetail.request.post)}
                <div class="empty">
                    <p>No Post parameters</p>
                </div>
            {else}
                <table class="">
                    <thead>
                    <tr>
                        <th scope="col" class="key">Key</th>
                        <th scope="col">Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$sDetail.request.post key=key item=value}
                        <tr>
                            <td>{$key|escape}</td>
                            <td>{$value|escape}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            {/if}

            <h3>Request Attributes</h3>

            <table class="">
                <thead>
                <tr>
                    <th scope="col" class="key">Key</th>
                    <th scope="col">Value</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th>_controller</th>
                    <td>Shopware_Controllers_{$sDetail.request.moduleName|ucfirst}_{$sDetail.request.controllerName|ucfirst}::{$sDetail.request.actionName}Action</td>
                </tr>
                <tr>
                    <th>_route_params</th>
                    <td>{$sDetail.request.params|@json_encode|escape}</td>
                </tr>
                </tbody>
            </table>

            <h3>Server Parameters</h3>
            <table class="">
                <thead>
                <tr>
                    <th scope="col" class="key">Key</th>
                    <th scope="col">Value</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$sDetail.server key=key item=value}
                    <tr>
                        <td>{$key|escape}</td>
                        <td>{$value|escape}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab">
        <h3 class="tab-title">Cookies</h3>

        <div class="tab-content">
            <h3>Request Cookies</h3>

            <table class="">
                <thead>
                <tr>
                    <th scope="col" class="key">Header</th>
                    <th scope="col">Value</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$sDetail.request.cookies key=key item=value}
                    <tr>
                        <td>{$key|escape}</td>
                        <td>{$value|escape}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab">
        <h3 class="tab-title">Response</h3>

        <div class="tab-content">
            <h3>Response Headers</h3>

            <table class="">
                <thead>
                <tr>
                    <th scope="col" class="key">Header</th>
                    <th scope="col">Value</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$sDetail.response.headers key=key item=value}
                    <tr>
                        <td>{$key|escape}</td>
                        <td>{$value|@implode:" "|escape}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>

        </div>
    </div>

    <div class="tab ">
        <h3 class="tab-title">Session</h3>

        <div class="tab-content">
            <h3>Session Metadata</h3>

            <table class="">
                <thead>
                <tr>
                    <th scope="col" class="key">Key</th>
                    <th scope="col">Value</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th scope="row">Last used</th>
                    <td>{$sDetail.session.meta.modified|date_format:"Y-m-d H:i:s"}</td>
                </tr>
                <tr>
                    <th scope="row">Lifetime</th>
                    <td>{$sDetail.session.meta.expiry}</td>
                </tr>
                </tbody>
            </table>
            <h3>Session Attributes</h3>
            <table class="">
                <thead>
                <tr>
                    <th scope="col" class="key">Attribute</th>
                    <th scope="col">Value</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$sDetail.session.data key=key item=value}
                    <tr>
                        <td>{$key|escape}</td>
                        <td>{$value|@json_encode|escape}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab{if count($sDetail.subrequest) === 0} disabled{/if}">
        <h3 class="tab-title">Sub Requests <span class="badge">{$sDetail.subrequest|count}</span></h3>

        <div class="tab-content">
            <table>
                <thead>
                <tr>
                    <th scope="col" class="key">Url</th>
                    <th scope="col">Controller</th>
                    <th scope="col">Action</th>
                    <th scope="col">Time</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$sDetail.subrequest key=key item=sSubrequest}
                    <tr>
                        <td>{$sSubrequest.request.url|escape}</td>
                        <td>{$sSubrequest.request.controllerName|ucfirst|escape}</td>
                        <td>{$sSubrequest.request.actionName|ucfirst|escape}</td>
                        <td>
                            <a href="{url controller=profiler action=detail id=$sId|cat:'|':$key}" class="btn">Open Subprofile</a>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>