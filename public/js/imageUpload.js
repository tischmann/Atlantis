import Atlantis from"/js/atlantis.js";const atlantis=new Atlantis;document.querySelectorAll("img[data-atlantis-image-load]").forEach(e=>{const t=e.parentElement.querySelector('input[type="file"][data-atlantis-image-load]'),l=e.parentElement.querySelector('input[type="hidden"][data-atlantis-image-load]');var a=e.parentElement.querySelector("button[data-atlantis-image-load]");let n=e.dataset.token;atlantis.on(t,"change",t=>{var a,t=t.target.files[0];t&&((a=new FormData).append("width",e.getAttribute("width")),a.append("height",e.getAttribute("height")),a.append("file",t,t.name),atlantis.fetch(e.dataset.url,{headers:{"X-Csrf-Token":n},body:a,success:t=>{l.value=t.image,e.src=t.location,n=t.token},failure:t=>{atlantis.dialog({message:t,onclose:()=>window.location.reload()}).show()}}))}),atlantis.on(e,"click",()=>{t.click()}),atlantis.on(a,"click",()=>{atlantis.attr(e,{src:e.dataset?.placeholder||"/images/placeholder.svg"}),l.value=""})});