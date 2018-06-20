import NetgenCore from '@netgen/layouts-core-ui';
import NlModal from './modal';

const $ = NetgenCore.$;

/* layout plugin */
export default class NlLayout {
    constructor(el) {
        this.$el = $(el);
        this.attributes = this.$el.find('.nl-layout-content').data();
        this.id = this.attributes.id;
        this.published = !!this.attributes.published;
        this.csrf = $('meta[name=ngbm-admin-csrf-token]').attr('content');
        this.shared = this.$el.parent().hasClass('nl-shared-layouts');
        this.baseUrl = $('meta[name=ngbm-admin-base-path]').attr('content') + (this.shared ? 'shared_layouts/' : 'layouts/');
        this.$selectExport = this.$el.find(`#exportLayout${this.id}`);
        this.selected = this.$selectExport.prop('checked');
        this.layouts = this.$el.data('layouts');

        this.setupEvents();
        this.onRender();

        this.$el.data('layout', this);
    }

    onRender() {
        this.$el.find('.nl-tt').tooltip();
        this.$el.find('.nl-dropdown').dropdown();
    }

    layoutDelete(e) {
        e.preventDefault();
        const url = `${this.baseUrl}${this.id}/delete${this.published ? '?published=true' : ''}`;
        const modal = new NlModal({
            preload: true,
            autoClose: false,
        });
        $('body').click();
        const formAction = (ev) => {
            ev.preventDefault();
            $.ajax({
                type: 'DELETE',
                url,
                beforeSend: () => modal.loadingStart(),
                success: () => {
                    modal.close();
                    for (let i = 0, len = this.layouts.layouts.length; i < len; i++) {
                        if (this.layouts.layouts[i].id === this.id) {
                            this.layouts.layouts.splice(i, 1);
                            this.layouts.toggleUI();
                            this.$el.remove();
                            return true;
                        }
                    }
                    return true;
                },
                error: (xhr) => {
                    modal.insertModalHtml(xhr.responseText);
                },
            });
        };
        $.ajax({
            type: 'GET',
            url,
            success: (data) => {
                modal.insertModalHtml(data);
                modal.$el.on('apply', formAction);
            },
        });
    }

    layoutCopy(e) {
        e.preventDefault();
        const url = `${this.baseUrl}${this.id}/copy${this.published ? '?published=true' : ''}`;
        const modal = new NlModal({
            preload: true,
            autoClose: false,
        });
        $('body').click();
        const formAction = (ev) => {
            ev.preventDefault();
            const $form = $(ev.currentTarget).find('form');
            $.ajax({
                type: 'POST',
                url,
                data: $form.serialize(),
                beforeSend: () => modal.loadingStart(),
                success: (data) => {
                    modal.close();
                    const $newLayout = $('<div class="nl-panel nl-layout">');
                    this.$el.parent().append($newLayout);
                    $newLayout.html(data);
                    $newLayout.data('layouts', this.layouts);
                    const newLayout = new NlLayout($newLayout);
                    this.layouts.layouts.push(newLayout);
                    $newLayout.data('layout').scrollToMe();
                },
                error: (xhr) => {
                    modal.insertModalHtml(xhr.responseText);
                },
            });
        };
        $.ajax({
            type: 'GET',
            url,
            success: (data) => {
                modal.insertModalHtml(data);
                modal.$el.on('apply', formAction);
            },
        });
    }

    clearLayoutCache(e) {
        e.preventDefault();
        const url = `${this.baseUrl}${this.id}/cache`;
        const modal = new NlModal({
            preload: true,
            autoClose: false,
        });
        $('body').click();
        const formAction = () => {
            $.ajax({
                type: 'POST',
                url,
                beforeSend: () => modal.loadingStart(),
                success: () => {
                    modal.close();
                },
                error: (xhr) => {
                    modal.insertModalHtml(xhr.responseText);
                },
            });
        };
        $.ajax({
            type: 'GET',
            url,
            success: (data) => {
                modal.insertModalHtml(data);
                modal.$el.on('apply', formAction);
            },
        });
    }

    clearBlockCaches(e) {
        e.preventDefault();
        const modal = new NlModal({
            preload: true,
            autoClose: false,
            className: 'nl-modal-cache',
        });
        const url = `${this.baseUrl}${this.id}/cache/blocks`;
        $('body').click();
        const formAction = (ev) => {
            ev.preventDefault();
            const $form = $(ev.currentTarget).find('form');
            $.ajax({
                type: 'POST',
                url,
                data: $form.serialize(),
                beforeSend: () => modal.loadingStart(),
                success: () => modal.close(),
                error: (xhr) => {
                    modal.insertModalHtml(xhr.responseText);
                    NlLayout.indeterminateCheckboxes(modal.$el);
                },
            });
        };
        $.ajax({
            type: 'GET',
            url,
            success: (data) => {
                modal.insertModalHtml(data);
                NlLayout.indeterminateCheckboxes(modal.$el);
                modal.$el.on('apply', formAction);
            },
        });
    }

    clearRelatedLayoutCaches(e) {
        e.preventDefault();
        const modal = new NlModal({
            preload: true,
            autoClose: false,
            className: 'nl-modal-cache',
        });
        const url = `${this.baseUrl}${this.id}/cache/related_layouts`;
        $('body').click();
        const formAction = (ev) => {
            ev.preventDefault();
            const $form = $(ev.currentTarget).find('form');
            $.ajax({
                type: 'POST',
                url,
                data: $form.serialize(),
                beforeSend: () => modal.loadingStart(),
                success: () => modal.close(),
                error: (xhr) => {
                    modal.insertModalHtml(xhr.responseText);
                    NlLayout.indeterminateCheckboxes(modal.$el);
                },
            });
        };
        $.ajax({
            type: 'GET',
            url,
            success: (data) => {
                modal.insertModalHtml(data);
                NlLayout.indeterminateCheckboxes(modal.$el);
                modal.$el.on('apply', formAction);
            },
        });
    }

    setupEvents() {
        $.ajaxPrefilter((options, originalOptions, jqXHR) => jqXHR.setRequestHeader('X-CSRF-Token', this.csrf));

        this.$el.on('click', '.js-layout-delete', this.layoutDelete.bind(this));
        this.$el.on('click', '.js-layout-copy', this.layoutCopy.bind(this));
        this.$el.on('click', '.js-layout-clear-cache', this.clearLayoutCache.bind(this));
        this.$el.on('click', '.js-layout-clear-block-caches', this.clearBlockCaches.bind(this));
        this.$el.on('click', '.js-layout-clear-related-layouts-caches', this.clearRelatedLayoutCaches.bind(this));

        this.$selectExport.on('change', () => {
            this.selected = this.$selectExport.prop('checked');
        });
    }

    toggleSelected(select) {
        this.selected = select;
        this.$selectExport.prop('checked', select);
    }

    scrollToMe() {
        const $scrollEl = this.$el.parents('.layout-column-inner').length ? $('.layout-column-inner') : $('body');
        $scrollEl.animate({ scrollTop: this.$el.position().top - 20 }, 750);
    }

    static indeterminateCheckboxes($form) {
        const $checkboxes = [];
        const $submit = $form.find('button[type="submit"]');
        const changeState = (arr) => {
            let checkedNr = 0;
            arr.forEach(el => el.checked && checkedNr++);
            $('input[type="checkbox"]#toggle-all-cache').prop({
                indeterminate: checkedNr > 0 && checkedNr < arr.length,
                checked: checkedNr === arr.length,
            });
            $submit.prop('disabled', checkedNr === 0);
        };
        $form.find('input[type="checkbox"]').each((i, el) => {
            el.id !== 'toggle-all-cache' && $checkboxes.push(el);
        });
        changeState($checkboxes);
        $form.on('change', 'input[type="checkbox"]', (e) => {
            if (e.currentTarget.id === 'toggle-all-cache') {
                $checkboxes.forEach((el) => {
                    $(el).prop('checked', e.currentTarget.checked);
                });
                $submit.prop('disabled', !e.currentTarget.checked);
            } else {
                changeState($checkboxes);
            }
        });
    }
}
