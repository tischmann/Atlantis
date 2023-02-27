export default class Atlantis{csrf_token="undefined";#handlers=new Map;#removeEventListeners(t){return t instanceof NodeList&&t.forEach(t=>{1==t.nodeType&&(this.off(t),this.#removeEventListeners(t.childNodes))}),this}constructor({log:t=!1,csrf_token:e}={}){this.log=t,this.uuid=this.getUUID()||this.setUUID(),this.setCsrfToken(e),new MutationObserver(t=>{this.#removeEventListeners(t[0]?.removedNodes)}).observe(document,{childList:!0,subtree:!0})}on(t,e,n,s=!1){var o=this.#handlers.get(e)||new Set;return t.addEventListener(e,n,s),o.add({element:t,handler:n,capture:s}),this.#handlers.set(e,o),this}off(t,e=void 0,n=void 0){for(var[s,o]of this.#handlers)if(s===e){for(const r of o)if(r.handler===n&&(t.removeEventListener(s,r.handler,r.capture),o.delete(r),n))return this;if(e)return this}return this}tag(t,{className:e=null,classList:n=[],css:s={},data:o={},attr:r={},text:i=null,html:a=null,append:c=[],on:h={}}={}){const l=document.createElement(t);return e&&(l.className=e),n.length&&l.classList.add(...n),s&&this.css(l,s),o&&this.data(l,o),r&&this.attr(l,r),i?l.textContent=i:a&&(l.innerHTML=a),c?.length&&l.append(...c),Object.entries(h).forEach(([t,e])=>{this.on(l,t,e)}),l}css(n,t={}){return t instanceof Object&&Object.entries(t).forEach(([t,e])=>{n.style[t]=e}),this}data(n,t={}){return Object.entries(t).forEach(([t,e])=>{n.dataset[t]=e}),this}attr(n,t={}){return Object.entries(t).forEach(([t,e])=>{n.setAttribute(t,e)}),this}handleEvent(t){"change"===t.type&&this.setArticleRating(t.target.closest("form[data-id]").dataset.id,t.target.value)}getCsrfToken(){return this.csrf_token}setCsrfToken(t){this.csrf_token=t}fetch(t,{method:e="POST",headers:n={"Content-Type":"application/json",Accept:"application/json"},body:s=void 0,success:o=function(){},failure:r=function(){}}={}){"string"!=typeof s&&(s=JSON.stringify(s)),n={...n,"Content-Length":s.length.toString()},["POST","PUT","DELETE"].includes(e.toUpperCase())&&(n={...n,"X-Csrf-Token":this.getCsrfToken()}),fetch(t,{method:e,headers:{...n,"X-Csrf-Token":this.getCsrfToken(),"Content-Length":s.length.toString()},body:s}).then(t=>{if(!t.ok)return r(t.statusText),console.error("Atlantis.fetch():",t.statusText);switch(t.headers.get("Content-Type")){case"application/json":t.json().then(t=>{this.log&&console.log("Atlantis.fetch():",t),this.setCsrfToken(t?.csrf),o(t)}).catch(t=>{r(t),console.error("Atlantis.fetch():",t)});break;case"text/html":t.text().then(t=>{this.log&&console.log("Atlantis.fetch():",t),o(t)}).catch(t=>{r(t),console.error("Atlantis.fetch():",t)})}}).catch(t=>{r(t),console.error("Atlantis.fetch():",t)})}uniqueid(){return self.crypto.randomUUID()}toInt(t){return parseInt(t,10)}getCookie(t){t=document.cookie.match(new RegExp(`(?:^|; )${t.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g,"\\$1")}=([^;]*)`));return t?decodeURIComponent(t[1]):void 0}setCookie(t,e,n={}){n={path:"/",secure:!0,domain:window.location.hostname,samesite:"strict",expires:new Date(Date.now()+121e7).toUTCString(),...n};let s=encodeURIComponent(t)+"="+encodeURIComponent(e);Object.entries(n).forEach(([t,e])=>{s+=`; ${t}=`+(e||"")}),document.cookie=s}getUUID(t="atlantis_uuid"){return this.getCookie(t)}setUUID(t="atlantis_uuid"){this.setCookie(t,self.crypto.randomUUID())}}