/*
* jQuery ScrollPaging
* 26.03.2013 (c) http://artod.ru
*/
;(function ($) {
    'use strict';

    var ScrollPaging = function (options) {
        this.opts = $.extend({
            $viewport: $(window),
            $scrollable: $(document),
            additive: 100,
            getPage: function (scrollPaging) {
            }
        }, options);

        this.gotten = [];
        this.page = 1;

        var scrollTimeout,
            self = this;

        this.opts.$viewport.off('.scrollPaging').on('scroll.scrollPaging', function () {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
                scrollTimeout = null;
            }

            if (self.gotten[self.page]) {
                return false;
            }

            scrollTimeout = setTimeout(function () {
                self.check();
            }, 250);
        });
    };

    ScrollPaging.prototype = {
        check: function () {
            if (this.opts.$scrollable.get(0) !== document && !this.opts.$scrollable.parent().length) { // check whether a element is in the DOM
                this.stop();
                return;
            }

            var contOffset = this.opts.$viewport.offset(), // window
                scrollOffset = this.opts.$scrollable.offset(), // document
                contTop = contOffset ? contOffset.top : this.opts.$viewport.scrollTop(),
                scrollTop = scrollOffset ? scrollOffset.top : 0;

            if (scrollTop + this.opts.$scrollable.height() - contTop - this.opts.$viewport.height() < this.opts.additive) {
                this.gotten[this.page] = true;
                this.opts.getPage(this);
            }
        },
        stop: function () {
            this.opts.$viewport.off('.scrollPaging');
        }
    };

    $.scrollPaging = function (options) {
        return new ScrollPaging(options);
    };
})(jQuery);