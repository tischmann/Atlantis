import Atlantis from"/js/atlantis.js";const atlantis=new Atlantis;document.querySelectorAll(".atlantis-delete-button").forEach(t=>{const s=t.dataset,e=atlantis.dialog({title:s.title,message:s.message,buttons:[{text:'<i class="fas fa-times text-xl"></i>',class:"bg-sky-800 text-white hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-700"},{text:'<i class="fas fa-check text-xl"></i>',class:"bg-pink-600 text-white hover:bg-pink-500 focus:bg-pink-500 active:bg-pink-500",callback:()=>{atlantis.fetch(s.url,{method:"DELETE",headers:{"Content-Type":"application/json","X-Csrf-Token":s.token},success:()=>{window.location.assign(s.redirect)}})}}]});atlantis.on(t,"click",()=>{e.show()})});