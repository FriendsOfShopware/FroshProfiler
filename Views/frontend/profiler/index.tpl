<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <meta name="robots" content="noindex,nofollow"/>
        <meta name="viewport" content="width=device-width,initial-scale=1"/>
        <title>Symfony Profiler</title>
        {include file="frontend/profiler/css.tpl"}
    </head>
    <body>
        <div id="header">
            <div class="container">
                <h1>
                    Symfony
                    <span>Profiler</span>
                </h1>
            </div>
        </div>

        <div id="content" class="container">
            <div id="main">
                <div id="collector-wrapper">
                    <div id="collector-content">
                        <h2>{$sIndex|@count} results found</h2>

                        <table id="search-results">
                            <thead>
                            <tr>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col">IP</th>
                                <th scope="col">Method</th>
                                <th scope="col">URL</th>
                                <th scope="col">Time</th>
                                <th scope="col">Token</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$sIndex item=sItem key=sKey}
                                <tr>
                                    <td class="text-center">
                                        <span class="label {if $sItem.httpResponse == 200}status-success{else}status-error{/if}">{$sItem.httpResponse}</span>
                                    </td>
                                    <td>
                                        <span class="nowrap">{$sItem.ip}</span>
                                    </td>
                                    <td>
                                        {$sItem.httpMethod}
                                    </td>
                                    <td class="break-long-words">
                                        {$sItem.uri}
                                    </td>
                                    <td class="text-small">
                                        <span class="nowrap">{$sItem.time|date:'d-m-Y'}</span>
                                        <span class="nowrap newline">{$sItem.time|date:'h:M:s'}</span>
                                    </td>
                                    <td class="nowrap"><a href="{url controller=Profiler action=detail id=$sKey}">{$sKey}</a>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
