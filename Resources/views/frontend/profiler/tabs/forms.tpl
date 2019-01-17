<h2>Forms</h2>

{function name=form_tree_entry treename="" data="" root=true}
    {$has_error = !empty($data.errors)}
    <li>
        <div class="tree-inner" data-tab-target-id="{$data.id}-details">
            {if $has_error}
                <div class="badge-error">{count($data.errors)}</div>
            {/if}

            {if !empty($data.children)}
                <a class="toggle-button" data-toggle-target-id="{$data.id}-children" href="#"><span class="toggle-icon"></span></a>
            {else}
                <div class="toggle-icon empty"></div>
            {/if}

            <span {if $has_error or $data.has_children_error}class="has-error"{/if}>
                {$treename}
            </span>
        </div>

        {if !empty($data.children)}
            <ul id="{$data.id}-children" {if $is_root and !$data.has_children_error}class="hidden"{/if}>
                {foreach from=$data.children item=childData key=childName}
                    {call name=form_tree_entry treename=$childName data=$childData root=false}
                {/foreach}
            </ul>
        {/if}
    </li>
{/function}

{function name=form_tree_details treename="" data="" forms_by_hash="" show=""}
<div class="tree-details{if !$show} hidden{/if}" {if $data.id}id="{$data.id}-details"{/if}>
    <h2 class="dump-inline">
        {$treename} {if !empty($data.type_class)}{$data.type_class|dump}{/if}
    </h2>

    {if !empty($data.errors)}
    <div class="errors">
        <h3>
            <a class="toggle-button" data-toggle-target-id="{$data.id}-errors" href="#">
                Errors <span class="toggle-icon"></span>
            </a>
        </h3>

        <table id="{$data.id}-errors">
            <thead>
            <tr>
                <th>Message</th>
                <th>Origin</th>
                <th>Cause</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$data.errors item=error}
                <tr>
                    <td>{$error.message}</td>
                    <td>
                        {if !empty($error.origin)}
                            <em>This form.</em>
                        {elseif $forms_by_hash[$error.origin]}
                            <em>Unknown.</em>
                        {else}
                            {$forms_by_hash[$error.origin].name}
                        {/if}
                    </td>
                    <td>
                        {if $error.trace}
                            <span class="newline">Caused by:</span>
                            {foreach from=$error.trace item=stacked}
                                {$stacked|dump|escape}
                            {/foreach}
                        {else}
                            <em>Unknown.</em>
                        {/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {/if}

    {if $data.default_data}
    <h3>
        <a class="toggle-button" data-toggle-target-id="{$data.id}-default_data" href="#">
            Default Data <span class="toggle-icon"></span>
        </a>
    </h3>

    <div id="{$data.id}-default_data">
        <table>
            <thead>
            <tr>
                <th width="180">Property</th>
                <th>Value</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th class="font-normal" scope="row">Model Format</th>
                <td>
                    {if $data.default_data.model}
                        {$data.default_data.model}
                    {else}
                        <em class="font-normal text-muted">same as normalized format</em>
                    {/if}
                </td>
            </tr>
            <tr>
                <th class="font-normal" scope="row">Normalized Format</th>
                <td>{$data.default_data.norm}</td>
            </tr>
            <tr>
                <th class="font-normal" scope="row">View Format</th>
                <td>
                    {if $data.default_data.view}
                        {$data.default_data.view}
                    {else}
                        <em class="font-normal text-muted">same as normalized format</em>
                    {/if}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    {/if}

    {if $data.submitted_data}
    <h3>
        <a class="toggle-button" data-toggle-target-id="{$data.id}-submitted_data" href="#">
            Submitted Data <span class="toggle-icon"></span>
        </a>
    </h3>

    <div id="{$data.id}-submitted_data">
        {if $data.submitted_data.norm}
        <table>
            <thead>
            <tr>
                <th width="180">Property</th>
                <th>Value</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th class="font-normal" scope="row">View Format</th>
                <td>
                    {if $data.submitted_data.view}
                        {$data.submitted_data.view}
                    {else}
                        <em class="font-normal text-muted">same as normalized format</em>
                    {/if}
                </td>
            </tr>
            <tr>
                <th class="font-normal" scope="row">Normalized Format</th>
                <td>{$data.submitted_data.norm}</td>
            </tr>
            <tr>
                <th class="font-normal" scope="row">Model Format</th>
                <td>
                    {if $data.submitted_data.model}
                        {$data.submitted_data.model}
                    {else}
                        <em class="font-normal text-muted">same as normalized format</em>
                    {/if}
                </td>
            </tr>
            </tbody>
        </table>
        {else}
        <div class="empty">
            <p>This form was not submitted.</p>
        </div>
        {/if}
    </div>
    {/if}

    {if $data.passed_options}
    <h3>
        <a class="toggle-button" data-toggle-target-id="{$data.id}-passed_options" href="#">
            Passed Options <span class="toggle-icon"></span>
        </a>
    </h3>

    <div id="{$data.id}-passed_options">
        {if !empty($data.passed_options)}
        <table>
            <thead>
            <tr>
                <th width="180">Option</th>
                <th>Passed Value</th>
                <th>Resolved Value</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$data.passed_options key=option item=value}
            <tr>
                <th>{$option}</th>
                <td>{$value|dump}</td>
                <td>
                    {$option_value = $value.value}
                    {$resolved_option_value = $data.resolved_options[$option].value}
                    {if $resolved_option_value == $option_value}
                        <em class="font-normal text-muted">same as passed value</em>
                    {else}
                        {$data.resolved_options[$option]|dump}
                    {/if}
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
        {else}
        <div class="empty">
            <p>No options where passed when constructing this form.</p>
        </div>
        {/if}
    </div>
    {/if}

    {if $data.resolved_options}
    <h3>
        <a class="toggle-button" data-toggle-target-id="{$data.id}-resolved_options" href="#">
            Resolved Options <span class="toggle-icon"></span>
        </a>
    </h3>

    <div id="{$data.id}-resolved_options" class="hidden">
        <table>
            <thead>
            <tr>
                <th width="180">Option</th>
                <th>Value</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$data.resolved_options key=option item=value}
                <tr>
                    <th scope="row">{$option}</th>
                    <td>{$value|dump}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {/if}

    {if $data.view_vars}
    <h3>
        <a class="toggle-button" data-toggle-target-id="{$data.id}-view_vars" href="#">
            View Variables <span class="toggle-icon"></span>
        </a>
    </h3>

    <div id="{$data.id}-view_vars" class="hidden">
        <table>
            <thead>
            <tr>
                <th width="180">Variable</th>
                <th>Value</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$data.view_vars key=variable item=value}
                <tr>
                    <th scope="row">{$variable}</th>
                    <td>{$value|dump}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {/if}
</div>

    {foreach from=$data.children key=childName item=childData}
        {call name=form_tree_details treename=$childName data=$childData forms_by_hash=$forms_by_hash}
    {/foreach}
{/function}

{if empty($sDetail.forms.forms)}
    <div class="empty">
        <p>No forms were submitted for this request.</p>
    </div>
{else}
    <div id="tree-menu" class="tree">
        <ul>
            {foreach from=$sDetail.forms.forms item=formData key=formName}
                {call name=form_tree_entry treename=$formName data=$formData root=true}
            {/foreach}
        </ul>
    </div>

    <div id="tree-details-container">
        {foreach from=$sDetail.forms.forms item=formData key=formName}
            {call name=form_tree_details treename=$formName data=$formData forms_by_hash=$sDetail.forms.forms_by_hash}
        {/foreach}
    </div>
{/if}