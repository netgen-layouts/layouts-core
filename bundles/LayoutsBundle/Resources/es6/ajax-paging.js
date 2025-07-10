import 'whatwg-fetch';
import './helpers/custom_event_polyfill';

const templateEngine = (html, options) => {
  const re = /<%=([^%>]+)?%>/g;
  const reExp = /(^( )?(if|for|else|switch|case|break|{|}))(.*)?/g;
  let code = 'var r=[];\n';
  let cursor = 0;
  let match;
  const add = (line, js) => {
    js ? (code += line.match(reExp) ? `${line}\n`
      : `r.push(${line});\n`)
      : (code += line !== '' ? `r.push("${line.replace(/"/g, '\\"')}");\n` : '');
    return add;
  };
  while (match = re.exec(html)) { // eslint-disable-line no-cond-assign
    add(html.slice(cursor, match.index))(match[1], true);
    cursor = match.index + match[0].length;
  }
  add(html.substr(cursor, html.length - cursor));
  code += 'return r.join("");';
  return new Function(code.replace(/[\r\t\n]/g, '')).apply(options); // eslint-disable-line no-new-func
};

class AjaxPaging {
  constructor(el) {
    if (el.dataset.pagingRendered) return;
    this.el = el;
    [this.container] = el.getElementsByClassName('ajax-container');
    this.nav = [...el.getElementsByClassName('ajax-navigation')];
    this.loadInitial = this.el.hasAttribute('data-load-initial');
    this.baseUrl = this.el.dataset.baseUrl;

    this.el.ajaxPaging = this;

    this.init();
  }

  init() {
    this.loadInitial && this.getPage(this.baseUrl);
    this.nav.length && this.initPaging();
    this.el.dataset.pagingRendered = true;
  }

  initPaging() {
    this.pagerData = { ...this.nav[0].dataset };
    this.page = parseInt(this.pagerData.page, 10);
    this.totalPages = parseInt(this.pagerData.totalPages, 10);

    if (this.totalPages > 1) {
      this.renderNavigation();
    }

    this.setupEvents();
  }

  renderNavigation() {
    this.nav.forEach((pager) => {
      pager.innerHTML = templateEngine(this.pagerData.template, { pages: this.totalPages, page: this.page, url: this.generateUrl.bind(this) }); // eslint-disable-line no-param-reassign
    });
  }

  generateUrl(page) {
    return `${this.baseUrl}&page=${page}`;
  }

  setupEvents() {
    this.nav.forEach((pager) => {
      pager.addEventListener('click', (e) => {
        if (e.target.tagName === 'A') {
          e.preventDefault();
          this.container.setAttribute('aria-busy', 'true');
          this.getPage(e.target.href);
        }
      });
    });
  }

  getPage(path) {
    this.loadingStart();
    const pageParam = /&page=\d+/.exec(path);
    const nextPage = pageParam ? parseInt(pageParam[0].replace(/&page=/, ''), 10) : 1;

    fetch(path, {
      credentials: 'same-origin',
    }).then((response) => {
      if (response.ok) {
        return response.text();
      }
      throw new Error(`Looks like there was a problem. Status Code: ${response.status}`);
    }).then((html) => {
      this.loadingStop();
      pageParam && this.setNextPage(nextPage);
      this.renderNewPage(html);
    }).catch((err) => {
      this.loadingStop();
      console.log('Fetch Error :-S', err); // eslint-disable-line no-console
    });
  }

  setNextPage(nextPage) {
    this.page = nextPage;
    this.renderNavigation();
  }

  renderNewPage(html) {
    const domParser = new DOMParser();
    const doc = domParser.parseFromString(html, 'text/html');
    const newItems = doc.body.children;
    const firstNewItemRef = newItems[0];

    switch (this.pagerData.type) {
      case 'load_more':
        this.container.append(...newItems);
        if (firstNewItemRef) {
          firstNewItemRef.setAttribute('tabindex', '0');
          firstNewItemRef.focus();
          firstNewItemRef.removeAttribute('tabindex');
        }
        break;
      default:
        this.container.replaceChildren(...newItems);
    }
    this.container.setAttribute('aria-busy', 'false');

    this.el.dispatchEvent(new CustomEvent('ajax-paging-added', { bubbles: true, cancelable: true, detail: { instance: this } }));
  }

  loadingStart() {
    this.el.classList.add('ajax-loading');
  }

  loadingStop() {
    this.el.classList.remove('ajax-loading');
  }
}

const initPaging = () => {
  [...document.getElementsByClassName('ajax-collection')].forEach(el => new AjaxPaging(el));
};

window.addEventListener('load', () => {
  initPaging();
});

window.addEventListener('renderAjaxPaging', () => {
  initPaging();
});

window.addEventListener('ngl:preview:block:refresh', () => {
  initPaging();
});
