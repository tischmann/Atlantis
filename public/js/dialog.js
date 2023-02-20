class Dialog{constructor({title:t,message:e,buttons:a=[]}={}){this.title=t,this.message=e,this.buttons=a,this.id="dialog-"+(Math.random()+1).toString(36).substring(16),this.dialog=this.create(),document.body.appendChild(this.dialog)}show(){this.dialog.showModal()}create(){var t=document.createElement("dialog"),e=(t.id=this.id,t.classList.add("rounded","shadow-xl","fixed","w-96"),document.createElement("form")),a=(e.method="dialog",document.createElement("h5")),i=(a.classList.add("block","text-xl","font-medium","leading-normal","text-gray-800","pr-12","mb-4","truncate"),a.innerText=this.title,document.createElement("div"));i.classList.add("mb-4"),i.innerText=this.message;const s=document.createElement("div");s.classList.add("flex","items-center","gap-4");var n=document.createElement("button"),d=(n.classList.add("absolute","top-4","right-4","ring-0","focus:ring-0","outline-none","text-gray-500"),document.createElement("i"));return d.classList.add("fas","fa-times","text-xl"),n.value="cancel",n.appendChild(d),this.buttons.forEach(t=>{var e=document.createElement("button");e.classList.add("inline-block","w-full","px-6","py-2.5","bg-sky-600","text-white","font-medium","text-xs","leading-tight","uppercase","rounded","shadow-md","hover:bg-pink-700","hover:shadow-lg","focus:bg-pink-700","focus:shadow-lg","focus:outline-none","focus:ring-0","active:bg-pink-800","active:shadow-lg","transition","duration-150","ease-in-out"),e.innerText=t?.text||"Button",e.addEventListener("click",()=>{t?.callback(),this.dialog.close()}),s.appendChild(e)}),e.append(n,a,i,s),t.appendChild(e),t}}