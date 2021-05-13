import { parser } from '../helpers';

/* multientry plugin */
export default class MultiEntry {
    constructor(el, opt = {}) {
        this.el = el;
        this.options = Object.assign({
            insert_first: true,
            last_item_can_be_removed: false,
            limit: null,
            show_errors: true,
        }, opt, el.dataset);
        this.id = 0;
        [this.items_container] = el.getElementsByClassName('multientry-items');
        this.items_exist = !![...this.items_container.getElementsByClassName('multientry-item')].length;
        [this.add_button] = el.getElementsByClassName('multientry_add');
        this.item_template = this.el.dataset.prototype;
        this.close_element = '<i class="material-icons close">close</i>';
        this.error_message = this.options.error_message || (`Max number of items: ${this.options.limit}`);
        this.error_el = document.createElement('div');
        this.error_el.className = 'multientry-error';
        this.error_el.innerHTML = this.error_message;

        this.setupDom();
        this.setupEvents();
    }

    nextId() {
        const timestamp = +new Date();
        return `${timestamp}${this.id++}`;
    }

    renderItemTemplate() {
        const newItem = parser(this.item_template.replace(/__name__/g, this.nextId()))[0];
        newItem.classList.add('new');
        newItem.appendChild(parser(this.close_element)[0]);
        return newItem;
    }

    setupDom() {
        [...this.el.getElementsByClassName('multientry-item')].forEach((el) => {
            el.appendChild(parser(this.close_element)[0]);
        });
        this.options.insert_first && !(this.items_exist) && this.add();
    }

    setupEvents() {
        this.el.addEventListener('click', (e) => {
            if (e.target.closest('.close')) {
                e.preventDefault();
                this.remove(e.target.closest('.multientry-item'));
            }
        });
        this.add_button.addEventListener('click', (e) => {
            e.preventDefault();
            this.add();
        });
    }

    add() {
        if (this.options.limit && this.limitReached()) return;
        const item = this.renderItemTemplate();
        this.trigger('before:add', { item });
        this.items_container.appendChild(item);
        this.trigger('add', { item });
        this.options.limit && this.limitCheck();
    }

    remove(item) {
        if (this.itemsCount() === 1 && !this.options.last_item_can_be_removed) return;
        this.trigger('before:remove', { item });
        item.parentElement.removeChild(item);
        this.options.limit && this.limitCheck();
        this.trigger('remove', { item });
    }

    itemsCount() {
        return [...this.items_container.getElementsByClassName('multientry-item')].length;
    }

    limitCheck() {
        this.limitReached() ? this.onLimitReached() : this.onLimitValid();
    }

    limitReached() {
        return this.itemsCount() >= this.options.limit;
    }

    onLimitValid() {
        this.options.show_errors && this.error_el.parentElement && this.error_el.parentElement.removeChild(this.error_el);
        this.add_button.classList.remove('disabled');
        this.trigger('limit:valid');
    }

    onLimitReached() {
        this.options.show_errors && this.items_container.appendChild(this.error_el);
        this.add_button.classList.add('disabled');
        this.trigger('limit:reached');
    }


    trigger(event, data) {
        const prefix = 'multientry:';
        const detail = Object.assign({}, data, { instance: this });
        this.el.dispatchEvent(new CustomEvent(prefix + event, { bubbles: true, cancelable: true, detail }));
    }
}
