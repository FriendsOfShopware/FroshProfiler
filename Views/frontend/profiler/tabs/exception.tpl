<h2>Exception</h2>

{if empty($sDetail.exception)}
    <div class="empty">
        <p>No exception was thrown and caught during the request.</p>
    </div>
{else}
    <table>
        <thead>
            <tr>
                <th>Key</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Message</td>
                <td>{$sDetail.exception->getMessage()}</td>
            </tr>
            <tr>
                <td>Code</td>
                <td>{$sDetail.exception->getCode()}</td>
            </tr>
            <tr>
                <td>File</td>
                <td>{$sDetail.exception->getFile()} Line: {$sDetail.exception->getLine()}</td>
            </tr>
            <tr>
                <td>Traceback</td>
                <td>
                    <pre>
                        {$sDetail.exception->getTraceAsString()}
                    </pre>
                </td>
            </tr>
        </tbody>
    </table>
{/if}