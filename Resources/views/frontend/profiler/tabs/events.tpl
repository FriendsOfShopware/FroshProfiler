<h2>Event Dispatcher</h2>

<div class="sf-tabs">
    <div class="tab">
        <h3 class="tab-title">Called Listeners ({$sDetail.events.eventAmount})</h3>

        <h4>Filters</h4>
        <form method="get">
            <input type="checkbox" name="showContainerEvents" value="1"{if $eventFilter.showContainerEvents} checked{/if}> Show container events<br>
            <input type="text" name="search" placeholder="Search for events" value="{$eventFilter.search}">
            <button class="btn">Filter</button>
        </form>
        <br>


        <div class="tab-content">
            <table>
                <thead>
                    <tr>
                        <th style="width: 40%">Type</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$sDetail.events.calledEvents item=event key=key}
                        <tr>
                            <td>{$event.type}</td>
                            <td>
                                {$event.name}
                                {if $event.type == 'notifyUntil'}
                                    <br>
                                    Canceled: {$event.cancel|@var_dump}
                                {/if}
                                <br>
                                <button class="btn" data-toggle-div="args-{$key}">Toggle Arguments</button>
                                {if $event.type == 'filter'}
                                    <button class="btn" data-toggle-div="filter-{$key}">Toggle Filter returns</button>
                                {/if}

                                <div id="args-{$key}" style="display: none;">
                                    {$event.args|dump}
                                </div>

                                {if $event.type == 'filter'}
                                    <div id="filter-{$key}" style="display: none">
                                        Before:<br>
                                        {$event.old|dump}<br>
                                        After: <br>
                                        {$event.new|dump}
                                    </div>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>