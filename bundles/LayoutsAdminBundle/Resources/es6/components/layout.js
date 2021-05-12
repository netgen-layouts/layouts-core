import NlModal from './modal';
import Tooltip from '../plugins/tooltip';
import { indeterminateCheckboxes, fetchModal, submitModal } from '../helpers';

/* layout plugin */
export default class NlLayout {
    constructor(el, layouts) {
        this.el = el;
        this.layouts = layouts;
        this.attributes = el.getElementsByClassName('nl-layout-content')[0].dataset;
        this.id = this.attributes.id;
        this.published = !!this.attributes.published;
        this.shared = this.el.parentElement.classList.contains('nl-shared-layouts');
        this.selectElement = document.getElementById(`export${this.id}`);
        this.selected = this.selectElement && this.selectElement.checked;
        [this.checkBoxContainer] = this.el.getElementsByClassName('nl-export-checkbox');
        [this.checkbox] = this.checkBoxContainer.getElementsByTagName('label');
        this.type = 'layout';

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

    handleCheckboxDisable(state) {
        if (state) {
            this.checkbox.style.opacity = '1';
        } else {
            this.selectElement.disabled = false;
            this.checkbox.style.opacity = '';
        }
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

    showRelatedLayouts(e) {
        e.preventDefault();
        if (!this.published) return;
        const modal = new NlModal({
            preload: true,
            autoClose: false,
            className: 'nl-modal-cache',
        });
        const url = `${this.layouts.baseUrl}${this.id}/related_layouts`;
        document.body.click();
        fetchModal(url, modal);
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
              } else if (e.target.closest('.js-layout-show-related')) {
                  this.showRelatedLayouts(e);
            } else if (e.target.closest('.js-layout-clear-related-layouts-caches')) {
                this.clearRelatedLayoutCaches(e);
            }
        });

        if (this.selectElement) {
            this.selectElement.addEventListener('change', () => {
                this.selected = this.selectElement.checked;

                if (this.selected) {
                        this.layouts.setSelecting(true);
                } else {
                    this.layouts.checkboxLoop();
                }
            });
        }
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

    canExport() {
        return this.published;
    }
}
