!function r(o,s,l){function u(t,e){if(!s[t]){if(!o[t]){var a="function"==typeof require&&require;if(!e&&a)return a(t,!0);if(c)return c(t,!0);var n=new Error("Cannot find module '"+t+"'");throw n.code="MODULE_NOT_FOUND",n}var i=s[t]={exports:{}};o[t][0].call(i.exports,function(e){return u(o[t][1][e]||e)},i,i.exports,r,o,s,l)}return s[t].exports}for(var c="function"==typeof require&&require,e=0;e<l.length;e++)u(l[e]);return u}({1:[function(e,t,a){"use strict";var n=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e},i=function(){function n(e,t){for(var a=0;a<t.length;a++){var n=t[a];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(e,t,a){return t&&n(e.prototype,t),a&&n(e,a),e}}();var r=function(){function t(e){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t),this.el=e,this.container=e.querySelector(".ajax-container"),this.nav=e.querySelector(".ajax-navigation"),this.loadInitial=this.el.hasAttribute("data-load-initial"),this.baseUrl=this.el.dataset.baseUrl,this.init()}return i(t,[{key:"init",value:function(){this.loadInitial&&this.getPage(this.baseUrl),this.nav&&this.initPaging()}},{key:"initPaging",value:function(){this.pagerData=n({},this.nav.dataset),this.page=parseInt(this.pagerData.page,10),this.totalPages=parseInt(this.pagerData.totalPages,10),this.nav.removeAttribute("data-template"),1<this.totalPages&&this.renderNavigation(),this.setupEvents()}},{key:"renderNavigation",value:function(){this.nav.innerHTML=function(e,t){for(var a=/<%=([^%>]+)?%>/g,n=/(^( )?(if|for|else|switch|case|break|{|}))(.*)?/g,i="var r=[];\n",r=0,o=void 0,s=function e(t,a){return i+=a?t.match(n)?t+"\n":"r.push("+t+");\n":""!==t?'r.push("'+t.replace(/"/g,'\\"')+'");\n':"",e};o=a.exec(e);)s(e.slice(r,o.index))(o[1],!0),r=o.index+o[0].length;return s(e.substr(r,e.length-r)),i+='return r.join("");',new Function(i.replace(/[\r\t\n]/g,"")).apply(t)}(this.pagerData.template,{pages:this.totalPages,page:this.page,url:this.generateUrl.bind(this)})}},{key:"generateUrl",value:function(e){return this.baseUrl+"&page="+e}},{key:"setupEvents",value:function(){var t=this;this.nav.addEventListener("click",function(e){"A"===e.target.tagName&&(e.preventDefault(),t.getPage(e.target.href))})}},{key:"getPage",value:function(e){var t=this;this.loadingStart();var a=/&page=\d+/.exec(e),n=a?parseInt(a[0].replace(/&page=/,""),10):1;fetch(e,{credentials:"same-origin"}).then(function(e){if(e.ok)return e.text();throw new Error("Looks like there was a problem. Status Code: "+e.status)}).then(function(e){t.loadingStop(),a&&t.setNextPage(n),t.renderNewPage(e)}).catch(function(e){t.loadingStop(),console.log("Fetch Error :-S",e)})}},{key:"setNextPage",value:function(e){this.page=e,this.renderNavigation()}},{key:"renderNewPage",value:function(e){switch(this.pagerData.type){case"load_more":this.container.insertAdjacentHTML("beforeend",e);break;default:this.container.innerHTML=e}this.el.dispatchEvent(new CustomEvent("ajax-paging-added",{bubbles:!0,cancelable:!0}))}},{key:"loadingStart",value:function(){this.el.classList.add("ajax-loading")}},{key:"loadingStop",value:function(){this.el.classList.remove("ajax-loading")}}]),t}();window.addEventListener("load",function(){var e=document.getElementsByClassName("ajax-collection");[].forEach.call(e,function(e){new r(e)})})},{}],2:[function(e,t,a){"use strict";e("./ajax-paging")},{"./ajax-paging":1}]},{},[2]);