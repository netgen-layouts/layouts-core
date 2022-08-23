/* modal plugin */
export default class NlModal {
    constructor(opt) {
        this.options = Object.assign({
            preload: false,
            cancelDisabled: false,
            autoClose: true,
            body: '<p>Empty modal</p>',
            title: '',
            cancelText: 'Cancel',
            applyText: 'OK',
        }, opt);
        [this.appEl] = document.getElementsByClassName('ng-layouts-app');
        this.el = document.createElement('div');
        this.el.className = 'nl-modal-mask';
        if (this.options.className) this.el.classList.add(this.options.className);
        this.container = document.createElement('div');
        this.container.className = 'nl-modal-container';
        this.loader = document.createElement('div');
        this.loader.className = 'nl-modal-loader';
        this.loader.innerHTML = '<span></span>';

        this.onKeyDown = (e) => {
            e.keyCode === 27 && this.close();
            e.keyCode === 13 && e.preventDefault();
        };

        this.onKeyDown = this.onKeyDown.bind(this);

        this.loadModal();
        this.setupEvents();
    }

    deleteValidation() {
        const regex = new RegExp(this.deleteInput.pattern);
        if (regex.test(this.deleteInputValue)) {
            this.applyElement.disabled = false;
        } else {
            this.applyElement.disabled = true;
        }
    }

    loadModal() {
        this.options.preload ? this.loadingStart() : this.container.innerHTML = this.getHtml();
        this.el.appendChild(this.loader);
        this.el.appendChild(this.container);
        this.appEl.appendChild(this.el);
        window.addEventListener('keydown', this.onKeyDown);
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
        this.el.addEventListener('click', (e) => {
            if (e.target.closest('.close-modal')) {
                this.close(e);
            } else if (e.target.closest('.action-apply')) {
                this.apply(e);
            } else if (e.target.closest('.action-cancel')) {
                this.cancel(e);
            }
        });
    }

    apply(e) {
        e && e.preventDefault();
        this.el.dispatchEvent(new Event('apply'));
        this.options.autoClose && this.close();
    }

    cancel(e) {
        e && e.preventDefault();
        this.close();
    }

    close(e) {
        e && e.preventDefault();
        this.el.dispatchEvent(new Event('cancel'));
        this.destroy();
        window.removeEventListener('keydown', this.onKeyDown);
    }

    deleteSetup() {
        this.deleteInput = document.getElementById('delete-verification');
        [this.applyElement] = this.el.getElementsByClassName('action-apply');
        this.deleteInputValue = '';
        if (this.deleteInput) {
            this.applyElement.disabled = true;
        }

        if (this.deleteInput) {
            this.deleteInput.addEventListener('keyup', (e) => {
                this.deleteInputValue = e.target.value;
                this.deleteValidation();
            });
        }
    }

    insertModalHtml(html) {
        this.container.innerHTML = html;
        this.loadingStop();
        this.deleteSetup();
    }

    loadingStart() {
        this.el.classList.add('modal-loading');
    }

    loadingStop() {
        this.el.classList.remove('modal-loading');
    }

    destroy() {
        this.el.dispatchEvent(new Event('close'));
        this.el.parentElement && this.el.parentElement.removeChild(this.el);
    }
}
