import { saveAs } from 'file-saver';

const downloadFile = (fileName, json) => {
    const blob = new Blob([json], { type: 'application/json;charset=UTF-8' });
    saveAs(blob, fileName);
};

/* export plugin */
export default class NlExport {
    constructor(el, items, itemType) {
        this.el = el;
        this.items = items;
        this.itemType = itemType;
        [this.exportBtn] = this.el.getElementsByClassName('js-export');
        this.toggleAllCheckbox = document.getElementById('toggleSelectAll');
        this.csrf = document.querySelector('meta[name=nglayouts-admin-csrf-token]').getAttribute('content');
        this.baseUrl = `${window.location.origin}${document.querySelector('meta[name=nglayouts-admin-base-path]').getAttribute('content')}/transfer/`;

        this.init();
    }

    init() {
        this.setupEvents();
        this.toggleUI();
    }

    setupEvents() {
        this.el.addEventListener('click', (e) => {
            if (e.target.closest('.js-cancel-export')) {
                this.endExport(e);
            } else if (e.target.closest('.js-download-export')) {
                this.downloadExport(e, this.items);
            }
        });

        this.exportBtn.addEventListener('click', this.startExport.bind(this));
        this.toggleAllCheckbox.addEventListener('change', this.toggleSelectAll.bind(this));
    }

    toggleUI() {
        if (!this.items.length) {
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
        this.items.forEach((item) => {
            item.selected && item.toggleSelected(false);
        });
        this.toggleAllCheckbox.checked = false;
    }

    toggleSelectAll(e) {
        this.items.forEach(item => item.canExport() && item.toggleSelected(e.currentTarget.checked));
    }

    downloadExport(e, items) {
        e.preventDefault();
        const selectedItems = [];
        const layoutsAppEl = document.getElementsByClassName('ng-layouts-app')[0];
        items.forEach(item => item.selected && selectedItems.push(item.id));
        layoutsAppEl.classList.add('ajax-loading');
        const itemIds = selectedItems.map(item => `item_ids[]=${item}`);
        const body = new URLSearchParams(itemIds.join('&'));
        let fileName = '';
        fetch(`${this.baseUrl}export/${this.itemType}`, {
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
