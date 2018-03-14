<h2>Queries</h2>

<div class="metrics">
    <div class="metric">
        <span class="value">{$sDetail.db.totalQueries}</span>
        <span class="label">Total Queries</span>
    </div>

    <div class="metric">
        <span class="value">{$sDetail.db.queryTime|number_format:4} s</span>
        <span class="label">Total Query Time</span>
    </div>
</div>

<table class="">
    <thead>
        <tr>
            <th scope="col" class="key">SQL</th>
            <th scope="col">Params</th>
            <th scope="col">Execution Time</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$sDetail.db.sqls item=sql}
            <tr>
                <td>{$sql.sql|sqlFormat}</td>
                <td>{$sql.params|dump}</td>
                <td>{$sql.execution|number_format:4} ms</td>
            </tr>
        {/foreach}
    </tbody>
</table>