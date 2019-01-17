<h2>Log Messages</h2>
<div class="sf-tabs">
    <div class="tab">
        <h3 class="tab-title">Info. &amp; Errors <span class="badge">{$sDetail.logs.OTHER|count}</span></h3>
        <div class="tab-content">
            <table class="logs">
                <thead>
                    <tr>
                        <th>Level</th>
                        <th>Channel</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$sDetail.logs.OTHER item=log}
                        <tr class="">
                            <td class="font-normal text-small">
                                <span class="colored text-bold nowrap">{$log[0]|escape}</span>
                                <span class="text-muted nowrap newline">{$log[3]|date_format:"H:i:s"}</span>
                            </td>

                            <td class="font-normal text-small text-bold nowrap">{$log[4]|escape}</td>

                            <td class="font-normal">
                                {$log[1]}<br>
                                Context: {$log[2]|@json_encode|escape}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>

        </div>
    </div>

    <div class="tab">
        <h3 class="tab-title">Debug <span class="badge">{$sDetail.logs.DEBUG|count}</span></h3>
        <div class="tab-content">
            <table class="logs">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Channel</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$sDetail.logs.DEBUG item=log}
                        <tr class="">
                            <td class="font-normal text-small">
                                <span class="colored text-bold nowrap">{$log[0]|escape}</span>
                                <span class="text-muted nowrap newline">{$log[3]|date_format:"H:i:s"}</span>
                            </td>

                            <td class="font-normal text-small text-bold nowrap">{$log[4]|escape}</td>

                            <td class="font-normal">
                                {$log[1]}<br>
                                Context: {$log[2]|@json_encode}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>