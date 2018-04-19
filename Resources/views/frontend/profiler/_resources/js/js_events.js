;(function ($) {

    window.StateDebug = {

        'filter' : JSON.parse(window.localStorage.getItem('state-manager-debug/filter')) || {
            'type': [],
            'event': [],
            'plugin': []
        },

        getIndents: function (type) {
            var minLength = 14,
                indents = "";

            while ((type + indents).length < minLength) {
                indents = indents + " ";
            }

            return indents;
        },

        decorate: function (func, type) {
            var me = this;

            if (me.filter.type.length !== 0 && $.inArray(type, me.filter.type) === -1) {
                type = null;
            }

            return function () {
                var indents = me.getIndents(type);

                switch (type) {
                    case 'subscribe':
                    case 'unsubscribe':
                    case 'publish':
                        var eventName = arguments[0];

                        if (me.filter.event.length !== 0 && !me.filter.event.some(function (val) { return eventName.includes(val) })) {
                            break;
                        }

                        me.logEvents(type, eventName, indents, arguments);

                        break;
                    case 'addPlugin':
                    case 'removePlugin':
                    case 'updatePlugin':
                    case 'destroyPlugin':
                        var pluginName = arguments[1];

                        if (me.filter.plugin.length !== 0 && !me.filter.plugin.some(function (val) { return pluginName.includes(val) })) {
                            break;
                        }

                        me.logPlugins(type, pluginName, indents, arguments);

                        break;
                    case 'switchPlugins':
                        me.logBreakpoints(type, indents, arguments);

                        break;
                    case 'initPlugin':
                        func.apply(
                            this,
                            arguments
                        );

                        var pluginInitName = arguments[2];

                        if (me.filter.plugin.length !== 0 && !me.filter.plugin.some(function (val) { return pluginInitName.includes(val) })) {
                            return;
                        }

                        me.logInits(type, pluginInitName, indents, arguments);

                        return;
                }

                return func.apply(
                    this,
                    arguments
                );
            }
        },

        logEvents: function (type, eventName, indents, args) {
            var argsArray = args[1];

            if (type === 'publish' && args) {
                console.log("(%s)%s %s [arguments: %O]", type, indents, eventName, argsArray);
            }
            else {
                console.log("(%s)%s %s", type, indents, eventName);
            }
        },

        logPlugins: function (type, pluginName, indents, args) {
            var element = args[0],
                viewports = args[2];

            if (type === 'addPlugin' && viewports) {
                console.log("(%s)%s %s [element: %o, viewports: %O]", type, indents, pluginName, element, viewports);
            }
            else {
                console.log("(%s)%s %s [element: %o]", type, indents, pluginName, element);
            }
        },

        logBreakpoints: function (type, indents, args) {
            var previousState = args[0],
                currentState = args[1];

            console.log("(%s)%s %c[previousState: %s, currentState: %s]", type, indents, 'font-weight:bold', previousState, currentState);
        },

        logInits: function (type, pluginName, indents, args) {
            var element = args[0],
                selector = args[1],
                plugin = element.data('plugin_' + pluginName);

            if (plugin) {
                console.log("(%s)%s %s [selector: %o, events: %O]", type, indents, pluginName, selector, plugin._events);
            }
            else {
                console.log("(%s)%s %s [selector: %o]", type, indents, pluginName, selector);
            }
        },

        setFilter: function (key, value) {
            var me = this;

            me.filter[key] = [];

            if ($.isArray(value)) {
                me.filter[key] = value;
            }
            else if (value) {
                me.filter[key].push(value)
            }

            window.localStorage.setItem('state-manager-debug/filter', JSON.stringify(me.filter))
        },

        setFilterType: function (value) {
            var me = this,
                types = [
                    'subscribe',
                    'unsubscribe',
                    'publish',
                    'addPlugin',
                    'removePlugin',
                    'updatePlugin',
                    'destroyPlugin',
                    'switchPlugins',
                    'initPlugin'
                ];

            if (
                (value && !$.isArray(value) && $.inArray(value, types) === -1) ||
                ($.isArray(value) && !value.every(function (val) { return $.inArray(val, types) !== -1; }))
            ) {
                console.error('Allowed types: %s', types.toString());

                return;
            }

            me.setFilter('type', value);
        },

        setFilterEvent: function (value) {
            var me = this;

            me.setFilter('event', value);
        },

        setFilterPlugin: function (value) {
            var me = this;

            me.setFilter('plugin', value);
        }

    };

    $.subscribe = window.StateDebug.decorate($.subscribe, 'subscribe');
    $.unsubscribe = window.StateDebug.decorate($.unsubscribe, 'unsubscribe');
    $.publish = window.StateDebug.decorate($.publish, 'publish');

    window.StateManager.addPlugin = window.StateDebug.decorate(window.StateManager.addPlugin, 'addPlugin');
    window.StateManager.removePlugin = window.StateDebug.decorate(window.StateManager.removePlugin, 'removePlugin');
    window.StateManager.updatePlugin = window.StateDebug.decorate(window.StateManager.updatePlugin, 'updatePlugin');
    window.StateManager.destroyPlugin = window.StateDebug.decorate(window.StateManager.destroyPlugin, 'destroyPlugin');

    window.StateManager._switchPlugins = window.StateDebug.decorate(window.StateManager._switchPlugins, 'switchPlugins');
    window.StateManager._initSinglePlugin = window.StateDebug.decorate(window.StateManager._initSinglePlugin, 'initPlugin');

}(jQuery));