(function(){
  const qs = (s, el=document)=>el.querySelector(s);
  const qsa = (s, el=document)=>Array.from(el.querySelectorAll(s));

  // Mobile right-drawer navigation
  (function(){
    const openBtn = qs('[data-drawer-open]');
    const drawer = qs('[data-drawer]');
    const overlay = qs('[data-drawer-overlay]');
    const closeBtn = qs('[data-drawer-close]');
    const links = qsa('[data-drawer-link]');

    if(!openBtn || !drawer || !overlay) return;

    const open = ()=>{
      document.body.classList.add('drawerOpen');
      drawer.setAttribute('aria-hidden','false');
      overlay.setAttribute('aria-hidden','false');
    };
    const close = ()=>{
      document.body.classList.remove('drawerOpen');
      drawer.setAttribute('aria-hidden','true');
      overlay.setAttribute('aria-hidden','true');
    };

    openBtn.addEventListener('click', open);
    overlay.addEventListener('click', close);
    if(closeBtn) closeBtn.addEventListener('click', close);
    links.forEach(a=>a.addEventListener('click', close));
    document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') close(); });
  })();

  function getBase(){
    const meta = qs('meta[name="app-base"]');
    const base = meta ? meta.getAttribute('content') : '';
    return (base || '').replace(/\/+$/,'');
  }

  // Weather (Dubai coords) - minimal + cache
  const weatherEl = qs('[data-weather]');
  async function loadWeather(){
    if(!weatherEl) return;
    try{
      const key = 'dsg_weather_cache_v1';
      const cached = JSON.parse(localStorage.getItem(key) || 'null');
      const now = Date.now();
      if(cached && (now - cached.ts) < 30*60*1000){
        weatherEl.innerHTML = cached.html;
        return;
      }
      const url = 'https://api.open-meteo.com/v1/forecast?latitude=25.2048&longitude=55.2708&current=temperature_2m,weather_code&forecast_days=1';
      const res = await fetch(url, {cache:'no-store'});
      const data = await res.json();
      const t = Math.round(data.current.temperature_2m);
      const html = `<div><div class="t">Dubai • Today</div><div class="v">${t}°C</div></div><div class="badge">Live</div>`;
      weatherEl.innerHTML = html;
      localStorage.setItem(key, JSON.stringify({ts:now, html}));
    }catch(e){}
  }
  loadWeather();

  // Toast "recently booked"
  const toast = qs('[data-toast]');
  const toastTitle = qs('[data-toast-title]');
  const toastSub = qs('[data-toast-sub]');
  const toastItemsEl = qs('[data-toast-items]');
  let toastItems = [];
  try{ toastItems = JSON.parse(toastItemsEl?.textContent || '[]'); }catch(e){}

  function showToast(item){
    if(!toast || !item) return;
    toastTitle.textContent = item.title || 'Recently booked';
    toastSub.textContent = item.subtitle || '';
    toast.style.display = 'block';
    setTimeout(()=>{ toast.style.display='none'; }, 2600);
  }

  function scheduleToast(){
    if(!toastItems.length) return;
    const item = toastItems[Math.floor(Math.random()*toastItems.length)];
    showToast(item);
    const next = 20000 + Math.floor(Math.random()*11000); // 20-31s
    setTimeout(scheduleToast, next);
  }
  setTimeout(scheduleToast, 5500);

  // Highlights carousel auto scroll (gentle)
  const car = qs('[data-carousel]');
  if(car){
    let dir = 1;
    setInterval(()=>{
      const max = car.scrollWidth - car.clientWidth;
      if(max <= 0) return;
      let next = car.scrollLeft + (dir*330);
      if(next >= max){ next = max; dir = -1; }
      if(next <= 0){ next = 0; dir = 1; }
      car.scrollTo({left: next, behavior: 'smooth'});
    }, 7000);
  }

  // Mobile-only layout re-order (does not affect desktop)
  (function(){
    const mq = window.matchMedia('(max-width: 980px)');
    const hero = qs('.hero');
    const packages = qs('#packages');
    const why = qs('#why');
    const lead = qs('#lead');
    if(!hero || !packages || !why || !lead) return;

    const home = {
      leadParent: lead.parentNode,
      leadNext: lead.nextSibling,
      packagesParent: packages.parentNode,
      packagesNext: packages.nextSibling,
    };

    const apply = ()=>{
      if(mq.matches){
        // After hero CTAs/pills, show packages; move booking form after "Why book with us" section.
        if(hero.nextElementSibling !== packages){
          hero.parentNode.insertBefore(packages, hero.nextElementSibling);
        }
        if(why.nextElementSibling !== lead){
          why.parentNode.insertBefore(lead, why.nextElementSibling);
        }
      } else {
        // Restore original positions
        if(home.packagesParent){
          home.packagesParent.insertBefore(packages, home.packagesNext);
        }
        if(home.leadParent){
          home.leadParent.insertBefore(lead, home.leadNext);
        }
      }
    };

    apply();

    // Re-apply after full load (mobile browsers sometimes delay layout)
    window.addEventListener('load', apply);
    setTimeout(apply, 80);

    if(mq.addEventListener) mq.addEventListener('change', apply);
    else mq.addListener(apply);
  })();

  // WhatsApp click tracking (timestamp, page, IP hash, user agent handled server-side)
  (function(){
    const base = getBase();
    const endpoint = (base ? base : '') + '/wa-track';

    function send(payload){
      try{
        const body = JSON.stringify(payload);
        if(navigator.sendBeacon){
          const blob = new Blob([body], {type:'application/json'});
          navigator.sendBeacon(endpoint, blob);
          return;
        }
        fetch(endpoint, {method:'POST', headers:{'Content-Type':'application/json'}, body, keepalive:true});
      }catch(e){}
    }

    function bind(){
      const links = qsa('a.js-wa, a[href*="wa.me/"]');
      links.forEach(a=>{
        if(a.dataset.waBound === '1') return;
        a.dataset.waBound = '1';
        a.addEventListener('click', ()=>{
          const explicit = a.getAttribute('data-wa-source');
          const inferred = a.closest('#packages') ? 'packages' : 'site';
          send({
            page: location.pathname + location.search,
            source: explicit ? explicit : inferred,
          });
        }, {passive:true});
      });
    }
    bind();
    // in case dynamic content is injected later
    document.addEventListener('DOMContentLoaded', bind);
  })();
})();
