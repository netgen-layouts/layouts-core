import { Browser, InputBrowse } from '@netgen/content-browser-ui';
import NetgenCore from '@netgen/layouts-ui-core';
import NlModal from './modal';
import parser from '../helpers/parser';

const { $ } = NetgenCore;

const addedFormInit = (form) => {
    const cb = form.getElementsByClassName('js-input-browse')[0];
    if (cb) {
        form.style.visibility = 'hidden';
        const browser = new InputBrowse(cb);
        browser.open();
        cb.addEventListener('browser:change', () => {
            form.querySelector('[type="submit"]').click();
        });
        cb.addEventListener('browser:cancel', () => {
            form.getElementsByClassName('js-cancel-add')[0].click();
        });
    }
    if (form.getElementsByClassName('multientry').length) {
        const showMsg = (el) => {
            el.getElementsByClassName('multientry-item').length === 0 && el.classList.add('show-message');
        };
        $('.multientry').multientry();
        showMsg(form.getElementsByClassName('multientry')[0]);
        $('.multientry').on('multientry:remove', e => showMsg(e.currentTarget));
        $('.multientry').on('multientry:add', e => e.currentTarget.classList.remove('show-message'));
    }
    [...form.querySelectorAll('select[multiple]')].forEach((el) => {
        let l = el.childElementCount;
        l > 10 && (l = 10);
        el.setAttribute('size', l);
    });
    [...form.getElementsByClassName('datetimepicker')].forEach((el) => {
        el.closest('form').classList.add('ngc');
        return new NetgenCore.DateTimePicker({
            el: $(el),
            options: {
                widgetPositioning: {
                    vertical: 'bottom',
                },
            },
        });
    });
};

/* nl rule plugin */
export default class NlRule {
    constructor(el, rules) {
        this.el = el;
        this.rules = rules;
        this.attributes = this.el.getElementsByClassName('nl-rule-content')[0].dataset;
        if (!this.attributes.targetType || this.attributes.targetType === 'null') this.attributes.targetType = 'undefined';
        this.id = this.attributes.id;
        this.draftCreated = false;

        this.el.dataset.id = this.id;
        this.setupEvents();
        this.onRender();
    }

    renderEl(html) {
        this.el.innerHTML = html;
        this.onRender();
    }

    onRender() {
        if (this.draftCreated) this.afterDraftCreate();
        [...this.el.getElementsByClassName('nl-dropdown')].forEach((el) => {
            !el.getElementsByClassName('nl-dropdown-menu')[0].childElementCount && el.parentElement.removeChild(el);
        });
    }

    createDraft(callback) {
        if (this.draftCreated) {
            callback();
            return;
        }
        fetch(`${this.rules.baseUrl}rules/${this.id}/draft`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-Token': this.rules.csrf,
            },
        }).then((response) => {
            if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
            return response.text();
        }).then(() => {
            this.draftCreated = true;
            callback();
        }).catch((error) => {
            console.log(error);
        });
    }

    addedFormAction(e) {
        e.preventDefault();
        const formEl = e.currentTarget;
        fetch(window.location.origin + formEl.getAttribute('action'), {
            method: formEl.method,
            credentials: 'same-origin',
            headers: {
                'X-CSRF-Token': this.rules.csrf,
            },
            body: new URLSearchParams(new FormData(formEl)),
        }).then((response) => {
            if (!response.ok) {
                return response.text().then((data) => {
                    formEl.innerHTML(data);
                    addedFormInit(formEl);
                    throw new Error(`HTTP error, status ${response.status}`);
                });
            }
            return response.text();
        }).then((data) => {
            this.renderEl(data);
        }).catch((error) => {
            console.log(error);
        });
    }


    afterDraftCreate() {
        this.el.classList.add('show-actions');
    }

    afterDraftRemove() {
        this.el.classList.remove('show-actions');
        this.draftCreated = false;
    }

    ruleEdit(e) {
        e.preventDefault();
        const { action } = e.target.closest('.js-rule-edit').dataset;
        const url = `${this.rules.baseUrl}rules/${this.id}/${action}`;
        const getDraft = !!((action === 'disable' || action === 'enable') && this.draftCreated);
        fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-Token': this.rules.csrf,
            },
        }).then((response) => {
            if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
            return response.text();
        }).then((data) => {
            this.renderEl(data);
            getDraft || this.afterDraftRemove();
        }).catch((error) => {
            console.log(error);
        });
    }

    ruleUnlink(e) {
        e.preventDefault();
        const url = `${this.rules.baseUrl}rules/${this.id}`;
        this.createDraft(() => {
            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-Token': this.rules.csrf,
                },
                body: new URLSearchParams('layout_id=0'),
            }).then((response) => {
                if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
                return response.text();
            }).then((data) => {
                this.renderEl(data);
            }).catch((error) => {
                console.log(error);
            });
        });
    }

    ruleDelete(e) {
        e.preventDefault();
        const url = `${this.rules.baseUrl}rules/${this.id}/delete`;
        const modal = new NlModal({
            preload: true,
            autoClose: false,
        });
        const formAction = (ev) => {
            ev.preventDefault();
            modal.loadingStart();
            fetch(url, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-Token': this.rules.csrf,
                },
            }).then((response) => {
                if (!response.ok) {
                    return response.text().then((data) => {
                        modal.insertModalHtml(data);
                        throw new Error(`HTTP error, status ${response.status}`);
                    });
                }
                return response.text();
            }).then(() => {
                modal.close();
                this.el.parentElement.removeChild(this.el);
                for (let i = 0, len = this.rules.rules.length; i < len; i++) {
                    if (this.rules.rules[i].id === this.id) {
                        this.rules.rules.splice(i, 1);
                        this.rules.toggleUI();
                        return true;
                    }
                }
                return true;
            }).catch((error) => {
                console.log(error);
            });
        };
        fetch(url, {
            method: 'GET',
        }).then((response) => {
            if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
            return response.text();
        }).then((data) => {
            modal.insertModalHtml(data);
            modal.el.addEventListener('apply', formAction);
        }).catch((error) => {
            console.log(error);
        });
    }

    settingDelete(e) {
        e.preventDefault();
        const { dataset } = e.target.closest('.js-setting-delete');
        const url = `${this.rules.baseUrl}${dataset.settingType}s/${dataset.settingId}`;
        this.createDraft(() => {
            fetch(url, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-Token': this.rules.csrf,
                },
            }).then((response) => {
                if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
                return response.text();
            }).then((data) => {
                this.renderEl(data);
            }).catch((error) => {
                console.log(error);
            });
        });
    }

    settingEdit(e) {
        e.preventDefault();
        const { dataset } = e.target.closest('.js-setting-edit');
        const url = `${this.rules.baseUrl}${dataset.settingType}s/${dataset.settingId}/edit`;
        const conditionEl = e.target.closest('li');
        this.createDraft(() => {
            fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-Token': this.rules.csrf,
                },
            }).then((response) => {
                if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
                return response.text();
            }).then((data) => {
                const formEl = parser(data)[0];
                conditionEl.style.display = 'none';
                conditionEl.parentElement.insertBefore(formEl, conditionEl);
                addedFormInit(formEl);
                formEl.addEventListener('submit', this.addedFormAction.bind(this));
                formEl.getElementsByClassName('js-cancel-add')[0].addEventListener('click', (ev) => {
                    ev.preventDefault();
                    formEl.parentElement.removeChild(formEl);
                    conditionEl.style.display = 'block';
                });
            }).catch((error) => {
                console.log(error);
            });
        });
    }

    settingAdd(e) {
        e.preventDefault();
        const actionsEl = e.target.closest('.settings-action-add');
        const loaderEl = actionsEl.parentElement.getElementsByClassName('settings-loader')[0];
        const { action } = e.target.dataset;
        let url;
        let targetType;
        if (action === 'add-target') {
            targetType = e.target.dataset.targetType || e.target.parentElement.getElementsByClassName('js-target-type')[0].value;
            url = `${this.rules.baseUrl}rules/${this.id}/target/new/${targetType}`;
        } else if (action === 'add-condition') {
            targetType = e.target.parentElement.getElementsByClassName('js-condition-type')[0].value;
            url = `${this.rules.baseUrl}rules/${this.id}/condition/new/${targetType}`;
        }
        this.createDraft(() => {
            this.createDraft(() => {
                actionsEl.style.display = 'none';
                loaderEl.style.display = 'block';
                fetch(url, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-Token': this.rules.csrf,
                    },
                }).then((response) => {
                    if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
                    return response.text();
                }).then((data) => {
                    const formEl = parser(data)[0];
                    loaderEl.style.display = 'none';
                    actionsEl.parentElement.insertBefore(formEl, actionsEl);
                    addedFormInit(formEl);
                    formEl.addEventListener('submit', this.addedFormAction.bind(this));
                    formEl.getElementsByClassName('js-cancel-add')[0].addEventListener('click', (ev) => {
                        ev.preventDefault();
                        formEl.parentElement.removeChild(formEl);
                        actionsEl.style.display = 'block';
                    });
                }).catch((error) => {
                    console.log(error);
                });
            });
        });
    }

    linkLayout(e) {
        e.stopPropagation();
        const { dataset } = e.target.closest('.js-link-layout');
        const browser = new Browser({
            overrides: {
                min_selected: 1,
                max_selected: 1,
            },
            rootPath: dataset.browserConfigName,
            disabledItems: [parseInt(dataset.linkedLayout, 10)],
            onConfirm: (selected) => {
                const newId = selected[0].value;
                this.createDraft(() => {
                    fetch(`${this.rules.baseUrl}rules/${this.id}`, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-Token': this.rules.csrf,
                        },
                        body: new URLSearchParams(`layout_id=${newId}`),
                    }).then((response) => {
                        if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
                        return response.text();
                    }).then((data) => {
                        this.renderEl(data);
                    }).catch((error) => {
                        console.log(error);
                    });
                });
            },
        });
        browser.open();
    }

    setupEvents() {
        this.el.addEventListener('click', (e) => {
            if (e.target.closest('.js-rule-edit')) {
                this.ruleEdit(e);
            } else if (e.target.closest('.js-rule-unlink')) {
                this.ruleUnlink(e);
            } else if (e.target.closest('.js-rule-delete')) {
                this.ruleDelete(e);
            } else if (e.target.closest('.js-setting-delete')) {
                this.settingDelete(e);
            } else if (e.target.closest('.js-setting-edit')) {
                this.settingEdit(e);
            } else if (e.target.closest('.js-setting-add')) {
                this.settingAdd(e);
            } else if (e.target.closest('.js-link-layout')) {
                this.linkLayout(e);
            } else if (e.target.closest('.nl-rule-head .nl-rule-cell')) {
                this.el.classList.toggle('show-body');
            }
        });
    }
}
