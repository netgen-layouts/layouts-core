/* tooltip plugin */
class Tooltip {
    constructor(el) {
        this.el = el;
        this.title = el.title;
        this.placement = el.dataset.position || 'top';
        this.ttWidth = el.dataset.width;

        if (this.title) this.init();
    }

    init() {
        this.tooltip = document.createElement('div');
        this.tooltip.classList.add('nl-tooltip');
        this.tooltip.innerHTML = `<div class="nl-tooltip-inner">${this.title}</div>`;

        this.el.removeAttribute('title');
        if (this.ttWidth) this.tooltip.style.width = this.ttWidth;
        this.el.appendChild(this.tooltip);

        this.setupEvents();
    }

    getCoordinates() {
        const rect = this.el.getBoundingClientRect();
        const coordinates = {
            left: rect.left,
            right: rect.right,
            top: rect.top,
            bottom: rect.bottom,
        };
        if (this.placement === 'bottom') {
            coordinates.left += rect.width / 2;
            coordinates.top += rect.height;
        } else if (this.placement === 'left') {
            coordinates.top += rect.height / 2;
            coordinates.right = window.innerWidth - rect.left;
            coordinates.left = 'auto';
        } else if (this.placement === 'right') {
            coordinates.top += rect.height / 2;
            coordinates.left += rect.width;
        } else {
            coordinates.left += rect.width / 2;
        }
        return coordinates;
    }

    setupEvents() {
        this.el.addEventListener('mouseenter', this.showTooltip.bind(this));
        this.el.addEventListener('mouseleave', this.hideTooltip.bind(this));
    }

    showTooltip() {
        const coordinates = this.getCoordinates();
        this.tooltip.style.left = `${coordinates.left}px`;
        this.tooltip.style.top = `${coordinates.top}px`;
        if (this.placement === 'left') this.tooltip.style.right = `${coordinates.right}px`;
        this.tooltip.classList.add(`nl-tooltip-${this.placement}`);
        this.tooltip.classList.add('nl-tooltip-active');
    }

    hideTooltip() {
        this.tooltip.className = 'nl-tooltip';
    }
}

export default Tooltip;
