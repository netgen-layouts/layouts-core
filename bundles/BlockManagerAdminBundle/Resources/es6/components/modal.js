import NetgenCore from 'netgen-core';

const $ = NetgenCore.$;

/* modal plugin */
export default class NlModal {
    constructor(opt) {
        this.options = $.extend({
            html: '<div class="nl-modal"><button class="close-modal"></button><div class="nl-modal-body"><p>Empty modal</p></div></div>',
        }, opt);
        this.className = `nl-modal-mask ${this.options.className}`;
        this.$el = $(`<div class="${this.className}">`);
        this.$container = $('<div class="nl-modal-container">');
        this.$loader = $('<div class="nl-modal-loader"><span></span></div>');

        this.loadModal();
        this.setupEvents();
    }

    loadModal() {
        this.options.preload && this.$el.addClass('modal-loading');
        this.$container.html(this.options.html);
        this.$el.append(this.$loader, this.$container);
        $('body').append(this.$el);
        $(document).on('keydown.closemodal', e => e.keyCode === 27 && this.closeModal());
    }

    setupEvents() {
        this.$el.on('click', '.close-modal', (e) => {
            e.preventDefault();
            this.closeModal();
        });
    }

    closeModal() {
        this.$el.fadeOut(150, this.destroy.bind(this));
        $(document).off('keydown.closemodal');
    }

    insertModalHtml(html) {
        this.$container.html(html);
        this.loadingStop();
    }

    loadingStart() {
        this.$el.addClass('modal-loading');
    }

    loadingStop() {
        this.$el.removeClass('modal-loading');
    }

    destroy() {
        this.$el.remove();
    }
}
