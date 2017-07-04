var collapseFields = document.querySelectorAll('[data-toggle-div]');

for (var i = 0; i < collapseFields.length; i++) {
    collapseFields[i].addEventListener('click', function (event) {
        event.preventDefault();
        var toggleDiv = document.getElementById(event.target.getAttribute('data-toggle-div'));

        if (toggleDiv) {
            if (toggleDiv.style.display === 'block') {
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
var search = docsearch({
    apiKey: '4c10d9397401c1dbbbae98ad3897c5e0',
    indexName: 'shopware',
    inputSelector: '#search-id',
    debug: false,

    algoliaOptions: {
        hitsPerPage: 7
    }
});