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

Sfjs = (function() {
    "use strict";

    var classListIsSupported = 'classList' in document.documentElement;

    if (classListIsSupported) {
        var hasClass = function (el, cssClass) { return el.classList.contains(cssClass); };
        var removeClass = function(el, cssClass) { el.classList.remove(cssClass); };
        var addClass = function(el, cssClass) { el.classList.add(cssClass); };
        var toggleClass = function(el, cssClass) { el.classList.toggle(cssClass); };
    } else {
        var hasClass = function (el, cssClass) { return el.className.match(new RegExp('\\b' + cssClass + '\\b')); };
        var removeClass = function(el, cssClass) { el.className = el.className.replace(new RegExp('\\b' + cssClass + '\\b'), ' '); };
        var addClass = function(el, cssClass) { if (!hasClass(el, cssClass)) { el.className += " " + cssClass; } };
        var toggleClass = function(el, cssClass) { hasClass(el, cssClass) ? removeClass(el, cssClass) : addClass(el, cssClass); };
    }

    var noop = function() {};

    var profilerStorageKey = 'sf2/profiler/';

    var request = function(url, onSuccess, onError, payload, options) {
        var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        options = options || {};
        options.maxTries = options.maxTries || 0;
        xhr.open(options.method || 'GET', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function(state) {
            if (4 !== xhr.readyState) {
                return null;
            }

            if (xhr.status == 404 && options.maxTries > 1) {
                setTimeout(function(){
                    options.maxTries--;
                    request(url, onSuccess, onError, payload, options);
                }, 500);

                return null;
            }

            if (200 === xhr.status) {
                (onSuccess || noop)(xhr);
            } else {
                (onError || noop)(xhr);
            }
        };
        xhr.send(payload || '');
    };

    var getPreference = function(name) {
        if (!window.localStorage) {
            return null;
        }

        return localStorage.getItem(profilerStorageKey + name);
    };

    var setPreference = function(name, value) {
        if (!window.localStorage) {
            return null;
        }

        localStorage.setItem(profilerStorageKey + name, value);
    };

    var requestStack = [];

    var extractHeaders = function(xhr, stackElement) {
        /* Here we avoid to call xhr.getResponseHeader in order to */
        /* prevent polluting the console with CORS security errors */
        var allHeaders = xhr.getAllResponseHeaders();
        var ret;

        if (ret = allHeaders.match(/^x-debug-token:\s+(.*)$/im)) {
            stackElement.profile = ret[1];
        }
        if (ret = allHeaders.match(/^x-debug-token-link:\s+(.*)$/im)) {
            stackElement.profilerUrl = ret[1];
        }
    };

    var successStreak = 4;
    var pendingRequests = 0;
    var renderAjaxRequests = function() {
        var requestCounter = document.querySelector('.sf-toolbar-ajax-request-counter');
        if (!requestCounter) {
            return;
        }
        requestCounter.textContent = requestStack.length;

        var infoSpan = document.querySelector(".sf-toolbar-ajax-info");
        if (infoSpan) {
            infoSpan.textContent = requestStack.length + ' AJAX request' + (requestStack.length !== 1 ? 's' : '');
        }

        var ajaxToolbarPanel = document.querySelector('.sf-toolbar-block-ajax');
        if (requestStack.length) {
            ajaxToolbarPanel.style.display = 'block';
        } else {
            ajaxToolbarPanel.style.display = 'none';
        }
        if (pendingRequests > 0) {
            addClass(ajaxToolbarPanel, 'sf-ajax-request-loading');
        } else if (successStreak < 4) {
            addClass(ajaxToolbarPanel, 'sf-toolbar-status-red');
            removeClass(ajaxToolbarPanel, 'sf-ajax-request-loading');
        } else {
            removeClass(ajaxToolbarPanel, 'sf-ajax-request-loading');
            removeClass(ajaxToolbarPanel, 'sf-toolbar-status-red');
        }
    };

    var startAjaxRequest = function(index) {
        var tbody = document.querySelector('.sf-toolbar-ajax-request-list');
        if (!tbody) {
            return;
        }

        var request = requestStack[index];
        pendingRequests++;
        var row = document.createElement('tr');
        request.DOMNode = row;

        var methodCell = document.createElement('td');
        methodCell.textContent = request.method;
        row.appendChild(methodCell);

        var typeCell = document.createElement('td');
        typeCell.textContent = request.type;
        row.appendChild(typeCell);

        var statusCodeCell = document.createElement('td');
        var statusCode = document.createElement('span');
        statusCode.textContent = 'n/a';
        statusCodeCell.appendChild(statusCode);
        row.appendChild(statusCodeCell);

        var pathCell = document.createElement('td');
        pathCell.className = 'sf-ajax-request-url';
        if ('GET' === request.method) {
            var pathLink = document.createElement('a');
            pathLink.setAttribute('href', request.url);
            pathLink.textContent = request.url;
            pathCell.appendChild(pathLink);
        } else {
            pathCell.textContent = request.url;
        }
        pathCell.setAttribute('title', request.url);
        row.appendChild(pathCell);

        var durationCell = document.createElement('td');
        durationCell.className = 'sf-ajax-request-duration';
        durationCell.textContent = 'n/a';
        row.appendChild(durationCell);

        var profilerCell = document.createElement('td');
        profilerCell.textContent = 'n/a';
        row.appendChild(profilerCell);

        row.className = 'sf-ajax-request sf-ajax-request-loading';
        tbody.insertBefore(row, tbody.firstChild);

        renderAjaxRequests();
    };

    var finishAjaxRequest = function(index) {
        var request = requestStack[index];
        if (!request.DOMNode) {
            return;
        }
        pendingRequests--;
        var row = request.DOMNode;
        /* Unpack the children from the row */
        var methodCell = row.children[0];
        var statusCodeCell = row.children[2];
        var statusCodeElem = statusCodeCell.children[0];
        var durationCell = row.children[4];
        var profilerCell = row.children[5];

        if (request.error) {
            row.className = 'sf-ajax-request sf-ajax-request-error';
            methodCell.className = 'sf-ajax-request-error';
            successStreak = 0;
        } else {
            row.className = 'sf-ajax-request sf-ajax-request-ok';
            successStreak++;
        }

        if (request.statusCode) {
            if (request.statusCode < 300) {
                statusCodeElem.setAttribute('class', 'sf-toolbar-status');
            } else if (request.statusCode < 400) {
                statusCodeElem.setAttribute('class', 'sf-toolbar-status sf-toolbar-status-yellow');
            } else {
                statusCodeElem.setAttribute('class', 'sf-toolbar-status sf-toolbar-status-red');
            }
            statusCodeElem.textContent = request.statusCode;
        } else {
            statusCodeElem.setAttribute('class', 'sf-toolbar-status sf-toolbar-status-red');
        }

        if (request.duration) {
            durationCell.textContent = request.duration + 'ms';
        }

        if (request.profilerUrl) {
            profilerCell.textContent = '';
            var profilerLink = document.createElement('a');
            profilerLink.setAttribute('href', request.profilerUrl);
            profilerLink.textContent = request.profile;
            profilerCell.appendChild(profilerLink);
        }

        renderAjaxRequests();
    };

    var addEventListener;

    var el = document.createElement('div');
    if (!('addEventListener' in el)) {
        addEventListener = function (element, eventName, callback) {
            element.attachEvent('on' + eventName, callback);
        };
    } else {
        addEventListener = function (element, eventName, callback) {
            element.addEventListener(eventName, callback, false);
        };
    }


    return {
        hasClass: hasClass,

        removeClass: removeClass,

        addClass: addClass,

        toggleClass: toggleClass,

        getPreference: getPreference,

        setPreference: setPreference,

        addEventListener: addEventListener,

        request: request,

        renderAjaxRequests: renderAjaxRequests,

        load: function(selector, url, onSuccess, onError, options) {
            var el = document.getElementById(selector);

            if (el && el.getAttribute('data-sfurl') !== url) {
                request(
                    url,
                    function(xhr) {
                        el.innerHTML = xhr.responseText;
                        el.setAttribute('data-sfurl', url);
                        removeClass(el, 'loading');
                        for (var i = 0; i < requestStack.length; i++) {
                            startAjaxRequest(i);
                            if (requestStack[i].duration) {
                                finishAjaxRequest(i);
                            }
                        }
                        (onSuccess || noop)(xhr, el);
                    },
                    function(xhr) { (onError || noop)(xhr, el); },
                    '',
                    options
                );
            }

            return this;
        },

        toggle: function(selector, elOn, elOff) {
            var tmp = elOn.style.display,
                el = document.getElementById(selector);

            elOn.style.display = elOff.style.display;
            elOff.style.display = tmp;

            if (el) {
                el.style.display = 'none' === tmp ? 'none' : 'block';
            }

            return this;
        },

        createTabs: function() {
            var tabGroups = document.querySelectorAll('.sf-tabs');

            /* create the tab navigation for each group of tabs */
            for (var i = 0; i < tabGroups.length; i++) {
                var tabs = tabGroups[i].querySelectorAll('.tab');
                var tabNavigation = document.createElement('ul');
                tabNavigation.className = 'tab-navigation';

                for (var j = 0; j < tabs.length; j++) {
                    var tabId = 'tab-' + i + '-' + j;
                    var tabTitle = tabs[j].querySelector('.tab-title').innerHTML;

                    var tabNavigationItem = document.createElement('li');
                    tabNavigationItem.setAttribute('data-tab-id', tabId);
                    if (j == 0) { addClass(tabNavigationItem, 'active'); }
                    if (hasClass(tabs[j], 'disabled')) { addClass(tabNavigationItem, 'disabled'); }
                    tabNavigationItem.innerHTML = tabTitle;
                    tabNavigation.appendChild(tabNavigationItem);

                    var tabContent = tabs[j].querySelector('.tab-content');
                    tabContent.parentElement.setAttribute('id', tabId);
                }

                tabGroups[i].insertBefore(tabNavigation, tabGroups[i].firstChild);
            }

            /* display the active tab and add the 'click' event listeners */
            for (i = 0; i < tabGroups.length; i++) {
                tabNavigation = tabGroups[i].querySelectorAll('.tab-navigation li');

                for (j = 0; j < tabNavigation.length; j++) {
                    tabId = tabNavigation[j].getAttribute('data-tab-id');
                    document.getElementById(tabId).querySelector('.tab-title').className = 'hidden';

                    if (hasClass(tabNavigation[j], 'active')) {
                        document.getElementById(tabId).className = 'block';
                    } else {
                        document.getElementById(tabId).className = 'hidden';
                    }

                    tabNavigation[j].addEventListener('click', function(e) {
                        var activeTab = e.target || e.srcElement;

                        /* needed because when the tab contains HTML contents, user can click */
                        /* on any of those elements instead of their parent '<li>' element */
                        while (activeTab.tagName.toLowerCase() !== 'li') {
                            activeTab = activeTab.parentNode;
                        }

                        /* get the full list of tabs through the parent of the active tab element */
                        var tabNavigation = activeTab.parentNode.children;
                        for (var k = 0; k < tabNavigation.length; k++) {
                            var tabId = tabNavigation[k].getAttribute('data-tab-id');
                            document.getElementById(tabId).className = 'hidden';
                            removeClass(tabNavigation[k], 'active');
                        }

                        addClass(activeTab, 'active');
                        var activeTabId = activeTab.getAttribute('data-tab-id');
                        document.getElementById(activeTabId).className = 'block';
                    });
                }
            }
        },

        createToggles: function() {
            var toggles = document.querySelectorAll('.sf-toggle');

            for (var i = 0; i < toggles.length; i++) {
                var elementSelector = toggles[i].getAttribute('data-toggle-selector');
                var element = document.querySelector(elementSelector);

                addClass(element, 'sf-toggle-content');

                if (toggles[i].hasAttribute('data-toggle-initial') && toggles[i].getAttribute('data-toggle-initial') == 'display') {
                    addClass(toggles[i], 'sf-toggle-on');
                    addClass(element, 'sf-toggle-visible');
                } else {
                    addClass(toggles[i], 'sf-toggle-off');
                    addClass(element, 'sf-toggle-hidden');
                }

                addEventListener(toggles[i], 'click', function(e) {
                    e.preventDefault();

                    if ('' !== window.getSelection().toString()) {
                        /* Don't do anything on text selection */
                        return;
                    }

                    var toggle = e.target || e.srcElement;

                    /* needed because when the toggle contains HTML contents, user can click */
                    /* on any of those elements instead of their parent '.sf-toggle' element */
                    while (!hasClass(toggle, 'sf-toggle')) {
                        toggle = toggle.parentNode;
                    }

                    var element = document.querySelector(toggle.getAttribute('data-toggle-selector'));

                    toggleClass(toggle, 'sf-toggle-on');
                    toggleClass(toggle, 'sf-toggle-off');
                    toggleClass(element, 'sf-toggle-hidden');
                    toggleClass(element, 'sf-toggle-visible');

                    /* the toggle doesn't change its contents when clicking on it */
                    if (!toggle.hasAttribute('data-toggle-alt-content')) {
                        return;
                    }

                    if (!toggle.hasAttribute('data-toggle-original-content')) {
                        toggle.setAttribute('data-toggle-original-content', toggle.innerHTML);
                    }

                    var currentContent = toggle.innerHTML;
                    var originalContent = toggle.getAttribute('data-toggle-original-content');
                    var altContent = toggle.getAttribute('data-toggle-alt-content');
                    toggle.innerHTML = currentContent !== altContent ? altContent : originalContent;
                });
            }

            /* Prevents from disallowing clicks on links inside toggles */
            var toggleLinks = document.querySelectorAll('.sf-toggle a');
            for (var i = 0; i < toggleLinks.length; i++) {
                addEventListener(toggleLinks[i], 'click', function(e) {
                    e.stopPropagation();
                });
            }
        }
    };
})();

Sfjs.addEventListener(window, 'load', function() {
    Sfjs.createTabs();
    Sfjs.createToggles();
});

function Toggler(storage) {
    "use strict";
    var STORAGE_KEY = 'sf_toggle_data',
        states = {},
        isCollapsed = function (button) {
            return Sfjs.hasClass(button, 'closed');
        },
        isExpanded = function (button) {
            return !isCollapsed(button);
        },
        expand = function (button) {
            var targetId = button.dataset.toggleTargetId,
                target = document.getElementById(targetId);
            if (!target) {
                throw "Toggle target " + targetId + " does not exist";
            }
            if (isCollapsed(button)) {
                Sfjs.removeClass(button, 'closed');
                Sfjs.removeClass(target, 'hidden');
                states[targetId] = 1;
                storage.setItem(STORAGE_KEY, states);
            }
        },
        collapse = function (button) {
            var targetId = button.dataset.toggleTargetId,
                target = document.getElementById(targetId);
            if (!target) {
                throw "Toggle target " + targetId + " does not exist";
            }
            if (isExpanded(button)) {
                Sfjs.addClass(button, 'closed');
                Sfjs.addClass(target, 'hidden');
                states[targetId] = 0;
                storage.setItem(STORAGE_KEY, states);
            }
        },
        toggle = function (button) {
            if (Sfjs.hasClass(button, 'closed')) {
                expand(button);
            } else {
                collapse(button);
            }
        },
        initButtons = function (buttons) {
            states = storage.getItem(STORAGE_KEY, {});
            // must be an object, not an array or anything else
            // `typeof` returns "object" also for arrays, so the following
            // check must be done
            // see http://stackoverflow.com/questions/4775722/check-if-object-is-array
            if ('[object Object]' !== Object.prototype.toString.call(states)) {
                states = {};
            }
            for (var i = 0, l = buttons.length; i < l; ++i) {
                var targetId = buttons[i].dataset.toggleTargetId,
                    target = document.getElementById(targetId);
                if (!target) {
                    throw "Toggle target " + targetId + " does not exist";
                }
                // correct the initial state of the button
                if (Sfjs.hasClass(target, 'hidden')) {
                    Sfjs.addClass(buttons[i], 'closed');
                }
                // attach listener for expanding/collapsing the target
                clickHandler(buttons[i], toggle);
                if (states.hasOwnProperty(targetId)) {
                    // open or collapse based on stored data
                    if (0 === states[targetId]) {
                        collapse(buttons[i]);
                    } else {
                        expand(buttons[i]);
                    }
                }
            }
        };
    return {
        initButtons: initButtons,
        toggle: toggle,
        isExpanded: isExpanded,
        isCollapsed: isCollapsed,
        expand: expand,
        collapse: collapse
    };
}
function JsonStorage(storage) {
    var setItem = function (key, data) {
            storage.setItem(key, JSON.stringify(data));
        },
        getItem = function (key, defaultValue) {
            var data = storage.getItem(key);
            if (null !== data) {
                try {
                    return JSON.parse(data);
                } catch(e) {
                }
            }
            return defaultValue;
        };
    return {
        setItem: setItem,
        getItem: getItem
    };
}
function TabView() {
    "use strict";
    var activeTab = null,
        activeTarget = null,
        select = function (tab) {
            var targetId = tab.dataset.tabTargetId,
                target = document.getElementById(targetId);
            if (!target) {
                throw "Tab target " + targetId + " does not exist";
            }
            if (activeTab) {
                Sfjs.removeClass(activeTab, 'active');
            }
            if (activeTarget) {
                Sfjs.addClass(activeTarget, 'hidden');
            }
            Sfjs.addClass(tab, 'active');
            Sfjs.removeClass(target, 'hidden');
            activeTab = tab;
            activeTarget = target;
        },
        initTabs = function (tabs) {
            for (var i = 0, l = tabs.length; i < l; ++i) {
                var targetId = tabs[i].dataset.tabTargetId,
                    target = document.getElementById(targetId);
                if (!target) {
                    throw "Tab target " + targetId + " does not exist";
                }
                clickHandler(tabs[i], select);
                Sfjs.addClass(target, 'hidden');
            }
            if (tabs.length > 0) {
                select(tabs[0]);
            }
        };
    return {
        initTabs: initTabs,
        select: select
    };
}
var tabTarget = new TabView(),
    toggler = new Toggler(new JsonStorage(sessionStorage)),
    clickHandler = function (element, callback) {
        Sfjs.addEventListener(element, 'click', function (e) {
            if (!e) {
                e = window.event;
            }
            callback(this);
            if (e.preventDefault) {
                e.preventDefault();
            } else {
                e.returnValue = false;
            }
            e.stopPropagation();
            return false;
        });
    };
tabTarget.initTabs(document.querySelectorAll('.tree .tree-inner'));
toggler.initButtons(document.querySelectorAll('a.toggle-button'));