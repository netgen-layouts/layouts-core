import NetgenCore from '@netgen/layouts-ui-core';
import FileSaver from 'file-saver';
import NlLayout from './layout';

const $ = NetgenCore.$;

/* layouts plugin */
export default class NlLayouts {
    constructor(el) {
        this.$el = $(el);
        this.viewList = localStorage.getItem('ngLayoutsViewList') === 'true';
        this.layouts = [];
        this.$layouts = this.$el.find('.nl-layouts');
        this.$noLayoutsMsg = this.$el.find('.nl-no-items');
        this.$layoutsHead = this.$el.find('.nl-layouts-head');
        this.$toggleViewBtn = this.$el.find('.js-change-layouts-view');
        this.$exportLayoutsBtn = this.$el.find('.js-export-layouts');
        this.$toggleAllCheckbox = this.$el.find('#toggleSelectAll');
        this.shared = typeof this.$el.data('shared') !== 'undefined';
        this.baseUrl = $('meta[name=ngbm-admin-base-path]').attr('content') + (this.shared ? '/shared_layouts/' : '/layouts/');
        this.sorting = JSON.parse(localStorage.getItem(this.shared ? 'ngSharedLayoutsSorting' : 'ngLayoutsSorting')) || {
            sort: 'name',
            direction: 'asc',
        };

        this.init();
    }

    init() {
        this.$el.data('layouts', this);

        this.setViewClass();
        this.setupEvents();
        this.initializeLayoutsPlugin();
        this.changeSortSelects();
        this.reorderLayouts();
        this.toggleUI();

        this.$el.show();
    }

    setupEvents() {
        this.$el.on('click', '.js-change-layouts-view', (e) => {
            e.preventDefault();
            this.viewList = !this.viewList;
            localStorage.setItem('ngLayoutsViewList', this.viewList);
            this.setViewClass();
        });

        this.$exportLayoutsBtn.on('click', this.startExport.bind(this));
        this.$el.on('click', '.js-cancel-export', this.endExport.bind(this));
        this.$el.on('click', '.js-download-layouts', this.downloadLayouts.bind(this));
        this.$toggleAllCheckbox.on('change', this.toggleSelectAll.bind(this));

        this.$el.on('reorder', this.reorderLayouts.bind(this));

        this.$el.on('click', '.js-reorder-layouts', (e) => {
            e.preventDefault();
            this.setSorting(e.target.dataset.sorting);
        });
        this.$el.on('change', '#layout-sorting-sort', (e) => {
            this.selectSorting(e.target.value);
        });
        this.$el.on('change', '#layout-sorting-direction', (e) => {
            this.selectSortDirection(e.target.value, true);
        });
    }

    toggleUI() {
        if (!this.layouts.length) {
            this.$noLayoutsMsg.show();
            this.$layoutsHead.hide();
            this.$toggleViewBtn.hide();
            this.$exportLayoutsBtn.hide();
        } else {
            this.$noLayoutsMsg.hide();
            this.$layoutsHead.css('display', 'flex');
            this.$toggleViewBtn.show();
            this.$exportLayoutsBtn.show();
        }
    }

    setViewClass() {
        this.viewList ? this.$el.addClass('nl-layouts-view-list').removeClass('nl-layouts-view-grid') : this.$el.addClass('nl-layouts-view-grid').removeClass('nl-layouts-view-list');
    }

    initializeLayoutsPlugin() {
        this.$el.find('.nl-layout').each((i, el) => {
            if ($(el).data('layout')) return;
            $(el).data('layouts', this);
            const newLayout = new NlLayout(el);
            this.layouts.push(newLayout);
        });
    }

    startExport(e) {
        e.preventDefault();
        this.$el.addClass('layout-export');
    }

    endExport(e) {
        e && e.preventDefault();
        this.$el.removeClass('layout-export');
        this.layouts.forEach((layout) => {
            layout.selected && layout.toggleSelected(false);
        });
        this.$toggleAllCheckbox.prop('checked', false);
    }

    toggleSelectAll(e) {
        this.layouts.forEach(layout => layout.published && layout.toggleSelected(e.currentTarget.checked));
    }

    static downloadFile(fileName, json) {
        const blob = new Blob([json], { type: 'application/json;charset=UTF-8' });
        FileSaver.saveAs(blob, fileName);
    }

    downloadLayouts(e) {
        e.preventDefault();
        const selectedLayouts = [];
        this.layouts.forEach(layout => layout.selected && selectedLayouts.push(layout.id));
        $.ajax({
            type: 'POST',
            url: `${this.baseUrl}export`,
            dataType: 'json',
            data: {
                layout_ids: selectedLayouts,
            },
            beforeSend: () => $('.ng-layouts-app').addClass('ajax-loading'),
            success: (data, status, jqXHR) => {
                NlLayouts.downloadFile(jqXHR.getResponseHeader('X-Filename'), jqXHR.responseText);
                this.endExport();
            },
            complete: () => $('.ng-layouts-app').removeClass('ajax-loading'),
        });
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
        this.$el.trigger('reorder');
    }

    selectSorting(sort) {
        this.sorting.sort = sort;
        this.saveSorting();
        this.$el.trigger('reorder');
    }

    selectSortDirection(direction) {
        this.sorting.direction = direction;
        this.saveSorting();
        this.$el.trigger('reorder');
    }

    saveSorting() {
        localStorage.setItem(this.shared ? 'ngSharedLayoutsSorting' : 'ngLayoutsSorting', JSON.stringify(this.sorting));
    }

    reorderLayouts() {
        const self = this;
        const direction = this.sorting.direction === 'asc' ? 1 : -1;
        this.layouts.sort((a, b) => {
            let compA = a.attributes[self.sorting.sort];
            let compB = b.attributes[self.sorting.sort];
            typeof compA === 'string' && (compA = compA.toUpperCase());
            typeof compB === 'string' && (compB = compB.toUpperCase());
            if (compA === compB) { // if attributes are the same, sort by name
                return a.attributes.name.toUpperCase() < b.attributes.name.toUpperCase() ? -1 : 1;
            }
            return compA < compB ? direction * -1 : direction;
        });
        $.each(this.layouts, (i, layout) => self.$layouts.append(layout.$el));
        $('.js-reorder-layouts.active').removeClass('active sorting-asc sorting-desc');
        $(`.js-reorder-layouts[data-sorting=${this.sorting.sort}]`).addClass(`active sorting-${this.sorting.direction}`);
    }

    changeSortSelects() {
        this.$el.find('#layout-sorting-sort').val(this.sorting.sort);
        this.$el.find('#layout-sorting-direction').val(this.sorting.direction);
    }
}
