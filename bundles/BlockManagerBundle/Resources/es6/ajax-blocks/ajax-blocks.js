const templateEngine = (html, options) => {
    const re = /<%=([^%>]+)?%>/g;
    const reExp = /(^( )?(if|for|else|switch|case|break|{|}))(.*)?/g;
    let code = 'var r=[];\n';
    let cursor = 0;
    let match;
    const add = (line, js) => {
        js ? (code += line.match(reExp) ? line + '\n' : 'r.push(' + line + ');\n') :
            (code += line !== '' ? 'r.push("' + line.replace(/"/g, '\\"') + '");\n' : '');
        return add;
    };
    while (match = re.exec(html)) {
        add(html.slice(cursor, match.index))(match[1], true);
        cursor = match.index + match[0].length;
    }
    add(html.substr(cursor, html.length - cursor));
    code += 'return r.join("");';
    return new Function(code.replace(/[\r\t\n]/g, '')).apply(options);
};

class AjaxBlock {
    constructor(el) {
        this.el = el;
        this.container = el.querySelector('.ajax-container');
        this.nav = el.querySelector('.ajax-navigation');
        this.loadInitial = this.el.dataset.loadInitial;

        this.init();
    }

    init() {
        this.loadInitial && this.getPage(this.loadInitial);
        this.nav && this.initPaging();
    }

    initPaging() {
        this.pager = this.nav.hasAttribute('data-pager');
        this.pagerData = { ...this.nav.dataset };
        this.page = parseInt(this.pagerData.page, 10);
        this.totalPages = parseInt(this.pagerData.totalPages, 10);
        this.nav.removeAttribute('data-template-pager');
        this.nav.removeAttribute('data-template-load-more');

        this.renderNavigation();
        this.setupEvents();
    }

    renderNavigation() {
        this.nav.innerHTML = templateEngine(this.pager ? this.pagerData.templatePager : this.pagerData.templateLoadMore, { pages: this.totalPages, page: this.page, url: this.generateUrl.bind(this) });
    }

    generateUrl(page) {
        return `${this.pagerData.url}&page=${page}`;
    }

    setupEvents() {
        this.nav.addEventListener('click', (e) => {
            if (e.target.tagName === 'A') {
                e.preventDefault();
                this.getPage(e.target.href);
            }
        });
    }

    getPage(path) {
        this.loadingStart();
        const pageParam = /&page=\d+/.exec(path);
        const nextPage = pageParam ? parseInt(pageParam[0].replace(/&page=/, ''), 10) : 1;

        fetch(path).then((response) => {
            if (response.ok) {
                return response.text();
            }
            throw new Error(`Looks like there was a problem. Status Code: ${response.status}`);
        }).then((html) => {
            this.loadingStop();
            pageParam && this.setNextPage(nextPage);
            this.renderNewBlocks(html);
        })
        .catch((err) => {
            this.loadingStop();
            console.log('Fetch Error :-S', err);
        });
    }

    setNextPage(nextPage) {
        this.page = nextPage;
        this.renderNavigation();
    }

    renderNewBlocks(html) {
        this.pager ? (this.container.innerHTML = html) : (this.container.innerHTML += html);
        this.el.dispatchEvent(new CustomEvent('ajax-blocks-added', { bubbles: true, cancelable: true }));
    }

    loadingStart() {
        this.el.classList.add('ajax-loading');
    }

    loadingStop() {
        this.el.classList.remove('ajax-loading');
    }
}

const ajaxCollections = document.getElementsByClassName('ajax-collection');
[].forEach.call(ajaxCollections, (el) => {
    const ajaxBlock = new AjaxBlock(el);
});
