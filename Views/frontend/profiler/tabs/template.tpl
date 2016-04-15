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
