document.addEventListener('DOMContentLoaded', () => {
  // ----------- Toggle Inputs (Cupom/Jogo) -----------
  document.querySelectorAll('.toggle-input').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.dataset.target;
      const targetPopup = document.getElementById(targetId);
      document.querySelectorAll('.input-popup').forEach(popup => {
        if (popup !== targetPopup) popup.classList.remove('show');
      });
      targetPopup.classList.toggle('show');
      if (targetPopup.classList.contains('show')) {
        const input = targetPopup.querySelector('input');
        input.focus();
      }
    });
  });
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.acao-form') && !e.target.closest('.input-popup')) {
      document.querySelectorAll('.input-popup').forEach(p => p.classList.remove('show'));
    }
  });

  // ----------- Mensagens temporárias -----------
  const mensagens = document.querySelectorAll('.mensagem-cupom, .mensagem-erro, .mensagem-sucesso');
  mensagens.forEach(msg => {
    msg.classList.add('show');
    setTimeout(() => msg.remove(), 2000);
  });

  // ----------- Modal Finalizar Compra -----------
  const modalPagamento = document.getElementById('modalPagamento');
  const btnFinalizar = document.getElementById('btnFinalizarCompra');
  const btnFecharModal = document.getElementById('fecharModalPagamento');
  const detalhesPagamento = document.getElementById('detalhesPagamento');

  btnFinalizar.onclick = () => { modalPagamento.style.display = 'flex'; };
  btnFecharModal.onclick = () => { modalPagamento.style.display = 'none'; };
  window.onclick = (e) => { if (e.target == modalPagamento) modalPagamento.style.display = 'none'; };

  // ----------- Seleção de método de pagamento -----------
  const botoesPagamento = document.querySelectorAll('.pagamento-btn');
  botoesPagamento.forEach(btn => {
    btn.addEventListener('click', () => {
      const metodo = btn.dataset.pagamento;
      detalhesPagamento.innerHTML = '';
      
      if (metodo === 'pix') {
        detalhesPagamento.innerHTML = `
          <div class="pix-container">
            <p>Escaneie o QR Code ou use a chave PIX:</p>
            <div id="pixQRCode"></div>
            <button id="btnConfirmarPix" class="botao">Confirmar Pagamento</button>
          </div>`;
        new QRCode(document.getElementById("pixQRCode"), { text: "CHAVE_PIX_AQUI", width: 300, height: 300 });
        document.getElementById('btnConfirmarPix').addEventListener('click', () => {
          modalPagamento.style.display = 'none';
          mostrarConfirmacao("Pagamento via PIX realizado com sucesso!", true);
          finalizarPedidoAJAX();
        });

      } else if (metodo === 'mastercard') {
        detalhesPagamento.innerHTML = `
          <form class="form-pagamento">
            <div class="campo-nome">
              <label for="nome_cartao">Nome no Cartão</label>
              <input id="nome_cartao" type="text" name="nome_cartao" placeholder="Nome completo" required>
            </div>
            <div class="linha-campos">
              <div class="campo numero-cartao">
                <label for="numero_cartao">Número do Cartão</label>
                <input id="numero_cartao" type="text" name="numero_cartao" placeholder="0000 0000 0000 0000" maxlength="19" required>
              </div>
              <div class="campo ccv">
                <label for="ccv">CCV</label>
                <input id="ccv" type="text" name="ccv" placeholder="123" maxlength="3" required>
              </div>
              <div class="campo validade">
                <label for="validade">Validade</label>
                <input id="validade" type="text" name="validade" placeholder="MM/AA" maxlength="5" required>
              </div>
            </div>
            <button type="submit">Pagar com Mastercard</button>
          </form>`;
        detalhesPagamento.querySelector('.form-pagamento').addEventListener('submit', (e) => {
          e.preventDefault();
          modalPagamento.style.display = 'none';
          mostrarConfirmacao("Pagamento com Mastercard realizado com sucesso!", true);
          finalizarPedidoAJAX();
        });

      } else if (metodo === 'paypal') {
        detalhesPagamento.innerHTML = `
          <p>Você será redirecionado para o PayPal:</p>
          <a href="#" class="botao-pagamento">Pagar com PayPal</a>`;
        detalhesPagamento.querySelector('.botao-pagamento').addEventListener('click', (e) => {
          e.preventDefault();
          modalPagamento.style.display = 'none';
          mostrarConfirmacao("Pagamento via PayPal realizado com sucesso!", true);
          finalizarPedidoAJAX();
        });
      }
    });
  });

  // ----------- Modal de Confirmação -----------
  const modalConfirmacao = document.getElementById('modalConfirmacao');
  const mensagemConfirmacao = document.getElementById('mensagemConfirmacao');
  document.getElementById('fecharModalConfirmacao').addEventListener('click', () => modalConfirmacao.style.display = 'none');
  document.getElementById('btnFecharConfirmacao').addEventListener('click', () => modalConfirmacao.style.display = 'none');

  function mostrarConfirmacao(mensagem, mostrarCheck = false) {
    if (mostrarCheck) {
      mensagemConfirmacao.innerHTML = `<span style="color:#75F94C; font-size:2rem; display:block; margin-bottom:10px;">✔</span>${mensagem}`;
    } else {
      mensagemConfirmacao.textContent = mensagem;
    }
    modalConfirmacao.style.display = 'flex';
  }

  // ----------- Finalizar Pedido via AJAX (única função) -----------
function finalizarPedidoAJAX() {
  fetch('Resumo_Pedido.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'finalizarPedido=1'
  })
  .then(res => res.text())
  .then(data => {
    console.log("Pedido finalizado:", data);

    // Mostrar apenas a mensagem de confirmação
    mostrarConfirmacao(data, true);

    // Esconder todo o container de jogos/resumo
    const container = document.querySelector('.container-jogos');
    if (container) container.style.display = 'none';

    // Opcional: esconder também mensagens de cupom ou botões
    const resumoTotal = document.querySelector('.resumo-total');
    if (resumoTotal) resumoTotal.style.display = 'none';
  })
  .catch(err => console.error("Erro ao finalizar pedido:", err));
}

}); // fim DOMContentLoaded
