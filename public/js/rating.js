class Rating{constructor(t,e){this.form=t,this.uuid=e,this.id=this.form.dataset.id,this.rating=this.form.dataset.rating,this.uniqueid=self.crypto.randomUUID();for(let t=5;1<=t;t--){var i=document.createElement("input"),n=document.createElement("label");i.type="radio",i.id=`star-${t}-`+this.uniqueid,n.setAttribute("for",i.id),i.name="rating",i.value=t,this.rating==t&&(i.checked=!0),i.addEventListener("change",this),this.form.append(i,n)}}handleEvent(t){"change"===t.type&&this.change(t)}change(t){this.rating=t.target.value;let e;t=JSON.stringify({uuid:this.uuid});fetch(`/rating/${this.id}/`+this.rating,{method:"POST",headers:{"Content-Type":"application/json",Accept:"application/json","X-Csrf-Token":"{{csrf-token}}","Content-Length":t.length.toString()},body:t}).then(t=>t.json().then(t=>{t?.status?e=t.csrf:(alert(t?.message),console.error("Rating:",t?.message))}).catch(t=>{alert(t),console.error("Rating:",t)})).catch(t=>{alert(t),console.error("Rating:",t)})}}