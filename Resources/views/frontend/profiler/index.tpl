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
                                <td class="nowrap"><a href="{url controller=Profiler action=detail id=$sItem.token}">{$sItem.token}</a>
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
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24" xml:space="preserve">
                                    <path fill="#AAAAAA" d="m 2.571,17.5 18.859,0 c 0.87,0 1.57,0.7 1.57,1.57 l 0,1.57 c 0,0.87 -0.7,1.57 -1.57,1.57 l -18.859,0 C 1.702,22.21 1,21.51 1,20.64 L 1,19.07 C 1,18.2 1.702,17.5 2.571,17.5 Z M 1,11.21 1,12.79 c 0,0.86 0.702,1.56 1.571,1.56 l 18.859,0 c 0.87,0 1.57,-0.7 1.57,-1.56 l 0,-1.58 C 23,10.35 22.3,9.644 21.43,9.644 l -18.859,0 C 1.702,9.644 1,10.35 1,11.21 Z M 1,3.357 1,4.929 c 0,0.869 0.702,1.572 1.571,1.572 l 18.859,0 C 22.3,6.501 23,5.798 23,4.929 L 23,3.357 C 23,2.489 22.3,1.786 21.43,1.786 l -18.859,0 C 1.702,1.786 1,2.489 1,3.357 Z"></path>
                                </svg>
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