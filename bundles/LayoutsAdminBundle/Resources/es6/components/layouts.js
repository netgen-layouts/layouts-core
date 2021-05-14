import NlLayout from './layout';
import NlExport from './export';

/* layouts plugin */
export default class NlLayouts {
    constructor(el) {
        this.el = el;
        this.viewList = localStorage.getItem('ngLayoutsViewList') === 'true';
        this.layouts = [];
        [this.layoutsEl] = this.el.getElementsByClassName('nl-layouts');
        [this.noLayoutsMsg] = this.el.getElementsByClassName('nl-no-items');
        [this.layoutsHead] = this.el.getElementsByClassName('nl-layouts-head');
        [this.toggleViewBtn] = this.el.getElementsByClassName('js-change-layouts-view');
        [this.exportButton] = document.getElementsByClassName('js-export');
        this.shared = typeof this.el.dataset.shared !== 'undefined';
        this.csrf = document.querySelector('meta[name=nglayouts-admin-csrf-token]').getAttribute('content');
        this.baseUrl = window.location.origin + document.querySelector('meta[name=nglayouts-admin-base-path]').getAttribute('content') + (this.shared ? '/shared_layouts/' : '/layouts/');
        this.sorting = JSON.parse(localStorage.getItem(this.shared ? 'ngSharedLayoutsSorting' : 'ngLayoutsSorting')) || {
            sort: 'name',
            direction: 'asc',
        };

        this.init();
    }

    init() {
        this.setViewClass();
        this.setupEvents();
        this.initializeLayoutsPlugin();
        this.changeSortSelects();
        this.reorderLayouts();
        this.toggleUI();

        this.el.style.display = 'block';
    }

    setupEvents() {
        this.el.addEventListener('click', (e) => {
            if (e.target.closest('.js-change-layouts-view')) {
                e.preventDefault();
                this.viewList = !this.viewList;
                localStorage.setItem('ngLayoutsViewList', this.viewList);
                this.setViewClass();
            } else if (e.target.closest('.js-reorder-layouts')) {
                e.preventDefault();
                this.setSorting(e.target.dataset.sorting);
            }
        });

        this.el.addEventListener('reorder', this.reorderLayouts.bind(this));

        document.getElementById('layout-sorting-sort').addEventListener('change', (e) => {
            this.selectSorting(e.target.value);
        });
        document.getElementById('layout-sorting-direction').addEventListener('change', (e) => {
            this.selectSortDirection(e.target.value, true);
        });
    }

    setSelecting(state) {
        if (this.exportButton) {
            state ? this.exportButton.style.display = 'inline-flex' : this.exportButton.style.display = 'none';
        }
        this.layouts.forEach((layout) => {
            layout.handleCheckboxDisable(state);
        });
    }

    checkboxLoop() {
        let checkBoxCount = 0;
        this.layouts.forEach((layout) => {
            layout.selected ? checkBoxCount++ : null;
        });
        checkBoxCount ? null : this.setSelecting(false);
    }

    toggleUI() {
        if (!this.layouts.length) {
            this.noLayoutsMsg.style.display = 'block';
            this.layoutsHead.style.display = 'none';
            this.toggleViewBtn.style.display = 'none';
        } else {
            this.noLayoutsMsg.style.display = 'none';
            this.layoutsHead.style.display = 'flex';
            this.toggleViewBtn.style.display = 'inline-block';
        }
    }

    setViewClass() {
        this.el.classList.toggle('nl-layouts-view-list', this.viewList);
        this.el.classList.toggle('nl-layouts-view-grid', !this.viewList);
    }

    initializeLayoutsPlugin() {
        [...this.el.getElementsByClassName('nl-layout')].forEach(el => new NlLayout(el, this));
        this.export = new NlExport(this.el, this.layouts, this);
    }

    setSorting(sort) {
        if (this.sorting.sort === sort) {
            this.sorting.direction = this.sorting.direction === 'asc' ? 'desc' : 'asc';
        } else {
            this.sorting = {
                sort,
                direction: 'asc',
            };
        }
        this.changeSortSelects();
        this.saveSorting();
        this.el.dispatchEvent(new Event('reorder'));
    }

    selectSorting(sort) {
        this.sorting.sort = sort;
        this.saveSorting();
        this.el.dispatchEvent(new Event('reorder'));
    }

    selectSortDirection(direction) {
        this.sorting.direction = direction;
        this.saveSorting();
        this.el.dispatchEvent(new Event('reorder'));
    }

    saveSorting() {
        localStorage.setItem(this.shared ? 'ngSharedLayoutsSorting' : 'ngLayoutsSorting', JSON.stringify(this.sorting));
    }

    reorderLayouts() {
        const direction = this.sorting.direction === 'asc' ? 1 : -1;
        this.layouts.sort((a, b) => {
            let compA = a.attributes[this.sorting.sort];
            let compB = b.attributes[this.sorting.sort];
            typeof compA === 'string' && (compA = compA.toUpperCase());
            typeof compB === 'string' && (compB = compB.toUpperCase());
            if (compA === compB) { // if attributes are the same, sort by name
                return a.attributes.name.toUpperCase() < b.attributes.name.toUpperCase() ? -1 : 1;
            }
            return compA < compB ? direction * -1 : direction;
        });
        this.layouts.forEach((layout) => {
            this.layoutsEl.removeChild(layout.el);
            this.layoutsEl.appendChild(layout.el);
        });
        const reorderActive = document.querySelector('.js-reorder-layouts.active');
        if (reorderActive) reorderActive.classList.remove('active', 'sorting-asc', 'sorting-desc');
        const reorderSorting = document.querySelector(`.js-reorder-layouts[data-sorting=${this.sorting.sort}]`);
        if (reorderSorting) reorderSorting.classList.add('active', `sorting-${this.sorting.direction}`);
    }

    changeSortSelects() {
        document.getElementById('layout-sorting-sort').value = this.sorting.sort;
        document.getElementById('layout-sorting-direction').value = this.sorting.direction;
    }
}
