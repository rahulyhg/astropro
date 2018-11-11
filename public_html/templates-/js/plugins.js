// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());


// Tabs

(function() {

    $('.tabs-nav > li > a').on('click touchstart', function(e){
        e.preventDefault();

        var tab = $($(this).attr("data-target"));
        var box = $(this).closest('.tabs');

        $(this).closest('.tabs-nav').find('li').removeClass('active');
        $(this).closest('li').addClass('active');

        box.find('> .tabs-item').removeClass('active');
        box.find(tab).addClass('active');
    });

}());