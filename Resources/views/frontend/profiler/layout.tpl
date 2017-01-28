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
                    var toggleDiv = document.getElementById(event.target.getAttribute('data-toggle-div'));

                    if (toggleDiv) {
                        if (toggleDiv.style.display == 'block') {
                            toggleDiv.style.display = 'none';
                        } else {
                            toggleDiv.style.display = 'block';
                        }
                    }
                })
            }

            var btnWindow = document.querySelectorAll('.btn-window');

            for (i = 0; i < btnWindow.length; i++) {
                btnWindow[i].addEventListener('click', function (event) {
                    event.preventDefault();


                    window.open(event.target.getAttribute('href'), '', 'width=800,height=700')
                })
            }
        </script>
    </body>
</html>
