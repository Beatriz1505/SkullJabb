(function(){
  const cfg = window.PERFIL_CONFIG || {};
  const perfilId = Number(cfg.perfilId || 0);
  const meuId    = Number(cfg.meuId    || 0);
  const base     = String(cfg.basePath || '../Perfil - User/');

  function $(sel){ return document.querySelector(sel); }
  function $all(sel){ return Array.from(document.querySelectorAll(sel)); }

  function safeJson(text){
    try { return JSON.parse(text); }
    catch(e){ return null; }
  }

  (function(){
    const abrir = $('#abrir-popup');
    const popup = $('#popup-editar');
    const fechar = $('#fechar-popup');
    const foto = $('#foto-upload');
    const preview = $('#preview');

    if (abrir && popup) abrir.addEventListener('click', e => { e.preventDefault(); popup.style.display = 'flex'; });
    if (fechar && popup) fechar.addEventListener('click', e => { e.preventDefault(); popup.style.display = 'none'; });

    document.addEventListener('click', ev => { if (popup && ev.target === popup) popup.style.display = 'none'; });
    document.addEventListener('keydown', ev => { if (ev.key === 'Escape' && popup) popup.style.display = 'none'; });

    if (foto && preview) {
      foto.addEventListener('change', function(){
        const f = this.files && this.files[0];
        if (!f) return;
        preview.src = URL.createObjectURL(f);
      });
    }

    $all('.molduras label').forEach(label=>{
      label.addEventListener('click', ()=> {
        $all('.molduras label').forEach(l=> l.classList.remove('ativa'));
        label.classList.add('ativa');
      });
    });
  })();

  (function(){
    const abrirBtn = $('#abrir-solicitacoes');
    const popup = $('#popup-solicitacoes');
    const fecharBtn = $('#fechar-solicitacoes');
    const requestsList = $('#requests-list');
    const badge = $('.badge-solicitacoes') || $('#ps-count');

    if (!popup || !requestsList) {
      return;
    }

    function openPopup(){
      popup.style.display = 'flex';
      popup.setAttribute('aria-hidden','false');
      const focusable = popup.querySelector('.popup-content, .ps-conteudo') || popup;
      if (focusable) focusable.focus && focusable.focus();
    }
    function closePopup(){
      popup.style.display = 'none';
      popup.setAttribute('aria-hidden','true');
    }

    abrirBtn && abrirBtn.addEventListener('click', function(e){
      e.preventDefault();
      loadSolicitacoes().then(()=> openPopup()).catch(()=> openPopup());
    });

    fecharBtn && fecharBtn.addEventListener('click', function(e){ e.preventDefault(); closePopup(); });
    popup.addEventListener('click', function(e){ if (e.target === popup) closePopup(); });
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && popup.style.display === 'flex') closePopup(); });

    requestsList.addEventListener('click', function(e){
      const bt = e.target;
      if (!bt) return;
      if (bt.classList.contains('btn-accept') || bt.classList.contains('btn-decline')) {
        const id = bt.dataset.id;
        const acao = bt.classList.contains('btn-accept') ? 'aceitar' : 'recusar';
        if (!id) return;

        if (!confirm((acao === 'aceitar' ? 'Aceitar' : 'Recusar') + ' esta solicitação?')) return;
        bt.disabled = true;
        bt.textContent = (acao === 'aceitar') ? 'Aceitando...' : 'Recusando...';

        fetch(base + 'responder_solicitacao.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
          body: 'id_solicitacao=' + encodeURIComponent(id) + '&acao=' + encodeURIComponent(acao)
        })
        .then(r => r.text())
        .then(txt => {
          const json = safeJson(txt);
          if (json && json.ok) {
            const item = document.getElementById('req-' + id) || document.getElementById('sol-' + id);
            if (item) item.remove();

            if (badge) {
              let val = parseInt(badge.textContent || badge.innerText || '0') || 0;
              val = Math.max(0, val - 1);
              if (badge.tagName === 'SPAN' || badge.tagName === 'DIV') badge.textContent = val;
              else badge.innerText = val;
            }
            if (acao === 'aceitar') setTimeout(()=> location.reload(), 250);
            if (requestsList.children.length === 0) {
              requestsList.innerHTML = '<p>Nenhuma solicitação pendente.</p>';
            }
          } else {
            alert((json && json.msg) ? json.msg : 'Erro ao processar.');
            bt.disabled = false;
            bt.textContent = (acao === 'aceitar') ? 'Aceitar' : 'Recusar';
          }
        })
        .catch(err => {
          console.error(err);
          alert('Erro de rede.');
          bt.disabled = false;
          bt.textContent = (acao === 'aceitar') ? 'Aceitar' : 'Recusar';
        });
      }
    });

async function loadSolicitacoes(){
  try {
    // Chama sem parâmetro id_usuario para obter a lista de solicitações
    const resp = await fetch(base + 'obter_estado_relacao.php', { credentials: 'same-origin' });
    const txt = await resp.text();
    const json = safeJson(txt) || [];
    
    // Verifica se é o formato de array (solicitações) ou objeto (estado)
    let solicitacoes = Array.isArray(json) ? json : [];

    if (solicitacoes.length === 0) {
      requestsList.innerHTML = '<p>Nenhuma solicitação pendente.</p>';
      if (badge) badge.textContent = '0';
      return;
    }

    const html = solicitacoes.map(s => {
      const id = Number(s.id || s.id_solicitante || 0);
      const nome = escapeHtml(s.nome || s.usuario || 'Usuário');
      const foto = escapeHtml(s.foto || '../../Img/Elementos/user.png');
      const data = escapeHtml(s.data_envio || s.data_solicitacao || '');
      return `<div id="req-${id}" class="request-item ps-item" style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.03);">
                <img src="${foto}" alt="avatar" style="width:48px;height:48px;border-radius:50%;object-fit:cover;margin-right:10px;">
                <div style="flex:1;"><strong>${nome}</strong><br><small style="opacity:.85">${data}</small></div>
                <div style="display:flex;gap:6px;">
                  <button class="btn-accept" data-id="${id}">Aceitar</button>
                  <button class="btn-decline" data-id="${id}">Recusar</button>
                </div>
              </div>`;
    }).join('');
    requestsList.innerHTML = html;
    if (badge) badge.textContent = String(solicitacoes.length);
  } catch(e){
    console.warn('Não foi possível carregar solicitações:', e);
  }
}

    function escapeHtml(str){
      if (typeof str !== 'string') return '';
      return str.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    }
  })();

  (function(){
    const btn = document.getElementById('btn-friend');
    if (!btn) return;

    async function obterEstado(){
      try {
        const res = await fetch(base + 'obter_estado_relacao.php?id_usuario=' + encodeURIComponent(perfilId), {credentials:'same-origin'});
        const txt = await res.text();
        const j = safeJson(txt) || {};
        return (j.estado || j.status || j.estado_relacao || j.estado_relacao || 'nenhum').toString();
      } catch(e) {
        console.error('erro obter estado', e);
        return 'nenhum';
      }
    }

async function atualizarBotao(){
  try {
    btn.style.display = 'inline-block';
    btn.disabled = true;
    btn.className = '';
    btn.textContent = 'Carregando...';

    // Usa o mesmo endpoint, mas agora com parâmetro id_usuario
    const resp = await fetch(base + 'obter_estado_relacao.php?id_usuario=' + encodeURIComponent(perfilId), {
      credentials: 'same-origin'
    });
    
    if (!resp.ok) throw new Error('Erro na requisição');
    
    const data = await resp.json();
    
    // Verifica se a resposta é o novo formato (com estado) ou o formato antigo (array)
    let estado;
    if (data && typeof data === 'object' && !Array.isArray(data) && data.estado) {
      // Novo formato: {estado: 'amigos'}
      estado = data.estado;
    } else {
      // Formato antigo (array) - fallback para verificação manual
      estado = 'nenhum';
      console.warn('Resposta em formato antigo, usando fallback');
    }
    
    console.log('Estado da relação:', estado);

    btn.disabled = false;
    btn.className = '';

    // Lógica corrigida para os estados
    if (estado === 'amigos') {
      btn.textContent = 'Remover';
      btn.dataset.action = 'remover';
      btn.classList.add('btn-remove');
    } else if (estado === 'pendente_enviado') {
      btn.textContent = 'Solicitação enviada';
      btn.dataset.action = 'nenhum';
      btn.classList.add('btn-disabled');
      btn.disabled = true;
    } else if (estado === 'pendente_recebido') {
      btn.textContent = 'Responder solicitação';
      btn.dataset.action = 'responder';
      btn.classList.add('btn-disabled');
      btn.disabled = true;
    } else {
      btn.textContent = '+ Adicionar';
      btn.dataset.action = 'adicionar';
      btn.classList.add('btn-add');
    }
  } catch(err) {
    console.error('Erro ao atualizar botão:', err);
    // Fallback seguro
    btn.style.display = 'inline-block';
    btn.className = '';
    btn.textContent = '+ Adicionar';
    btn.dataset.action = 'adicionar';
    btn.classList.add('btn-add');
    btn.disabled = false;
  }
}


    async function enviarSolicitacao(){
      const resp = await fetch(base + 'enviar_solicitacao.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: 'id_destinatario=' + encodeURIComponent(perfilId),
        credentials:'same-origin'
      });
      const txt = await resp.text();
      return safeJson(txt) || {};
    }

    async function removerAmizade(){
      const resp = await fetch(base + 'remover_amizade.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: 'id_outro=' + encodeURIComponent(perfilId),
        credentials:'same-origin'
      });
      const txt = await resp.text();
      return safeJson(txt) || {};
    }

    btn.addEventListener('click', async function(){
      const action = btn.dataset.action;
      if (action === 'adicionar') {
        btn.disabled = true;
        const res = await enviarSolicitacao().catch(()=>({ok:false,msg:'Erro de rede'}));
        alert(res.msg || (res.ok ? 'Solicitação enviada' : 'Erro'));
        await atualizarBotao();
        btn.disabled = false;
      } else if (action === 'remover') {
        if (!confirm('Remover amizade?')) return;
        btn.disabled = true;
        const res = await removerAmizade().catch(()=>({ok:false,msg:'Erro de rede'}));
        alert(res.msg || (res.ok ? 'Amizade removida' : 'Não foi possível remover'));
        await atualizarBotao();
        setTimeout(()=> location.reload(), 600);
        btn.disabled = false;
      } else if (action === 'responder') {
        window.location.href = 'perfil.php';
      }
    });

    document.addEventListener('DOMContentLoaded', function(){ setTimeout(atualizarBotao, 120); });
  })();

})();
