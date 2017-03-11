{extends file="frontend/profiler/layout.tpl"}

{block name="content"}
    <div id="summary">
        <div class="status">
            <div class="container">
                <h2>Profile Search</h2>
            </div>
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
                                    <span class="label {if $sItem.status == 200}status-success{else}status-error{/if}">{$sItem.status}</span>
                                </td>
                                <td>
                                    <span class="nowrap">{$sItem.ip}</span>
                                </td>
                                <td>
                                    {$sItem.method}
                                </td>
                                <td class="break-long-words">
                                    {$sItem.url}
                                </td>
                                <td class="text-small">
                                    <span class="nowrap">{$sItem.time|date:'F'}</span>
                                    <span class="nowrap newline">{$sItem.time|date:'H:m:s'}</span>
                                </td>
                                <td class="nowrap"><a href="{url controller=profiler action=detail id=$sItem.token}">{$sItem.token}</a>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="sidebar">
                <div id="sidebar-shortcuts">
                    <div class="shortcuts">
                        <a href="#" class="visible-small">
                            <span class="icon">
                                {fetchFile file="@Toolbar/_resources/svg/symfony.svg"}
                            </span>
                        </a>

                        <a class="btn btn-sm" href="{url controller=profiler limit=10}">Last 10</a>
                        <a class="btn btn-sm" href="{url controller=profiler limit=1}">Latest</a>

                        <div id="sidebar-search" class="sf-toggle-content sf-toggle-visible">
                            <form action="{url controller=profiler}" method="get">
                                <div class="form-group">
                                    <label for="ip">IP</label>
                                    <input name="ip" id="ip" value="{$params.ip}" type="text">
                                </div>

                                <div class="form-group">
                                    <label for="method">Method</label>
                                    <select name="method" id="method">
                                        <option value="">Any</option>
                                        <option{if $params.method == "DELETE"} selected{/if}>DELETE</option>
                                        <option{if $params.method == "GET"} selected{/if}>GET</option>
                                        <option{if $params.method == "HEAD"} selected{/if}>HEAD</option>
                                        <option{if $params.method == "PATCH"} selected{/if}>PATCH</option>
                                        <option{if $params.method == "POST"} selected{/if}>POST</option>
                                        <option{if $params.method == "PUT"} selected{/if}>PUT</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="url">URL</label>
                                    <input name="url" id="url" value="{$params.url}" type="text">
                                </div>

                                <div class="form-group">
                                    <label for="token">Token</label>
                                    <input name="token" id="token" value="{$params.token}" type="text">
                                </div>

                                <div class="form-group">
                                    <label for="start">From</label>
                                    <input name="start" id="start" value="{$params.start}" type="date">
                                </div>

                                <div class="form-group">
                                    <label for="end">Until</label>
                                    <input name="end" id="end" value="{$params.end}" type="date">
                                </div>

                                <div class="form-group">
                                    <label for="limit">Results</label>
                                    <select name="limit" id="limit">
                                        <option{if $params.limit == 10} selected{/if}>10</option>
                                        <option{if $params.limit == 50} selected{/if}>50</option>
                                        <option{if $params.limit == 100} selected{/if}>100</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-sm">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}