<h2>Smarty Metrics</h2>
<div class="metrics">
    <div class="metric">
        <span class="value">{$sDetail.template.renderTime|number_format:2} <span class="unit">s</span></span>
        <span class="label">Render time</span>
    </div>
    <div class="metric">
        <span class="value">{$sDetail.template.templateCalls}</span>
        <span class="label">Template calls</span>
    </div>

    <div class="metric">
        <span class="value">{$sDetail.template.blockCalls}</span>
        <span class="label">Block calls</span>
    </div>
</div>

<h2>Rendered Templates</h2>
<table class="">
    <thead>
        <tr>
            <th scope="col">Template Name</th>
            <th scope="col">Render Count</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$sDetail.template.renderedTemplates key=template item=count}
            <tr>
                <td>{$template}</td>
                <td>{$count}</td>
            </tr>
        {/foreach}
    </tbody>
</table>

<h2>Template Attributes</h2>
<table class="">
    <thead>
        <tr>
            <th scope="col" class="key">Key</th>
            <th scope="col">Value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Cache Dir</th>
            <td>{$sDetail.template.cache_dir}</td>
        </tr>
        <tr>
            <th>Compile Dir</th>
            <td>{$sDetail.template.cache_dir}</td>
        </tr>
        <tr>
            <th>Templates Directories</th>
            <td>{$sDetail.template.template_dir|dump}</td>
        </tr>
        <tr>
            <th>Rendered Templates</th>
            <td>{$sDetail.template.template|dump}</td>
        </tr>
    </tbody>
</table>

<h2>Template Vars</h2>

{$sDetail.template.vars|dump}
