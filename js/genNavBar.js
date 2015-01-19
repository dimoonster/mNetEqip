(function($) {
    $.fn.genNavBar = function(options) {
        var options = $.extend({
            callback: null,
            active: 0
        }, options);

        return this.each(function() {
            var $this = $(this);
            $this.empty();

            var output = $('<ul class="nav navbar-nav">');

            var tagLI;

            $('<li/>', {'class': options.active==1?'active':''})
                .appendTo(output).append($('<a/>', {
                                    'href': 'index.html',
                                    'html': 'Сеть'
                                }));
            $('<li/>', {'class': options.active==2?'active':''})
                .appendTo(output).append($('<a/>', {
                                    'href': 'projects.html',
                                    'html': 'Проекты'
                                }));
            $('<li/>', {'class': options.active==3?'active':''})
                .appendTo(output).append($('<a/>', {
                                    'href': 'sklad.html',
                                    'html': 'Склад'
                                }));


            $this.append(output);
            $.isFunction(options.callback) && options.callback.call($this);
        });
    }
})(jQuery);
