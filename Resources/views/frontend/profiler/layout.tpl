<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <meta name="robots" content="noindex,nofollow"/>
        <meta name="viewport" content="width=device-width,initial-scale=1"/>
        <title>Symfony Profiler</title>
        {include file="frontend/profiler/css.tpl"}
    </head>
    <body>
        <div id="header">
            <div class="container">
                <h1>
                    Symfony
                    <span>Profiler</span>
                </h1>
            </div>
        </div>
        {block name="content"}{/block}
        <script>
            var collapseFields = document.querySelectorAll('[data-toggle-div]');

            for (var i = 0; i < collapseFields.length; i++) {
                collapseFields[i].addEventListener('click', function (event) {
                    event.preventDefault();
                    var toggleDiv = document.getElementById(event.srcElement.getAttribute('data-toggle-div'));

                    if (toggleDiv) {
                        if (toggleDiv.style.display == 'block') {
                            toggleDiv.style.display = 'none';
                        } else {
                            toggleDiv.style.display = 'block';
                        }
                    }
                })
            }
        </script>
    </body>
</html>