<h2>Mails Send</h2>

<table class="">
    <thead>
        <tr>
            <th scope="col" class="key">From</th>
            <th scope="col">To</th>
            <th scope="col">Subject</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
    {foreach from=$sDetail.mails key=id item=data}
        <tr>
            <td>{$data.from|escape}</td>
            <td>{$data.to|dump|escape}</td>
            <td>{$data.subject|escape}</td>
            <td>
                <a class="btn btn-window" href="{url action=mail mode=bodyHtml id=$sId mailId=$id}">Show Html Mail</a>
                <a class="btn btn-window" href="{url action=mail mode=bodyPlain id=$sId mailId=$id}">Show Plain Mail</a>
                <a class="btn btn-window" href="{url action=mail mode=context id=$sId mailId=$id}">Show Context</a>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
