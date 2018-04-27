import Browser from 'netgen-content-browser';
import NetgenCore from 'netgen-core';

const $ = NetgenCore.$;

/* nl rule plugin */
export default class NlRule {
    constructor(el) {
        this.$el = $(el);
        this.attributes = this.$el.find('.nl-rule-content').data();
        if (!this.attributes.targetType || this.attributes.targetType === 'null') this.attributes.targetType = 'undefined';
        this.id = this.attributes.id;
        this.baseUrl = `${$('meta[name=ngbm-admin-base-path]').attr('content')}layout_resolver/`;

        this.$el.data('rule', this);
        this.setupEvents();
        this.onRender();
    }

    renderEl(html) {
        this.$el.hide().html(html).fadeIn();
        this.onRender();
    }

    onRender() {
        if (this.draftCreated === 1) {
            this.afterDraftCreate();
            this.draftCreated++;
        }
        this.$el.attr('data-id', this.$el.find('.nl-rule-content').data('id'));
        this.$el.find('.nl-tt').tooltip();
        this.$el.find('.nl-dropdown').dropdown();
    }

    createDraft(callback) {
        $.ajax({
            type: 'POST',
            url: `${this.baseUrl}rules/${this.id}/draft`,
            beforeSend: () => {
                if (this.checkIfDraft()) {
                    callback();
                    return false;
                }
                return true;
            },
            success: () => {
                this.draftCreated = 1;
                callback();
            },
        });
    }

    static addedFormInit(form) {
        const $cb = form.find('.js-input-browse');
        if ($cb.length) {
            form.css('visibility', 'hidden');
            $cb.input_browse();
            $cb.data('input_browse').$change();
            $cb.on('browser:change', () => {
                form.submit();
            }).on('browser:cancel', () => {
                form.find('.js-cancel-add').click();
            });
        }
        if (form.find('.multientry').length) {
            const showMsg = (el) => {
                el.find('.multientry-item').length === 0 && el.addClass('show-message');
            };
            $('.multientry').multientry();
            showMsg($('.multientry'));
            $('.multientry').on('multientry:remove', e => showMsg($(e.currentTarget)));
            $('.multientry').on('multientry:add', e => $(e.currentTarget).removeClass('show-message'));
        }
        form.find('select[multiple]').each((i, el) => {
            let l = $(el).find('option').length;
            l > 10 && (l = 10);
            $(el).attr('size', l);
        });
        form.find('.datetimepicker').each((i, el) => {
            $(el).closest('form').addClass('ngc');
            return new NetgenCore.DateTimePicker({
                el: $(el),
                options: {
                    widgetPositioning: {
                        vertical: 'bottom',
                    },
                },
            });
        });
    }

    addedFormAction(e) {
        e.preventDefault();
        const $form = $(e.currentTarget);
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            success: data => this.renderEl(data),
            error: (xhr) => {
                $form.html(xhr.responseText);
                NlRule.addedFormInit($form);
            },
        });
    }


    afterDraftCreate() {
        this.$el.data('draft', 'true');
        this.$el.addClass('show-actions');
    }
    afterDraftRemove() {
        this.$el.data('draft', 'false');
        this.$el.removeClass('show-actions');
        this.draftCreated = 0;
    }

    checkIfDraft() {
        return this.$el.data('draft') === 'true';
    }

    ruleEdit(e) {
        e.preventDefault();
        const action = $(e.currentTarget).data('action');
        const url = `rules/${this.id}/${action}`;
        const getDraft = !!((action === 'disable' || action === 'enable') && this.checkIfDraft());
        $.ajax({
            type: 'POST',
            url: this.baseUrl + url,
            success: (data) => {
                this.renderEl(data);
                getDraft || this.afterDraftRemove();
            },
        });
    }
    ruleUnlink(e) {
        e.preventDefault();
        const url = `rules/${this.id}`;
        this.createDraft(() => {
            $.ajax({
                type: 'POST',
                data: {
                    layout_id: 0,
                },
                url: this.baseUrl + url,
                success: (data) => {
                    this.renderEl(data);
                },
            });
        });
    }
    ruleDelete(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this mapping?')) {
            const url = `rules/${this.id}`;
            $.ajax({
                type: 'DELETE',
                url: this.baseUrl + url,
                success: () => {
                    this.$el.remove();
                    $(document).trigger('delete-rule', { nlRule: this });
                },
            });
        }
    }
    settingDelete(e) {
        e.preventDefault();
        const url = `${$(e.currentTarget).data('setting-type')}s/${$(e.currentTarget).data('setting-id')}`;
        this.createDraft(() => {
            $.ajax({
                type: 'DELETE',
                url: this.baseUrl + url,
                success: (data) => {
                    this.renderEl(data);
                },
            });
        });
    }
    settingEdit(e) {
        e.preventDefault();
        const url = `${$(e.currentTarget).data('setting-type')}s/${$(e.currentTarget).data('setting-id')}/edit`;
        const $condition = $(e.currentTarget).closest('li');
        this.createDraft(() => {
            $.ajax({
                type: 'GET',
                url: this.baseUrl + url,
                success: (data) => {
                    const $form = $(data);
                    $condition.hide();
                    $condition.before($form);
                    NlRule.addedFormInit($form);
                    $form.on('submit', this.addedFormAction.bind(this));
                    this.$el.on('click', '.js-cancel-add', (ev) => {
                        ev.preventDefault();
                        $form.remove();
                        $condition.show();
                    });
                },
            });
        });
    }
    settingAdd(e) {
        e.preventDefault();
        const $actions = $(e.currentTarget).closest('.settings-action-add');
        const $loader = $actions.siblings('.settings-loader');
        const action = $(e.currentTarget).data('action');
        let url;
        let targetType;
        if (action === 'add-target') {
            targetType = $(e.currentTarget).data('target-type') || $(e.currentTarget).siblings('.js-target-type').val();
            url = `rules/${this.id}/target/new/${targetType}`;
        } else if (action === 'add-condition') {
            targetType = $(e.currentTarget).siblings('.js-condition-type').val();
            url = `rules/${this.id}/condition/new/${targetType}`;
        }
        this.createDraft(() => {
            $.ajax({
                type: 'GET',
                url: this.baseUrl + url,
                beforeSend: () => {
                    $actions.hide();
                    $loader.show();
                },
                success: (data) => {
                    const $form = $(data);
                    $loader.hide();
                    $actions.before($form);
                    NlRule.addedFormInit($form);
                    $form.on('submit', this.addedFormAction.bind(this));
                    this.$el.on('click', '.js-cancel-add', (ev) => {
                        ev.preventDefault();
                        $form.remove();
                        $actions.show();
                    });
                },
            });
        });
    }

    linkLayout(e) {
        e.stopPropagation();
        const dataset = e.currentTarget.dataset;
        const browser = new Browser({
            disabled_item_ids: [parseInt(dataset.linkedLayout, 10)],
            tree_config: {
                overrides: {
                    max_selected: 1,
                    min_selected: 1,
                },
                root_path: dataset.browserConfigName,
            },
        });
        browser.on('apply', () => {
            const newId = browser.selected_collection.first().id;
            this.createDraft(() => {
                $.ajax({
                    type: 'POST',
                    data: {
                        layout_id: newId,
                    },
                    url: `${this.baseUrl}rules/${this.id}`,
                    success: (data) => {
                        this.renderEl(data);
                    },
                });
            });
        }).load_and_open();
    }

    setupEvents() {
        this.$el.on('click', '.nl-rule-head .nl-rule-cell', () => {
            this.$el.toggleClass('show-body');
        });

        this.$el.on('click', '.js-rule-edit', this.ruleEdit.bind(this));
        this.$el.on('click', '.js-rule-unlink', this.ruleUnlink.bind(this));
        this.$el.on('click', '.js-setting-delete', this.settingDelete.bind(this));
        this.$el.on('click', '.js-setting-edit', this.settingEdit.bind(this));
        this.$el.on('click', '.js-setting-add', this.settingAdd.bind(this));
        this.$el.on('click', '.js-rule-delete', this.ruleDelete.bind(this));

        this.$el.on('click', '.js-link-layout', this.linkLayout.bind(this));
    }
}
