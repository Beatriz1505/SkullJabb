<?php  
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();


if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Login/Login.php");
    exit;
}

require_once "../Conexao/Conexao.php";
$conn = Conexao::getConexao();
if (!$conn) { 
    die("Erro: sem conexão ao banco"); 
}


if (!isset($_SESSION['jogos_adicionados'])) {
    $_SESSION['jogos_adicionados'] = [];
}
if (!isset($_SESSION['cupom'])) {
    $_SESSION['cupom'] = null;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionarJogo'])) {
    $nomeJogo = trim($_POST['jogo']);
    if (!empty($nomeJogo)) {
        $sqlJogo = "SELECT * FROM jogo WHERE nome LIKE ? LIMIT 1";
        $stmtJogo = $conn->prepare($sqlJogo);
        $nomeBusca = "%$nomeJogo%";
        $stmtJogo->bind_param("s", $nomeBusca);
        $stmtJogo->execute();
        $resultJogo = $stmtJogo->get_result();

        if ($resultJogo->num_rows > 0) {
            $jogo = $resultJogo->fetch_assoc();

            // Evitar duplicação na sessão
            $found = false;
            foreach ($_SESSION['jogos_adicionados'] as &$item) {
                if ($item['produto_id'] == $jogo['ID_jogo']) {
                    $item['quantidade'] += 1;
                    $found = true;
                    break;
                }
            }
            unset($item);

            if (!$found) {
                $_SESSION['jogos_adicionados'][] = [
                    'produto_id' => $jogo['ID_jogo'],
                    'nome' => $jogo['nome'],
                    'preco' => (float)$jogo['preco'],
                    'desconto' => (float)$jogo['desconto'],
                    'Img' => $jogo['Img'],
                    'quantidade' => 1,
                ];
            }
        }
        $stmtJogo->close();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aplicarCupom'])) {
    $cupomCodigo = trim($_POST['cupom']);
    $sqlCupom = "SELECT * FROM cupom WHERE codigo = ? AND ativo = 1 AND validade >= CURDATE() LIMIT 1";
    $stmtCupom = $conn->prepare($sqlCupom);
    $stmtCupom->bind_param("s", $cupomCodigo);
    $stmtCupom->execute();
    $resultCupom = $stmtCupom->get_result();

    $_SESSION['cupom'] = ($resultCupom->num_rows > 0) ? $resultCupom->fetch_assoc() : null;
    $stmtCupom->close();
}


$usuario_id = (int)$_SESSION['usuario_id'];
require_once "../Perfil/ClasseModelagemPerfil.php";
$perfil = Perfil::buscarPorId($usuario_id);


$sql = "SELECT c.ID_carrinho AS cart_id, c.quantidade, j.ID_jogo AS produto_id, j.nome, j.preco, j.desconto, j.Img
        FROM carrinho c
        INNER JOIN jogo j ON c.ID_jogo = j.ID_jogo
        WHERE c.ID_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$itens = [];
while ($row = $result->fetch_assoc()) {
    $preco = (float)$row['preco'];
    $desconto = (float)($row['desconto'] ?? 0);
    $preco_final = ($desconto > 0 && $desconto <= 100) ? 
        $preco - ($preco * ($desconto / 100.0)) : 
        max(0, $preco - $desconto);

    $row['preco_final'] = $preco_final;
    $itens[$row['produto_id']] = $row;
}
$stmt->close();

// MERGE ITENS DA SESSÃO (sem duplicar ao atualizar)
if (!empty($_SESSION['jogos_adicionados'])) {
    foreach ($_SESSION['jogos_adicionados'] as $item) {
        $produtoId = $item['produto_id'];

        // Se o produto JÁ existe no banco, não precisa somar de novo
        if (!isset($itens[$produtoId])) {
            $preco = (float)$item['preco'];
            $desconto = (float)($item['desconto'] ?? 0);
            $preco_final = ($desconto > 0 && $desconto <= 100)
                ? $preco - ($preco * ($desconto / 100.0))
                : max(0, $preco - $desconto);

            $itens[$produtoId] = [
                'produto_id' => $produtoId,
                'nome' => $item['nome'],
                'preco' => $preco,
                'desconto' => $desconto,
                'Img' => $item['Img'],
                'quantidade' => (int)$item['quantidade'],
                'preco_final' => $preco_final
            ];
        }
    }

  
    unset($_SESSION['jogos_adicionados']);
}


$subtotal = 0;
foreach ($itens as $item) {
    $subtotal += $item['quantidade'] * $item['preco_final'];
}

$descontoCupom = 0;
if (!empty($_SESSION['cupom'])) {
    $cupom = $_SESSION['cupom'];
    $descontoCupom = ($cupom['tipo'] === 'percentual') ? 
        ($cupom['desconto'] / 100.0) * $subtotal : 
        (float)$cupom['desconto'];
}

$totalComDesconto = max(0, $subtotal - $descontoCupom);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['metodo_pagamento'])) {
    $metodo_pagamento = $_POST['metodo_pagamento'];
    $total = $totalComDesconto;

    $conn->begin_transaction();

    try {
       
        $stmt_pedido = $conn->prepare("INSERT INTO resumo_pedido (ID_cliente, data_pedido, status, metodo_pagamento, total)
                                       VALUES (?, NOW(), 'pendente', ?, ?)");
        $stmt_pedido->bind_param("isd", $usuario_id, $metodo_pagamento, $total);
        $stmt_pedido->execute();
        $pedido_id = $stmt_pedido->insert_id;
        $stmt_pedido->close();

     
        $stmt_item = $conn->prepare("INSERT INTO pedido_item (ID_pedido, ID_jogo, quantidade) VALUES (?, ?, ?)");
        foreach ($itens as $item) {
            $stmt_item->bind_param("iii", $pedido_id, $item['produto_id'], $item['quantidade']);
            $stmt_item->execute();
        }
        $stmt_item->close();

       
        $stmt_clear = $conn->prepare("DELETE FROM carrinho WHERE ID_cliente = ?");
        $stmt_clear->bind_param("i", $usuario_id);
        $stmt_clear->execute();
        $stmt_clear->close();

        unset($_SESSION['cupom']);
        unset($_SESSION['jogos_adicionados']);

        $conn->commit();

        echo json_encode(["status" => "ok"]);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "erro", "msg" => $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resumo Pedido</title>

<link rel="shortcut icon" href="../../Img/Elementos/Logo SJ.png" sizes="64x64" type="image/x-icon">
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<link rel="shortcut icon" href="../../Img/Elementos/Logo SJ.png"sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="Resumo_Pedido.css?v=<?= time() ?>">

</head>

<body>

<div class="content">

    <header class="navbar">
            <div class="left-side">
                <div class="logo">
                    <a href="../Home/home.php"><img src="../../Img/Elementos/Logo SJ.png" alt="Caveira branca com capuz azul"></a>
                    <a class="lin" href=""><span>SKULL<br>JABB</span></a>
                </div>

                <div class="search">
                    <input type="text" placeholder="Procurar...">
                    <a href="#"><i class="mdi mdi-magnify search-icon"></i></a>
                </div>
            </div>

            <nav class="nav-links">
                <a class="grif" href="../Home/Home.php">Home</a> 
                <a href="../Loja/loja.php">Loja</a>
                <a href="../Suporte/Suporte.php">Suporte</a> 
            </nav>

            <div class="icons">
                <a href="../Carrinho/Carrinho.php"><i class="mdi mdi-cart icone"></i></a> <!-- CORRIGI O LINK -->
                <div class="profile">
                    <a href="../Perfil/Perfil.php">
                        <img src="<?= $perfil->foto ? $perfil->foto : '../../Img/Elementos/user.png' ?>" alt="Perfil">
                    </a>
                </div>
            </div>
        </header>

  </div>

  <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
          <div class="vw-plugin-top-wrapper"></div>
        </div>
      </div>
     <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
      <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
      </script>

<div class="container-jogos">
<section class="info-01">
  <h2>RESUMO DO PEDIDO</h2> 
</section>

<div class="container-pedido">
  <div class="itens-container">
    <?php if (count($itens) > 0): ?>
       <br/>
      <?php foreach($itens as $item): ?>
      
        <div class="item-jogo">
           
            <img src="<?= htmlspecialchars($item['Img']) ?>" alt="<?= htmlspecialchars($item['nome']) ?>">
            <div class="item-info">
                <p class="nome"><?= htmlspecialchars($item['nome']) ?></p>
                <p class="preco">R$<?= number_format($item['preco_final'],2,',','.') ?></p>
                <?php if($item['desconto'] > 0): ?>
                    <p class="desconto">-<?= $item['desconto'] ?>%</p>
                <?php endif; ?>
            </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Seu carrinho está vazio.</p>
    <?php endif; ?>
  </div>

   <div class="Linha"></div>


  <div class="Infos">
    <div class="acoes-pedido-wrapper">
        <div class="acoes-pedido">
            <form method="POST" action="" class="acao-form">
                <button type="button" class="toggle-input" data-target="cupomInput" title="Aplicar cupom">
                    <span class="mdi mdi-ticket-percent-outline"></span>
                </button>
                <div class="input-popup" id="cupomInput">
                    <input class="box-text" type="text" name="cupom" placeholder="Cupom" required>
                    <button class="botao" type="submit" name="aplicarCupom">
                        <span class="mdi mdi-chevron-right"></span>
                    </button>
                </div>
            </form>

            <form method="POST" action="" class="acao-form">
                <button type="button" class="toggle-input" data-target="jogoInput" title="Adicionar jogo">
                    <span class="mdi mdi-loupe"></span>
                </button>
                <div class="input-popup" id="jogoInput">
                    <input class="box-text" type="text" name="jogo" placeholder="Jogo" required>
                    <button class="botao" type="submit" name="adicionarJogo">
                        <span class="mdi mdi-chevron-right"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>


   <div class="resumo-total">
    <p><strong>Subtotal:</strong> R$ <?= number_format($subtotal, 2, ',', '.') ?></p>

    <?php if ($descontoCupom > 0): ?>
        <p><strong>Cupom:</strong> (− R$ <?= number_format($descontoCupom,2,',','.') ?>)</p>
    <?php elseif (!empty($mensagemCupom)): ?>
        <p style="color:red;"><?= htmlspecialchars($mensagemCupom) ?></p>
    <?php endif; ?>

    <div class="Linha"></div>
    <p class="total-text"><strong>Total:</strong> R$ <?= number_format($totalComDesconto, 2, ',', '.') ?></p>

    <button id="btnFinalizarCompra" class="botao-finalizar">Finalizar Compra</button>
</div>

  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleButtons = document.querySelectorAll('.toggle-input');

    toggleButtons.forEach(button => {
        const targetId = button.getAttribute('data-target');
        const popup = document.getElementById(targetId);

        // Ao clicar no botão, mostra ou esconde o popup
        button.addEventListener('click', (e) => {
            e.stopPropagation(); // evita que o clique feche imediatamente
            const isShown = popup.classList.contains('show');

            // Fecha outros popups abertos
            document.querySelectorAll('.input-popup.show').forEach(p => {
                p.classList.remove('show');
            });

            // Abre ou fecha o popup clicado
            if (!isShown) popup.classList.add('show');
        });

        // Impede que clicar dentro do popup feche ele
        popup.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

    // Fecha o popup se clicar fora de qualquer popup
    document.addEventListener('click', () => {
        document.querySelectorAll('.input-popup.show').forEach(p => {
            p.classList.remove('show');
        });
    });
});
button.addEventListener('click', (e) => {
    e.stopPropagation();
    const isShown = popup.classList.contains('show');

    // fecha outros popups
    document.querySelectorAll('.input-popup.show').forEach(p => p.classList.remove('show'));

    if (!isShown) popup.classList.add('show');
});

</script>




<!-- Modal de Pagamento -->
<div id="modalPagamento" class="modal">
  <div class="modal-conteudo">
    <span id="fecharModalPagamento" class="fechar">&times;</span>
    <h2>Escolha a forma de pagamento</h2>

    <!-- Aqui vai aparecer a opção selecionada -->
    <div id="opcaoSelecionada">
      <div id="pixContainer" class="pagamento-opcao" style="display:none;">
        <p>Escaneie o QR Code ou use a chave PIX:</p>
        <div id="pixQRCode"></div>
        <p>Chave PIX: <strong>123.456.789-00</strong></p>
      </div>

      <div id="mastercardContainer" class="pagamento-opcao" style="display:none;">
        <form class="form-pagamento" id="formMastercard">
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
        </form>
      </div>

      <div id="paypalContainer" class="pagamento-opcao" style="display:none;">
        <p>Você será redirecionado para o PayPal:</p>
        <a href="#" class="botao-pagamento" id="btnPayPal">Pagar com PayPal</a>
      </div>
    </div>

    <!-- Botões de pagamento abaixo -->
    <div class="opcoes-pagamento">
      <button class="pagamento-btn" data-pagamento="pix">
        <img src="../../Img/Pagamentos/Pagamento via Pix.webp" alt="PIX" class="img-pagamento">
      </button>
      <button class="pagamento-btn" data-pagamento="mastercard">
        <img src="../../Img/Pagamentos/Pagameto via MasterCard.png" alt="Mastercard" class="img-pagamento">
      </button>
      <button class="pagamento-btn" data-pagamento="paypal">
        <img src="../../Img/Pagamentos/Pagamento via Paypal.webp" alt="PayPal" class="img-pagamento">
      </button>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalPagamento');
    const fechar = document.getElementById('fecharModalPagamento');

    const pixContainer = document.getElementById('pixContainer');
    const mastercardContainer = document.getElementById('mastercardContainer');
    const paypalContainer = document.getElementById('paypalContainer');

    // Botões principais
   document.querySelectorAll('.pagamento-btn').forEach(btn => {
    btn.onclick = () => {
        const metodo = btn.getAttribute('data-pagamento');

        // Esconde todas opções
        pixContainer.style.display = 'none';
        mastercardContainer.style.display = 'none';
        paypalContainer.style.display = 'none';

        if (metodo === 'pix') {
            pixContainer.style.display = 'block';
            
            // Remove QRCode antigo se existir
            const qrDiv = document.getElementById("pixQRCode");
            qrDiv.innerHTML = "";

            // Gera QR Code
            new QRCode(qrDiv, {
                text: "00020126580014BR.GOV.BCB.PIX0114+55119999999990214Teste PIX52040000530398654041.005802BR5909Cliente6009Sao Paulo61080540900062070503***6304B14F",
                width: 200,
                height: 200
            });

        } else if (metodo === 'mastercard') {
            mastercardContainer.style.display = 'block';
        } else if (metodo === 'paypal') {
            paypalContainer.style.display = 'block';
        }
    };
});

    // Fechar modal
    fechar.onclick = () => modal.style.display = 'none';

    // Mastercard submit
    document.getElementById('formMastercard').onsubmit = async (e) => {
        e.preventDefault();
        // Aqui simula envio e finaliza pedido
        const formData = new FormData(e.target);
        formData.append('metodo_pagamento', 'mastercard');

        const response = await fetch('', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.status === 'ok') {
            modal.style.display = 'none';
            document.getElementById('modalConfirmacao').style.display = 'block';
        } else {
            alert('Erro ao confirmar o pedido.');
        }
    };

    // PayPal click
    document.getElementById('btnPayPal').onclick = async (e) => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('metodo_pagamento', 'paypal');

        const response = await fetch('', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.status === 'ok') {
            modal.style.display = 'none';
            document.getElementById('modalConfirmacao').style.display = 'block';
        } else {
            alert('Erro ao confirmar o pedido.');
        }
    };
});

const botoes = document.querySelectorAll('.pagamento-btn');
const opcoes = document.querySelectorAll('.pagamento-opcao');

botoes.forEach(btn => {
  btn.addEventListener('click', () => {
    const tipo = btn.dataset.pagamento;

    opcoes.forEach(opc => {
      if(opc.id === tipo + 'Container') {
        opc.style.display = 'block';
      } else {
        opc.style.display = 'none';
      }
    });
  });
});

</script>


<!-- Modal Confirmação -->
<div id="modalConfirmacao" class="modal" style="display:none;">
  <div class="modal-conteudo">
    <span id="fecharModalConfirmacao" class="fechar">&times;</span>
    <p id="mensagemConfirmacao">Pedido confirmado com sucesso!</p>
    <button id="btnFecharConfirmacao">Fechar</button>
  </div>
</div>
<br/>
</div>



<script>
// Exibir modal de pagamento
document.getElementById('btnFinalizarCompra').onclick = () => {
  document.getElementById('modalPagamento').style.display = 'block';
};

// Fechar modais
document.getElementById('fecharModalPagamento').onclick = () => {
  document.getElementById('modalPagamento').style.display = 'none';
};
document.getElementById('fecharModalConfirmacao').onclick = () => {
  document.getElementById('modalConfirmacao').style.display = 'none';
};
document.getElementById('btnFecharConfirmacao').onclick = () => {
  document.getElementById('modalConfirmacao').style.display = 'none';
  window.location.href = '../Loja/Loja.php';
};

// Enviar pagamento via AJAX
document.querySelectorAll('.pagamento-btn').forEach(btn => {
  btn.onclick = async () => {
    const metodo = btn.getAttribute('data-pagamento');
    const formData = new FormData();
    formData.append('metodo_pagamento', metodo);

    const response = await fetch('', { method: 'POST', body: formData });
    const result = await response.json();

    if (result.status === 'ok') {
      document.getElementById('modalPagamento').style.display = 'none';
      document.getElementById('modalConfirmacao').style.display = 'block';
    } else {
      alert('Erro ao confirmar o pedido.');
    }
  };
});
</script>
<footer>
  <div class="social-icons">
    <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
    <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
    <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline icone"></i></a>
  </div>
  <p>© 2025 Skull Jabb - Todos os direitos reservados.</p>
</footer>

</body>
</html>
