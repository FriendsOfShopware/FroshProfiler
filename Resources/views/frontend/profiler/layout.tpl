<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <meta name="robots" content="noindex,nofollow"/>
        <meta name="viewport" content="width=device-width,initial-scale=1"/>
        <title>Symfony Profiler</title>
        <link rel="icon" type="image/x-icon" sizes="16x16" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFEUlEQVR4AZVXA4wm2RMf27bXDM/+3/+sYBGfrbVtezc6BWtzfPbYXtvDL9906t6v0vWl05me7q1JzXuvvu4yXnvZgJ9hH6bwZYXLFR739vauUGuDwhq1L1N4Uv/tRYUhFjwcg49hn6aYr1V4TiGp86CoP9Oh1tV414KnM6t9fHymKUZ3DAI0hW4b1AyK3lE8phh5OxWeoJgUGhi5mLm95YzBwcHuhIQEV1JSEoWGhoKWHxYWFmenhJ/B5W0GwZpDt5Ovry9lZWWRyWOu5ORk7JsUpogsq5gnmISTU+HKQoLFQv/qq6/os88+I+EVFRUlSsRZ5oRiVmwlXMWShQkahUdERJCfnx/vd+3aRTU1NXTixAmqrq6mK1eu0PTp05mnrmD+QK6XhLO0XP2O2FJAQICRjjMU4P1PP/1EfX19NGfOHM8Z0N7ezueQkBBXYGAgSWIaQ5Em2T5QzFNSUig9PV3OHOe4uDjZ87p//34C7Nm7x/NcRUUFAX799Vec8Y7m7+8Pz92SfBDXr7VwPYRbxn/MmDG8Tps2jQBd3V30/PPPe35/6qmnaPXq1TR69Gg+h4eHiwwosdLT4dBkQDSXWmJiIq/vv/8+/fvvv3ThwgWqr6+n/Px8oyCmAerq6jy03Nxc2Yv7ySSjQzrmi4i92fVpaWlYOZ79/f2MW7dtpSlTptDp06epo6ODPvroI850ASiGdyZOnEjXrl2jyspKT4XA9cgjkaPL/D8UWG62HokieyQQoKSkRGiMs2bNotraWmprayOBNWvWyO+scGdnp5zF/WYvLEb8TwpRykp1MV7feust6uzqJMD169fpueeeY/rDDz/MKzzgdrsJoGkaffvtt/TFF19wQsIDmzZtssojt+6Fo1CgzKiAvAB3DRs2jAULtLS0eErPGB5Ad3c3lZaWUnFxMfeAd955h5+JjY3FaqXAPwhBnRCNySK4b98+Aoilv/z6i/zGggSk1g0opWupAMvGP91yt96zadWqVdTc3Ezz58/31LOAy+US6zgHBP766y+mDR8+HBUgFWSnQI2EAFnqlpcaGxsJIFkMN8L9AnPnzmX6jRs3SACeAi0vL888JwYPgTEJpauhnADo6/LSgQMHCHD37l2Cp15//XXq7eslgKb+Fi1exM9lZmbaCDclIcpQQhATE4OVsrOzuamg+cyePZuzG64Hrlu3jp9ZuWolCdy+fZueeOIJpkdHR1sLHqgM0Yh0bTRz1m7fvp2KiopYkYKCApo8ebLZIwzlFeXSOXEnsLPe2Ij+p5DbYYdOdOtDQ0rNjFya5sTcsGGDcTDZoXTcNoVBMoxWyzDS2yXmOyeUtGSskmDjx4/nRgPAfBDmMpZtUIbRcsi2GsfSD2QYyd2OcdmyZSSwdu1apuXk5GB16v4bak0yX0imyIUEgwNovFTglhMZGcm0srIy43zAVUxuTLbW4xn17Fci23wly9dngUummrTaixcvMpOtW7fiiBwQpqKYU9efHuxDJE5hC9wvL9TW1RLg+PHjPGTQ8wsLC4WpDC5Y5UR4k5qKMSLT6lqeAiX0nuAaMmSI9sMPP9CZM2foyJEj9O677wpTVIuTjidNp0HibvttoH9E5OMqbWKkSaNSlojldoLF7TEP+nUEmKI62y1kOBINbVaNarcI0PuGGUlHyfYvLHg7/jhFSFYqZh0P8KHSptd5ksOPU3tvqAEUot/hFmOIYJLp87wGe9Dwm95eg5xa/R8G6d8U5EcFhwAAAABJRU5ErkJggg==">
        {include file="frontend/profiler/css.tpl"}
    </head>
    <body>
    <div id="header">
        <div class="container">
            <h1>
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                    <path fill="#AAAAAA" d="M12,0.9C5.8,0.9,0.9,5.8,0.9,12s5,11.1,11.1,11.1s11.1-5,11.1-11.1S18.2,0.9,12,0.9z M18.5,6.9
                        c-0.6,0-0.9-0.3-0.9-0.8c0-0.2,0-0.4,0.2-0.6c0.1-0.3,0.2-0.3,0.2-0.4c0-0.3-0.5-0.4-0.6-0.4c-1.8,0.1-2.3,2.5-2.7,4.4l-0.2,1
                        c1,0.2,1.8,0,2.2-0.3c0.6-0.4-0.2-0.7-0.1-1.2c0.1-0.3,0.5-0.5,0.7-0.6c0.5,0,0.7,0.5,0.7,0.9c0,0.7-1,1.8-3,1.8
                        c-0.3,0-0.5,0-0.6-0.1L13.8,13c-0.4,1.6-0.8,3.8-2.4,5.7c-1.4,1.7-2.9,1.9-3.5,1.9c-1.2,0-1.9-0.6-2-1.5c0-0.8,0.7-1.3,1.2-1.3
                        c0.6,0,1.1,0.5,1.1,1c0,0.5-0.2,0.6-0.4,0.6c-0.1,0.1-0.3,0.2-0.3,0.4c0,0.1,0.1,0.3,0.4,0.3c0.5,0,0.8-0.3,1.1-0.5
                        c1.2-0.9,1.6-2.7,2.2-5.7l0.1-0.7c0.2-1,0.5-2.1,0.7-3.2c-0.8-0.6-1.3-1.4-2.4-1.7C9,8.2,8.5,8.4,8.1,8.8c-0.4,0.5-0.2,1.1,0.2,1.5
                        L9,10.9c0.7,0.8,1.2,1.6,1,2.5C9.7,14.9,8,16,6,15.3c-1.8-0.6-2-1.8-1.8-2.5c0.2-0.6,0.6-0.7,1.1-0.6c0.5,0.2,0.6,0.7,0.6,1.2
                        c0,0.1,0,0.1-0.1,0.3c-0.2,0.1-0.3,0.3-0.3,0.4c-0.1,0.4,0.4,0.6,0.7,0.7c0.7,0.3,1.6-0.2,1.8-0.8c0.2-0.6-0.2-1-0.4-1.1l-0.7-0.8
                        c-0.4-0.4-1.1-1.4-0.7-2.6C6.3,9,6.6,8.6,6.9,8.2c0.9-0.6,1.8-0.7,2.8-0.6c1.2,0.4,1.8,1.1,2.6,1.8c0.5-1.2,1-2.4,1.8-3.5
                        C15,5,16,4.3,17.2,4.2c1.3,0.2,2.2,0.7,2.2,1.6C19.4,6.2,19.2,6.9,18.5,6.9z"></path>
                </svg>
                Symfony <span>Profiler</span>
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
