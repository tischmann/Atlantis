import Atlantis from"/js/atlantis.js";const atlantis=new Atlantis;atlantis.on(window,"load",()=>{document.querySelectorAll("[data-atlantis-categories]").forEach(t=>{let a=t.dataset.token;new Sortable(t,{handle:".handle",animation:150,ghostClass:"bg-sky-200",onEnd:t=>{const e=[];t.target.querySelectorAll("li[data-id]").forEach((t,a)=>e.push(t.dataset.id)),atlantis.fetch("/categories/order",{headers:{"Content-Type":"application/json","X-Csrf-Token":a},body:e,success:t=>{a=t.token}})}})})});