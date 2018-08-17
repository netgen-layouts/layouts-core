import NetgenCore from '@netgen/layouts-ui-core';

const $ = NetgenCore.$;

/* modal plugin */
export default class NlModal {
    constructor(opt) {
        this.options = $.extend({
            preload: false,
            cancelDisabled: false,
            autoClose: true,
            body: '<p>Empty modal</p>',
            title: '',
            cancelText: 'Cancel',
            applyText: 'OK',
        }, opt);
        this.className = `nl-modal-mask ${this.options.className}`;
        this.$el = $(`<div class="${this.className}">`);
        this.$container = $('<div class="nl-modal-container">');
        this.$loader = $('<div class="nl-modal-loader"><span></span></div>');

        this.loadModal();
        this.setupEvents();
    }

    loadModal() {
        this.options.preload ? this.loadingStart() : this.$container.html(this.getHtml());
        this.$el.append(this.$loader, this.$container);
        $('body').append(this.$el);
        $(document).on('keydown.closemodal', (e) => {
            e.keyCode === 27 && this.close();
        });
    }

    getHtml() {
        return `<div class="nl-modal">
                    <button class="close-modal"></button>
                    <div class="nl-modal-head">${this.options.title}</div>
                    <div class="nl-modal-body">${this.options.body}</div>
                    <div class="nl-modal-actions">
                        <button type="button" class="nl-btn nl-btn-default action-cancel">${this.options.cancelText}</button>
                        <button type="button" class="nl-btn nl-btn-primary action-apply">${this.options.applyText}</button>
                    </div>
                </div>`;
    }

    setupEvents() {
        this.$el.on('click', '.close-modal', this.close.bind(this));
        this.$el.on('click', '.action-apply', this.apply.bind(this));
        this.$el.on('click', '.action-cancel', this.cancel.bind(this));
    }

    apply(e) {
        e && e.preventDefault();
        this.$el.trigger('apply');
        this.options.autoClose && this.close();
    }

    cancel(e) {
        e && e.preventDefault();
        this.$el.trigger('cancel');
        this.close();
    }

    close(e) {
        e && e.preventDefault();
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
        this.$el.trigger('close');
        this.$el.remove();
    }
}
