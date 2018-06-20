import NetgenCore from '@netgen/layouts-core-ui';

const $ = NetgenCore.$;

/* tooltip plugin */
$.fn.tooltip = function () {
    /*
     data attributes on element:
     data-position = top, bottom, left, right - for placement of tooltip
     data-width = int - for max width of tooltip in pixels
     */
    return this.each(function () {
        const title = $(this).attr('title');
        const placement = $(this).data('position') || 'top';
        const ttWidth = $(this).data('width');
        const tt = $(`<div class="nl-tooltip"><div class="nl-tooltip-inner">${title}</div></div>`);
        const getCoordinates = () => {
            const offset = $(this).offset();
            if (placement === 'bottom') {
                offset.left += $(this).width() / 2;
                offset.top += $(this).height();
            } else if (placement === 'left') {
                offset.top += $(this).height() / 2;
                offset.right = $(document).width() - offset.left;
                offset.left = 'auto';
            } else if (placement === 'right') {
                offset.top += $(this).height() / 2;
                offset.left += $(this).width();
            } else {
                offset.left += $(this).width() / 2;
            }
            return offset;
        };

        if (title) {
            $(this).removeAttr('title');
            $(this).after(tt);
            if (ttWidth) {
                tt.css('width', ttWidth);
            }
            $(this).on('mouseenter', () => {
                const coordinates = getCoordinates();
                tt.addClass(`nl-tooltip-active nl-tooltip-${placement}`);
                tt.offset(coordinates);
                if (placement === 'left') tt.css('right', coordinates.right);
            });
            $(this).on('mouseleave', () => tt.attr('class', 'nl-tooltip'));
        }
    });
};

/* dropdown plugin */
class Dropdown {
    constructor(el) {
        this.$el = $(el);
        this.$toggle = this.$el.find('.nl-dropdown-toggle');
        this.$dropdown = this.$el.find('.nl-dropdown-menu');
        this.opened = false;

        this.setupEvents();
    }
    setupEvents() {
        this.$toggle.on('click', (e) => {
            e.stopPropagation();
            $('.nl-dropdown').each((i, el) => {
                $(el).data('dropdown') !== this &&
                $(el).data('dropdown').opened &&
                $(el).data('dropdown').close();
            });
            this.opened ? this.close() : this.open();
            this.$toggle.blur();
        });
    }
    close() {
        this.opened = false;
        this.$el.removeClass('nl-dropdown-active');
    }
    open() {
        const rect = this.$el[0].getBoundingClientRect();
        window.innerHeight - rect.bottom < this.$dropdown[0].scrollHeight && rect.top >= this.$dropdown[0].scrollHeight ? this.$el.addClass('nl-dropdown-top') : this.$el.removeClass('nl-dropdown-top');
        this.opened = true;
        this.$el.addClass('nl-dropdown-active');
    }
}

$.fn.dropdown = function () {
    /*
     data attributes on element:
     data-position = left, right - for placement of dropdown
    */
    if (!$(document).data('hide-dropdown')) {
        $(document).data('hide-dropdown', 'true');
        $(document).on('click.hideDropdown', (e) => {
            !$(e.target).closest('.nl-dropdown').length &&
            $('.nl-dropdown').each((i, el) => {
                $(el).data('dropdown').opened && $(el).data('dropdown').close();
            });
        });
    }

    return this.each(function () {
        if (!$(this).data('dropdown')) {
            const instance = new Dropdown(this);
            $(this).data('dropdown', instance);
        }
    });
};
