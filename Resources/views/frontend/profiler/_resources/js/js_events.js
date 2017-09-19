var orgPublish = $.publish;
$.publish = function (eventName) {
    for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
    }

    console.log(eventName, args);
    orgPublish.apply(undefined, [eventName].concat(args));
};