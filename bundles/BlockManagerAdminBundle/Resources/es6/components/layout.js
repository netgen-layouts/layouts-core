import NlModal from './modal';
import Tooltip from '../helpers/tooltip';

const indeterminateCheckboxes = (form) => {
    const checkboxes = [];
    const submit = form.querySelector('button[type="submit"]');
    const changeState = (arr) => {
        let checkedNr = 0;
        arr.forEach(el => el.checked && checkedNr++);
        const toggleAllEl = document.querySelector('input[type="checkbox"]#toggle-all-cache');
        if (toggleAllEl) {
            toggleAllEl.indeterminate = checkedNr > 0 && checkedNr < arr.length;
            toggleAllEl.checked = checkedNr === arr.length;
        }
        if (submit) submit.disabled = checkedNr === 0;
    };
    const allCheckboxes = [...form.querySelectorAll('input[type="checkbox"]')];
    allCheckboxes.forEach((el) => {
        el.id !== 'toggle-all-cache' && checkboxes.push(el);
        el.addEventListener('change', (e) => {
            if (e.currentTarget.id === 'toggle-all-cache') {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = e.currentTarget.checked;
                });
                if (submit) submit.disabled = !e.currentTarget.checked;
            } else {
                changeState(checkboxes);
            }
        });
    });
    changeState(checkboxes);
};

const fetchModal = (url, modal, formAction, afterSuccess) => {
    fetch(url, {
        method: 'GET',
    }).then((response) => {
        if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
        return response.text();
    }).then((data) => {
        modal.insertModalHtml(data);
        modal.el.addEventListener('apply', formAction);
        if (afterSuccess) afterSuccess();
    }).catch((error) => {
        console.log(error);
    });
};

const submitModal = (url, modal, method, csrf, body, afterSuccess, afterError) => {
    fetch(url, {
        method,
        credentials: 'include',
        headers: {
            'X-CSRF-Token': csrf,
        },
        body,
    }).then((response) => {
        if (!response.ok) {
            return response.text().then((data) => {
                modal.insertModalHtml(data);
                if (afterError) afterError();
                throw new Error(`HTTP error, status ${response.status}`);
            });
        }
        return response.text();
    }).then((data) => {
        modal.close();
        if (afterSuccess) afterSuccess(data);
        return true;
    }).catch((error) => {
        console.log(error);
    });
};

/* layout plugin */
export default class NlLayout {
    constructor(el, layouts) {
        this.el = el;
        this.layouts = layouts;
        this.attributes = el.getElementsByClassName('nl-layout-content')[0].dataset;
        this.id = this.attributes.id;
        this.published = !!this.attributes.published;
        this.shared = this.el.parentElement.classList.contains('nl-shared-layouts');
        this.selectExport = document.getElementById(`exportLayout${this.id}`);
        this.selected = this.selectExport.checked;

        this.layouts.layouts.push(this);

        this.setupEvents();
        this.onRender();
    }

    onRender() {
        [...this.el.getElementsByClassName('nl-tt')].forEach(el => new Tooltip(el));
        [...this.el.getElementsByClassName('nl-dropdown')].forEach((el) => {
            !el.getElementsByClassName('nl-dropdown-menu')[0].childElementCount && el.parentElement.removeChild(el);
        });
    }

    layoutDelete(e) {
        e.preventDefault();
        const url = `${this.layouts.baseUrl}${this.id}/delete${this.published ? '?published=true' : ''}`;
        const modal = new NlModal({
            preload: true,
            autoClose: false,
        });
        document.body.click();
        const formAction = (ev) => {
            ev.preventDefault();
            modal.loadingStart();
            const afterSuccess = () => {
                for (let i = 0, len = this.layouts.layouts.length; i < len; i++) {
                    if (this.layouts.layouts[i].id === this.id) {
                        this.layouts.layouts.splice(i, 1);
                        this.layouts.toggleUI();
                        this.el.parentNode.removeChild(this.el);
                        return true;
                    }
                }
                return true;
            };
            submitModal(url, modal, 'DELETE', this.layouts.csrf, null, afterSuccess);
        };
        fetchModal(url, modal, formAction);
    }

    layoutCopy(e) {
        e.preventDefault();
        const url = `${this.layouts.baseUrl}${this.id}/copy${this.published ? '?published=true' : ''}`;
        const modal = new NlModal({
            preload: true,
            autoClose: false,
        });
        document.body.click();
        const formAction = (ev) => {
            ev.preventDefault();
            modal.loadingStart();
            const formEl = modal.el.getElementsByTagName('FORM')[0];
            const afterSuccess = (data) => {
                const newLayoutEl = document.createElement('div');
                newLayoutEl.className = 'nl-panel nl-layout';
                this.el.parentElement.appendChild(newLayoutEl);
                newLayoutEl.innerHTML = data;
                const newLayout = new NlLayout(newLayoutEl, this.layouts);
                newLayout.scrollToMe();
            };
            submitModal(url, modal, 'POST', this.layouts.csrf, new URLSearchParams(new FormData(formEl)), afterSuccess);
        };
        fetchModal(url, modal, formAction);
    }

    clearLayoutCache(e) {
        e.preventDefault();
        const url = `${this.layouts.baseUrl}${this.id}/cache`;
        const modal = new NlModal({
            preload: true,
            autoClose: false,
        });
        document.body.click();
        const formAction = (ev) => {
            ev.preventDefault();
            modal.loadingStart();
            submitModal(url, modal, 'POST', this.layouts.csrf, null);
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
        const url = `${this.layouts.baseUrl}${this.id}/cache/blocks`;
        document.body.click();
        const formAction = (ev) => {
            ev.preventDefault();
            modal.loadingStart();
            const formEl = modal.el.getElementsByTagName('FORM')[0];
            submitModal(url, modal, 'POST', this.layouts.csrf, new URLSearchParams(new FormData(formEl)), null, () => indeterminateCheckboxes(modal.el));
        };
        fetchModal(url, modal, formAction, () => indeterminateCheckboxes(modal.el));
    }

    clearRelatedLayoutCaches(e) {
        e.preventDefault();
        const modal = new NlModal({
            preload: true,
            autoClose: false,
            className: 'nl-modal-cache',
        });
        const url = `${this.layouts.baseUrl}${this.id}/cache/related_layouts`;
        document.body.click();
        const formAction = (ev) => {
            ev.preventDefault();
            modal.loadingStart();
            const formEl = modal.el.getElementsByTagName('FORM')[0];
            submitModal(url, modal, 'POST', this.layouts.csrf, new URLSearchParams(new FormData(formEl)), null, () => indeterminateCheckboxes(modal.el));
        };
        fetchModal(url, modal, formAction, () => indeterminateCheckboxes(modal.el));
    }

    setupEvents() {
        this.el.addEventListener('click', (e) => {
            if (e.target.closest('.js-layout-delete')) {
                this.layoutDelete(e);
            } else if (e.target.closest('.js-layout-copy')) {
                this.layoutCopy(e);
            } else if (e.target.closest('.js-layout-clear-cache')) {
                this.clearLayoutCache(e);
            } else if (e.target.closest('.js-layout-clear-block-caches')) {
                this.clearBlockCaches(e);
            } else if (e.target.closest('.js-layout-clear-related-layouts-caches')) {
                this.clearRelatedLayoutCaches(e);
            }
        });

        this.selectExport.addEventListener('change', () => {
            this.selected = this.selectExport.checked;
        });
    }

    toggleSelected(select) {
        this.selected = select;
        this.selectExport.checked = select;
    }

    scrollToMe() {
        this.el.scrollIntoView({
            behavior: 'smooth',
        });
    }
}
