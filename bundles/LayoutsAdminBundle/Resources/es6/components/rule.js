import { Browser, InputBrowse } from '@netgen/content-browser-ui';
import NlModal from './modal';
import MultiEntry from '../plugins/multientry';
import DateTimePicker from '../plugins/datetimepicker';
import {
    parser,
    indeterminateCheckboxes,
    fetchModal,
    submitModal,
} from '../helpers';

const addedFormInit = (form) => {
    const cb = form.getElementsByClassName('js-input-browse')[0];
    if (cb) {
        form.style.visibility = 'hidden'; // eslint-disable-line no-param-reassign
        const browser = new InputBrowse(cb);
        browser.open();
        cb.addEventListener('browser:change', () => {
            form.querySelector('[type="submit"]').click();
        });
        cb.addEventListener('browser:cancel', () => {
            form.getElementsByClassName('js-cancel-add')[0].click();
        });
    }
    const showMsg = (el) => {
        el.getElementsByClassName('multientry-item').length === 0 && el.classList.add('show-message');
    };
    [...form.getElementsByClassName('multientry')].forEach((el) => {
        showMsg(el);
        el.addEventListener('multientry:remove', () => showMsg(el));
        el.addEventListener('multientry:add', () => el.classList.remove('show-message'));
        return new MultiEntry(el);
    });
    [...form.querySelectorAll('select[multiple]')].forEach((el) => {
        let l = el.childElementCount;
        l > 10 && (l = 10);
        l < 5 && (l = 5);
        el.setAttribute('size', l);
    });
    [...form.getElementsByClassName('datetimepicker')].forEach(el => new DateTimePicker(el));
};

/* nl rule plugin */
export default class NlRule {
    constructor(el, priority, rules) {
        this.el = el;
        this.priority = priority;
        this.rules = rules;
        this.attributes = this.el.getElementsByClassName('nl-rule-content')[0].dataset;
        if (!this.attributes.targetType || this.attributes.targetType === 'null') this.attributes.targetType = 'undefined';
        this.id = this.attributes.id;
        this.draftCreated = false;
        [this.priorityEl] = this.el.getElementsByClassName('rule-priority');
        this.type = 'rule';
        this.enabled = this.attributes.enabled;

        this.selectElement = document.getElementById(`export${this.id}`);
        this.selected = this.selectElement && this.selectElement.checked;
        [this.checkBoxContainer] = this.el.getElementsByClassName('nl-export-checkbox');

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
        [this.priorityEl] = this.el.getElementsByClassName('rule-priority');
        this.renderPriority();
        [...this.el.getElementsByClassName('nl-dropdown')].forEach((el) => {
            !el.getElementsByClassName('nl-dropdown-menu')[0].childElementCount && el.parentElement.removeChild(el);
        });
        this.attributes = this.el.getElementsByClassName('nl-rule-content')[0].dataset;

        this.selectElement = document.getElementById(`export${this.id}`);
        this.selected = this.selectElement && this.selectElement.checked;
        [this.checkBoxContainer] = this.el.getElementsByClassName('nl-export-checkbox');

        if (this.selectElement) {
            this.selectElement.addEventListener('change', () => {
                this.selected = this.selectElement.checked;

                if (this.selected) {
                        this.el.classList.add('selected');
                        this.rules.setSelectingId('00000000-0000-0000-0000-000000000000');
                } else {
                    this.el.classList.remove('selected');
                    this.rules.checkboxLoop();
                }
            });
        }

        this.enabled = this.attributes.enabled;
        this.checkEnabled();
    }

    checkEnabled() {
        if (this.enabled !== '1') {
            this.el.classList.add('disabled');
        } else {
            this.el.classList.remove('disabled');
        }
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
            console.log(error); // eslint-disable-line no-console
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
                    formEl.innerHTML = data;
                    addedFormInit(formEl);
                    throw new Error(`HTTP error, status ${response.status}`);
                });
            }
            return response.text();
        }).then((data) => {
            this.renderEl(data);
        }).catch((error) => {
            console.log(error); // eslint-disable-line no-console
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
        if (action === 'publish') return this.publishRule();
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
            console.log(error); // eslint-disable-line no-console
        });
        return true;
    }

    discardDraft(e) {
        e.preventDefault();
        const url = `${this.rules.baseUrl}rules/${this.id}/discard`;
        if (this.draftCreated) {
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
                this.afterDraftRemove();
            }).catch((error) => {
                console.log(error); // eslint-disable-line no-console
            });
        }
        return true;
    }

    publishRule() {
        const url = `${this.rules.baseUrl}rules/${this.id}/publish`;
        this.rules.showLoader();
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
            this.afterDraftRemove();
            this.rules.hideLoader();
        }).catch((error) => {
            console.log(error); // eslint-disable-line no-console
            this.rules.hideLoader();
        });
    }

    handleCheckboxDisable(id) {
        if (id === null) {
            this.selectElement.disabled = false;
            this.checkBoxContainer.getElementsByTagName('label')[0].style.opacity = '';
            this.el.classList.remove('hide-checkbox');
        } else if (id === '00000000-0000-0000-0000-000000000000') {
            this.checkBoxContainer.getElementsByTagName('label')[0].style.opacity = '1';
            this.el.classList.remove('hide-checkbox');
        }
    }

    linkAnimation() {
        this.el.classList.toggle('blink');
        setTimeout(() => {
            this.el.classList.toggle('blink');
        }, 1500);
    }

    ruleUnlink(e) {
        e.preventDefault();
        const url = `${this.rules.baseUrl}rules/${this.id}/unlink_layout`;
        const modal = new NlModal({
            preload: true,
            autoClose: false,
        });
        const confirmUnlink = () => {
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
                this.publishRule();
                modal.close();
                this.linkAnimation();
            }).catch((error) => {
                console.log(error); // eslint-disable-line no-console
            });
        };

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
                modal.insertModalHtml(data);
                modal.el.addEventListener('apply', confirmUnlink);
            }).catch((error) => {
                console.log(error); // eslint-disable-line no-console
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
                this.rules.deleteRule(this.id);
                return true;
            }).catch((error) => {
                console.log(error); // eslint-disable-line no-console
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
            console.log(error); // eslint-disable-line no-console
        });
    }

    settingDelete(e) {
        e.preventDefault();
        const { dataset } = e.target.closest('.js-setting-delete');
        const url = `${this.rules.baseUrl}rules/${dataset.settingType}s/${dataset.settingId}`;
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
                console.log(error); // eslint-disable-line no-console
            });
        });
    }

    settingEdit(e) {
        e.preventDefault();
        const { dataset } = e.target.closest('.js-setting-edit');
        const url = `${this.rules.baseUrl}rules/${dataset.settingType}s/${dataset.settingId}/edit`;
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
                    conditionEl.style.display = 'flex';
                });
            }).catch((error) => {
                console.log(error); // eslint-disable-line no-console
            });
        });
    }

    settingAdd(e) {
        e.preventDefault();
        const actionsEl = e.target.closest('.settings-action-add');
        const loaderEl = actionsEl.parentElement.getElementsByClassName('settings-loader')[0];
        const { action } = e.target.dataset;
        let url;
        if (action === 'add-target') {
            const targetType = e.target.dataset.targetType || e.target.parentElement.getElementsByClassName('js-target-type')[0].value;
            url = `${this.rules.baseUrl}rules/${this.id}/target/new/${targetType}`;
        } else if (action === 'add-condition') {
            const conditionType = e.target.parentElement.getElementsByClassName('js-condition-type')[0].value;
            url = `${this.rules.baseUrl}rules/${this.id}/condition/new/${conditionType}`;
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
                    formEl.addEventListener('click', (ev) => {
                        if (ev.target.closest('.js-cancel-add')) {
                            ev.preventDefault();
                            formEl.parentElement.removeChild(formEl);
                            actionsEl.style.display = 'block';
                        }
                    });
                }).catch((error) => {
                    console.log(error); // eslint-disable-line no-console
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
            itemType: dataset.browserItemType,
            disabledItems: [parseInt(dataset.linkedLayout, 10)],
            onConfirm: (selected) => {
                const newId = selected[0].value;
                this.createDraft(() => {
                    fetch(`${this.rules.baseUrl}rules/${this.id}/link_layout`, {
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
                        this.publishRule();
                        this.linkAnimation();
                    }).catch((error) => {
                        console.log(error); // eslint-disable-line no-console
                    });
                });
            },
        });
        browser.open();
    }

    clearLayoutCache(e) {
        e.preventDefault();
        const url = `${this.rules.apiUrl}/layouts/${this.attributes.layoutId}/cache`;
        const modal = new NlModal({
            preload: true,
            autoClose: false,
        });
        document.body.click();
        const formAction = (ev) => {
            ev.preventDefault();
            modal.loadingStart();
            submitModal(url, modal, 'POST', this.rules.csrf, null);
        };
        fetchModal(url, modal, formAction);
    }

    clearBlockCaches(e) {
        e.preventDefault();
        const modal = new NlModal({
            preload: true,
            autoClose: false,
            className: 'nl-modal-cache',
        });
        const url = `${this.rules.apiUrl}/layouts/${this.attributes.layoutId}/cache/blocks`;
        document.body.click();
        const formAction = (ev) => {
            ev.preventDefault();
            modal.loadingStart();
            const formEl = modal.el.getElementsByTagName('FORM')[0];
            submitModal(url, modal, 'POST', this.rules.csrf, new URLSearchParams(new FormData(formEl)), null, () => indeterminateCheckboxes(modal.el));
        };
        fetchModal(url, modal, formAction, () => indeterminateCheckboxes(modal.el));
    }

    editRule(e) {
        e.preventDefault();
        const shouldPublish = !this.draftCreated;
        const url = `${this.rules.baseUrl}rules/${this.id}/edit`;
        const modal = new NlModal({
            preload: true,
            autoClose: false,
        });
        this.createDraft(() => {
          document.body.click();
          const formAction = (ev) => {
              ev.preventDefault();
              modal.loadingStart();
              const formEl = modal.el.getElementsByTagName('FORM')[0];
              const afterSuccess = (html) => {
                shouldPublish ? this.publishRule() : this.renderEl(html);
              };
              submitModal(url, modal, 'POST', this.rules.csrf, new URLSearchParams(new FormData(formEl)), afterSuccess);
          };
          fetchModal(url, modal, formAction);
        });
    }

    copyRule(e) {
        e.preventDefault();
        const url = `${this.rules.baseUrl}rules/${this.id}/copy`;
        const modal = new NlModal({
            preload: true,
            autoClose: false,
        });
        document.body.click();
        const formAction = (ev) => {
            ev.preventDefault();
            modal.loadingStart();
            const formEl = modal.el.getElementsByTagName('FORM')[0];

            const afterSuccess = (html) => {
                document.body.click();
                this.rules.createRule(html, this.priority + 1);
                this.rules.hideLoader();
            };
            submitModal(url, modal, 'POST', this.rules.csrf, new URLSearchParams(new FormData(formEl)), afterSuccess);
        };
        fetchModal(url, modal, formAction);
    }

    onSortingStart() {
      this.selectEl = document.createElement('select');
      this.selectEl.className = 'nl-select';
      for (let i = 0; i <= this.rules.rules.ids.length - 1; i++) {
        const option = document.createElement('option');
        option.text = i + 1;
        option.value = i;
        this.selectEl.add(option);
      }
      this.selectEl.value = this.priority;
      this.priorityEl.innerHTML = '';
      this.priorityEl.appendChild(this.selectEl);
      this.selectEl.addEventListener('change', e => this.rules.moveRule(this.priority, parseInt(e.currentTarget.value, 10), true));
    }

    onSortingCancel(priority) {
      if (priority !== undefined) this.priority = priority;
      this.onSortingEnd();
    }

    onSortingChange(newPriority) {
      this.priority = newPriority;
      this.selectEl.value = newPriority;
    }

    onSortingEnd() {
      this.renderPriority();
    }

    renderPriority() {
      this.priorityEl.innerHTML = `<span class="rule-priority-nr">${this.priority + 1}</span>`;
    }

    setupEvents() {
        this.el.addEventListener('click', (e) => {
            e.stopPropagation();
            this.rules.focusCheck() && this.el.classList.add('focused');
            document.addEventListener('click', (e2) => {
                if (!this.el.contains(e2.target)) {
                    this.rules.focusCheck() && this.el.classList.remove('focused');
                }
            }, { once: true });
        });

        this.el.addEventListener('click', (e) => {
            if (e.target.closest('.js-rule-edit')) {
                this.ruleEdit(e);
            } else if (e.target.closest('.js-rule-unlink')) {
                this.ruleUnlink(e);
            } else if (e.target.closest('.js-rule-delete')) {
                this.ruleDelete(e);
            } else if (e.target.closest('.js-rule-edit-rule')) {
                this.editRule(e);
            } else if (e.target.closest('.js-rule-copy-rule')) {
                e.stopPropagation();
                this.copyRule(e);
            } else if (e.target.closest('.js-setting-delete')) {
                e.stopPropagation();
                this.settingDelete(e);
            } else if (e.target.closest('.js-setting-edit')) {
                e.stopPropagation();
                this.settingEdit(e);
            } else if (e.target.closest('.js-setting-add')) {
                this.settingAdd(e);
            } else if (e.target.closest('.js-link-layout')) {
                this.linkLayout(e);
            } else if (e.target.closest('.js-layout-clear-cache')) {
                this.clearLayoutCache(e);
            } else if (e.target.closest('.js-layout-clear-block-caches')) {
                this.clearBlockCaches(e);
            } else if (e.target.closest('.js-toggle-body')) {
                e.stopPropagation();
                this.el.classList.toggle('show-body');
            } else if (e.target.className === 'nl-rule-body-overlay') {
                e.stopPropagation();
                this.el.classList.toggle('show-body');
            }
        });

        window.addEventListener('keyup', (e) => {
            if (e.key === 'Escape' && this.el.classList.contains('show-body')) {
                this.el.classList.toggle('show-body');
            }
        });

        if (this.selectElement) {
            this.selectElement.addEventListener('change', () => {
                this.selected = this.selectElement.checked;

                if (this.selected) {
                        this.el.classList.add('selected');
                        this.rules.setSelectingId('00000000-0000-0000-0000-000000000000');
                } else {
                    this.el.classList.remove('selected');
                }
                this.rules.checkboxLoop();
            });
        }

        if (this.selectElement) {
            this.selectElement.addEventListener('change', () => {
                this.selected = this.selectElement.checked;
            });
        }
    }

    toggleSelected(select) {
        this.selected = select;
        this.selectElement.checked = select;
    }

    canExport() { // eslint-disable-line class-methods-use-this
        return true;
    }
}
