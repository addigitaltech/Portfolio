(function(){
function el(t,x,c){var e=document.createElement(t);if(x!=null)e.textContent=x;if(c)e.className=c;return e}
function fmt(d){if(!d)return '';var t=new Date(String(d).replace(' ','T'));return isNaN(t)?'':t.toLocaleDateString('en-GB',{day:'numeric',month:'short',year:'numeric'})}
var OK=/^(api\/image\.php\?f=[a-f0-9]{32}\.(jpg|png|webp)|assets\/[A-Za-z0-9._-]+)$/;
function cover(p,alt){if(!p.cover_image||!OK.test(p.cover_image))return null;var i=document.createElement('img');i.src=p.cover_image;i.alt=alt||'';i.loading='lazy';return i}
function card(p,big){var a=el('a',null,'nc'+(big?' big':''));a.href='blog.html?s='+encodeURIComponent(p.slug);
var m=cover(p)||el('div',p.category,'nph');a.appendChild(m);var b=el('div',null,'nb');b.appendChild(el('span',p.category,'tag'));b.appendChild(el('h3',p.title));
b.appendChild(el('p',p.summary));b.appendChild(el('time',fmt(p.published_at)));a.appendChild(b);return a}
window.ADNews={el:el,fmt:fmt,card:card,cover:cover};
})();
