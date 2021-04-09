import { saveAs } from 'file-saver';

const downloadFile = (fileName, json) => {
    const blob = new Blob([json], { type: 'application/json;charset=UTF-8' });
    saveAs(blob, fileName);
};

/* export plugin */
export default class NlExport {
    constructor(el, entities, container) {
        this.el = el;
        this.container = container;
        this.entities = entities;
        [this.exportBtn] = this.el.getElementsByClassName('js-export');
        this.toggleAllCheckbox = document.getElementById('toggleSelectAll');
        this.csrf = document.querySelector('meta[name=nglayouts-admin-csrf-token]').getAttribute('content');
        this.baseUrl = `${window.location.origin}${document.querySelector('meta[name=nglayouts-admin-base-path]').getAttribute('content')}/transfer/`;

        this.init();
    }

    init() {
        this.setupEvents();
        // this.toggleUI();
    }

    setupEvents() {
        this.el.addEventListener('click', (e) => {
            if (e.target.closest('.js-export')) {
                e.stopPropagation();
                this.downloadExport(e);
            }
        });

        this.exportBtn && this.exportBtn.addEventListener('click', this.startExport.bind(this));
        this.toggleAllCheckbox.addEventListener('change', this.toggleSelectAll.bind(this));
    }

    toggleUI() {
        if (!Object.keys(this.entities).length) {
            this.exportBtn.style.display = 'none';
        } else {
            this.exportBtn.style.display = 'inline-block';
        }
    }

    startExport(e) {
        e.preventDefault();
        this.el.classList.add('export');
    }

    endExport(e) {
        e && e.preventDefault();
        this.el.classList.remove('export');
        Object.keys(this.entities).forEach((key) => {
            this.entities[key].selected = false;
            this.entities[key].selectElement.checked = false;
            this.entities[key].checkBoxContainer.style.visibility = '';
            this.entities[key].el.classList.remove('selected');
        });
        this.toggleAllCheckbox.checked = false;
        this.container.checkboxLoop();
    }

    toggleSelectAll(e) {
        Object.keys(this.entities).forEach(key => this.entities[key].canExport() && this.entities[key].toggleSelected(e.currentTarget.checked));
    }

    downloadExport(e) {
        e.preventDefault();
        e.stopPropagation();
        const selectedEntities = [];
        const layoutsAppEl = document.getElementsByClassName('ng-layouts-app')[0];
        Object.keys(this.entities).forEach(key => this.entities[key].selected && selectedEntities.push({ id: this.entities[key].id, type: this.entities[key].type }));
        layoutsAppEl.classList.add('ajax-loading');
        const entities = selectedEntities.map(entity => `entities[${entity.id}]=${entity.type}`);
        const body = new URLSearchParams(entities.join('&'));
        let fileName = '';
        if (selectedEntities.length) {
            fetch(`${this.baseUrl}export`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-Token': this.csrf,
                },
                body,
            }).then((response) => {
                if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
                fileName = response.headers.get('X-Filename');
                return response.text();
            }).then((data) => {
                downloadFile(fileName, data);
                this.endExport();
                layoutsAppEl.classList.remove('ajax-loading');
            }).catch((error) => {
                layoutsAppEl.classList.remove('ajax-loading');
                console.log(error); // eslint-disable-line no-console
            });
        }
    }
}
