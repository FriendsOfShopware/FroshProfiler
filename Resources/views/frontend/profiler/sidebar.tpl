<div id="sidebar">
    <ul id="menu-profiler">
        <li class="request{if $sPanel == "request"} selected{/if}">
            <a href="{url controller=profiler action=detail id=$sId panel=request}">
                <span class="label">
                    <span class="icon">
                        {fetchFile file="@Toolbar/_resources/svg/request.svg"}
                    </span>
                <strong>Request / Response</strong>
                </span>
            </a>
        </li>
        <li class="time{if $sPanel == "time"} selected{/if}">
            <a href="{url controller=profiler action=detail id=$sId panel=time}">
                <span class="label disabled">
                    <span class="icon">
                        {fetchFile file="@Toolbar/_resources/svg/time.svg"}
                    </span>
                    <strong>Performance</strong>
                </span>
            </a>
        </li>
        <li class="exception{if $sPanel == "exception"} selected{/if}">
            <a href="{url controller=profiler action=detail id=$sId panel=exception}">
                <span class="label{if empty($sDetail.exception)} disabled{/if}">
                    <span class="icon">
                        {fetchFile file="@Toolbar/_resources/svg/exception.svg"}
                    </span>
                    <strong>Exception</strong>
                </span>
            </a>
        </li>
        <li class="logger{if $sPanel == "logs"} selected{/if}">
            <a href="{url controller=profiler action=detail id=$sId panel=logs}">
                <span class="label">
                    <span class="icon">
                        {fetchFile file="@Toolbar/_resources/svg/logger.svg"}
                    </span>
                    <strong>Logs</strong>
                </span>
            </a>
        </li>
        <li class="events{if $sPanel == "events"} selected{/if}">
            <a href="{url controller=profiler action=detail id=$sId panel=events}">
                <span class="label">
                    <span class="icon">
                        {fetchFile file="@Toolbar/_resources/svg/event.svg"}
                    </span>
                <strong>Events</strong>
                </span>
            </a>
        </li>
        <li class="security{if $sPanel == "security"} selected{/if}">
            <a href="{url controller=profiler action=detail id=$sId panel=security}">
                <span class="label">
                    <span class="icon">
                        {fetchFile file="@Toolbar/_resources/svg/person.svg"}
                    </span>
                    <strong>Security</strong>
                </span>
            </a>
        </li>
        <li class="twig{if $sPanel == "template"} selected{/if}">
            <a href="{url controller=profiler action=detail id=$sId panel=template}">
                <span class="label">
                    <span class="icon">
                        {fetchFile file="@Toolbar/_resources/svg/template.svg"}
                    </span>
                    <strong>Smarty</strong>
                </span>
            </a>
        </li>
        <li class="doctrine{if $sPanel == "db"} selected{/if}">
            <a href="{url controller=profiler action=detail id=$sId panel=db}">
                <span class="label">
                    <span class="icon">
                        {fetchFile file="@Toolbar/_resources/svg/database.svg"}
                    </span>
                    <strong>Database ({$sDetail.db.sqls|count})</strong>
                </span>
            </a>
        </li>
        <li class="subrequest{if $sPanel == "subrequest"} selected{/if}">
            <a href="{url controller=profiler action=detail id=$sId panel=subrequest}">
                <span class="label{if $sDetail.subrequest|count == 0} disabled{/if}">
                    <span class="icon">
                        {fetchFile file="@Toolbar/_resources/svg/ajax.svg"}
                    </span>
                    <strong>Subrequest ({$sDetail.subrequest|count})</strong>
                </span>
            </a>
        </li>
        <li class="swiftmailer{if $sPanel == "mailer"} selected{/if}">
            <a href="{url controller=profiler action=detail id=$sId panel=mailer}">
                <span class="label{if $sDetail.mails|count == 0} disabled{/if}">
                    <span class="icon">
                        {fetchFile file="@Toolbar/_resources/svg/mailer.svg"}
                    </span>
                    <strong>E-Mails ({$sDetail.mails|count})</strong>
                </span>
            </a>
        </li>
        <li class="config{if $sPanel == "config"} selected{/if}">
            <a href="{url controller=profiler action=detail id=$sId panel=config}">
                <span class="label">
                    <span class="icon">
                        {fetchFile file="@Toolbar/_resources/svg/config.svg"}
                    </span>
                    <strong>Configuration</strong>
                </span>
            </a>
        </li>
    </ul>
</div>
