import NetgenCore from 'netgen-core';
import NlRule from './rule';

const $ = NetgenCore.$;

/* nl rules app plugin */
export default class NlRules {
    constructor(el) {
        this.$el = $(el);
        this.$rulesContainer = this.$el.find('.nl-rules');
        this.rules = [];
        this.$rulesHeader = this.$el.find('.nl-rules-head');
        this.$noRulesMsg = this.$el.find('.nl-no-items');
        this.$sortBtn = this.$el.find('.js-sort-start');
        this.csrf = $('meta[name=ngbm-admin-csrf-token]').attr('content');
        this.baseUrl = `${$('meta[name=ngbm-admin-base-path]').attr('content')}layout_resolver/`;
        this.filter = JSON.parse(localStorage.getItem('ngMappingFilters')) || [];

        this.initialize();
    }

    initialize() {
        this.$el.data('rules', this);
        this.$el.find('.nl-dropdown').dropdown();
        this.initializeFilters();
        this.initializeRulePlugin();
        this.setupEvents();
        this.setRulesTop();
        this.toggleUI();
        this.$el.css('visibility', 'visible');
    }
    initializeRulePlugin() {
        this.$el.find('.nl-rule').each((i, el) => {
            const newRule = new NlRule(el);
            this.rules.push(newRule);
        });
        this.filterMappings();
    }
    deleteRule(id) {
        for (let i = 0, len = this.rules.length; i < len; i++) {
            if (this.rules[i].id === id) {
                this.rules.splice(i, 1);
                this.toggleUI();
                return true;
            }
        }
        return true;
    }
    toggleUI() {
        if (!this.rules.length) {
            this.$rulesHeader.hide();
            this.$noRulesMsg.show();
        } else {
            this.$rulesHeader.css('display', 'flex');
            this.$noRulesMsg.hide();
        }
        this.rules.length < 2 ? this.$sortBtn.hide() : this.$sortBtn.show();
    }
    setRulesTop() {
        this.$rulesContainer.css('top', this.$rulesHeader.position().top + this.$rulesHeader.outerHeight());
    }
    setupEvents() {
        const $appContainer = $('.ng-layouts-app');
        const self = this;

        $(document).bind('delete-rule', (e, data) => this.deleteRule(data.nlRule.id));

        $(document).bind('ajaxSend', () => {
            $appContainer.addClass('ajax-loading');
        }).bind('ajaxComplete', (data, status) => {
            $appContainer.removeClass('ajax-loading');
            if (status.status === 401 || status.status === 403) {
                location.reload();
            }
        });

        $.ajaxPrefilter((options, originalOptions, jqXHR) => jqXHR.setRequestHeader('X-CSRF-Token', self.csrf));

        this.$el.on('click', '.js-add-rule', (e) => {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: `${this.baseUrl}rules`,
                success: (data) => {
                    const $newRule = $('<div class="nl-rule">');
                    $newRule.html(data).addClass('show-body');
                    this.$rulesContainer.append($newRule);
                    const newRule = new NlRule($newRule);
                    this.rules.push(newRule);
                    this.toggleUI();
                    $('.nl-rules').animate({ scrollTop: $('.nl-rules')[0].scrollHeight }, 750);
                },
            });
        });

        this.$el.on('click', '.js-sort-start', () => {
            $appContainer.addClass('sorting');
            $('.nl-rule-between').remove();
            this.$rulesContainer.sortable({
                items: '> .nl-rule',
                axis: 'y',
            });
            this.initialSort = this.$rulesContainer.sortable('toArray', { attribute: 'data-id' });
            this.$rulesContainer.sortable({
                update: () => {
                    this.sorted = true;
                },
            });
        });
        this.$el.on('click', '.js-sort-save', () => {
            const sorted = this.$rulesContainer.sortable('toArray', { attribute: 'data-id' });

            $.ajax({
                type: 'POST',
                url: `${this.baseUrl}rules/priorities`,
                data: {
                    rule_ids: sorted,
                },
                success: () => {
                    location.reload(); /* reload to set new priority numbers */
                },
            });
        });
        this.$el.on('click', '.js-sort-cancel', () => {
            if (this.sorted) {
                const tempSort = [];
                this.initialSort.forEach((t) => {
                    tempSort.push(this.$rulesContainer.find(`.nl-rule[data-id=${t}]`).detach());
                });
                this.$rulesContainer.html(tempSort);
            }
            this.sorted = false;

            $appContainer.removeClass('sorting');
            this.$rulesContainer.sortable('destroy');
            this.filterMappings();
        });
    }

    /* mapping filtering */
    initializeFilters() {
        this.updateFilterInputs();
        this.$el.on('change', 'input[name=filter-mappings]', this.updateFilter.bind(this));
        this.$el.on('click', '.js-check-all', this.checkAllFilters.bind(this));
        this.$el.on('click', '.js-check-none', this.checkNoneFilters.bind(this));
    }
    updateFilterInputs() {
        $('.nl-mappings-filter').find('input[name=filter-mappings]').each((i, el) => $(el).prop('checked', this.filter.includes(el.value)));
    }
    updateFilter(e) {
        e && e.preventDefault();
        e.target.checked ? this.filter.push(e.target.value) : (this.filter = this.filter.filter(item => item !== e.target.value));
        this.saveFilterToStorage();
        this.filterMappings();
    }
    filterMappings() {
        let showBetween = true;
        $('.nl-rule-between').remove();
        const $filterAmount = this.$el.find('.filter-checked-amount');
        if (this.filter.length) {
            $filterAmount.show().html(this.filter.length);
        } else {
            $filterAmount.hide();
        }
        this.rules.forEach((rule) => {
            const isHidden = !!this.filter.length && !this.filter.includes(rule.attributes.targetType);
            rule.isHidden = isHidden; // eslint-disable-line no-param-reassign
            rule.$el.toggleClass('nl-rule-hidden', isHidden);
            if (!isHidden) {
                showBetween = true;
            } else if (showBetween) {
                rule.$el.before('<div class="nl-rule-between"><i></i></div>');
                showBetween = false;
            }
        });
        this.$el.toggleClass('no-filtered-items', !!this.rules.length && !this.rules.some(rule => !rule.isHidden));
    }
    saveFilterToStorage() {
        localStorage.setItem('ngMappingFilters', JSON.stringify(this.filter));
    }
    checkAllFilters(e) {
        e && e.preventDefault();
        const newFilter = [];
        $('.nl-mappings-filter').find('input[name=filter-mappings]').each((i, el) => newFilter.push(el.value));
        this.filter = newFilter;
        this.updateFilterInputs();
        this.saveFilterToStorage();
        this.filterMappings();
    }
    checkNoneFilters(e) {
        e && e.preventDefault();
        this.filter = [];
        this.updateFilterInputs();
        this.saveFilterToStorage();
        this.filterMappings();
    }
    /* /mapping filtering */
}
