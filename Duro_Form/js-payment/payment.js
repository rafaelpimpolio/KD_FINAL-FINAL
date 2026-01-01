(function(factory){
    if(typeof define === 'function' && define.amd){
        define(['jquery'], factory);
    }else if(typeof module === 'object' && module.exports){
        module.exports = function(root, jQuery){
            if(jQuery === undefined){
                if(typeof window !== 'undefined'){
                    jQuery = require('jquery');
                } else{
                    jQuery = require('jquery')(root);
                }
            }
            factory(jQuery);
            return jQuery;
        };
    }else{
        factory(jQuery);
    }
}(function($){
    "use strict";

    var w = window;

    $.fn.paymentConfirm = function(options, option2){
        if(typeof options === 'undefined') options = {};
        if(typeof options === 'string'){
            options = {
                content: options,
                title: (option2) ? option2 : false
            };
        }

        $(this).each(function(){
            var $this = $(this);
            if($this.attr('pc-attached')) return;

            $this.on('click', function(e){
                e.preventDefault();
                var pcOption = $.extend({}, options);
                if($this.attr('data-title'))
                    pcOption['title'] = $this.attr('data-title');
                if($this.attr('data-content'))
                    pcOption['content'] = $this.attr('data-content');

                pcOption['$target'] = $this;

                if($this.attr('href') && !pcOption['buttons']){
                    pcOption['buttons'] = {
                        "Pay Now": {
                            action: function(){ location.href = $this.attr('href'); },
                            btnClass: 'btn-success'
                        },
                        "Cancel": {
                            action: function(){},
                            btnClass: 'btn-danger'
                        }
                    };
                }

                pcOption['closeIcon'] = false;
                w.paymentConfirm(pcOption);
            });

            $this.attr('pc-attached', true);
        });
        return $(this);
    };

    w.paymentConfirm = function(options){
        if(!options) options = {};
        var pluginOptions = $.extend(true, {}, w.paymentConfirm.pluginDefaults, options);
        var instance = new w.PaymentConfirm(pluginOptions);
        w.paymentConfirm.instances.push(instance);
        return instance;
    };

    w.PaymentConfirm = function(options){
        $.extend(this, options);
        this._init();
    };

    w.PaymentConfirm.prototype = {
        _init: function(){
            var that = this;
            this._id = Math.floor(Math.random()*99999);
            this.contentParsed = $('<div>');
            if(!this.lazyOpen){
                setTimeout(function(){ that.open(); }, 0);
            }
        },
        _buildHTML: function(){
            var that = this;
            var template = $(this.template);

            template.find('.pc-box-container').addClass(this.animationParsed);
            this.$el = template.appendTo(this.container);
            this.$boxContainer = this.$el.find('.pc-box-container');
            this.$box = this.$el.find('.pc-box');
            this.$content = this.$el.find('.pc-content');
            this.$btnc = this.$el.find('.pc-buttons');
            this.$closeIcon = this.$el.find('.pc-closeIcon');

            this.setTitle();
            this._setButtons();
            this.setContent(this.content);
        },
        _setButtons: function(){
            var that = this;
            if(typeof this.buttons !== 'object') this.buttons = {};

            $.each(this.buttons, function(key, button){
                if(typeof button === 'function') button = { action: button };
                var btn = $('<button class="btn"></button>')
                    .addClass(button.btnClass || 'btn-default')
                    .text(button.text || key)
                    .click(function(e){
                        e.preventDefault();
                        var res = button.action.apply(that);
                        if(res !== false) that.close();
                    });
                that.$btnc.append(btn);
            });
        },
        setTitle: function(title){
            this.$el.find('.pc-title').text(title || this.title);
        },
        setContent: function(content){
            this.$content.html(content);
        },
        close: function(){
            this.$el.remove();
            var i = w.paymentConfirm.instances.findIndex(x => x._id === this._id);
            if(i !== -1) w.paymentConfirm.instances.splice(i,1);
        },
        open: function(){
            if(this.$el) return false;
            this._buildHTML();
            return true;
        }
    };

    w.paymentConfirm.instances = [];
    w.paymentConfirm.pluginDefaults = {
        template: '' +
            '<div class="payment-confirm">' +
            '<div class="pc-bg"></div>' +
            '<div class="pc-holder">' +
            '<div class="pc-box-container">' +
            '<div class="pc-box">' +
            '<div class="pc-closeIcon">&times;</div>' +
            '<div class="pc-title"></div>' +
            '<div class="pc-content"></div>' +
            '<div class="pc-buttons"></div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>',
        title: 'Payment Confirmation',
        content: 'Do you want to proceed with the payment?',
        container: 'body',
        buttons: {
            "Pay Now": { btnClass: 'btn-success' },
            "Cancel": { btnClass: 'btn-danger' }
        },
        animationParsed: 'pc-scale'
    };
}));
