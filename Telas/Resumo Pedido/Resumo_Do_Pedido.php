<?php
session_start();

$ID_cliente = $_SESSION['usuario_id'] ?? null;
if(!$ID_cliente){ 
    echo "Usuário não logado!"; 
    exit; 
}

include_once 'Resumo_Pedido.php';
include_once 'Pedido_Item.php';

$resumo = new Resumo_Pedido($ID_cliente);

// 1️⃣ Finaliza o pedido apenas se não existir um pedido em andamento
if (!isset($_SESSION['ID_pedido']) || empty($_SESSION['ID_pedido'])) {
    $novoPedidoID = $resumo->finalizar();
    if (!$novoPedidoID) {
        echo "Erro ao criar o pedido.";
        exit;
    }
    $_SESSION['ID_pedido'] = $novoPedidoID;
} else {
    $novoPedidoID = $_SESSION['ID_pedido'];
}

$pedidoAtual = [
    'ID_pedido'  => $novoPedidoID,
    'ID_cliente' => $ID_cliente
];

// 2️⃣ Itens
$pedidoItem = new Pedido_Item($novoPedidoID);

// Sempre lista os itens do pedido antes de qualquer POST
$itens = $pedidoItem->listar();

// 🔄 Função para calcular subtotal (com descontos por item)
function calcularSubtotal($itens) {
    $subtotal = 0;
    foreach ($itens as $item) {
        $preco = $item['preco'];
        if (!empty($item['desconto']) && $item['desconto'] > 0) {
            $preco -= ($preco * $item['desconto'] / 100);
        }
        $subtotal += $preco * $item['quantidade'];
    }
    return round($subtotal, 2); // sempre arredonda para 2 casas decimais
}

// 3️⃣ Subtotal inicial
$subtotal = calcularSubtotal($itens);
$totalReal = $subtotal;
$cupomCodigo = null;
$cupomDesconto = 0;

// Garante que o total inicial do pedido seja o subtotal
$resumo->setTotal($subtotal);

// Mensagem de cupom (vazia por padrão)
$mensagemCupom = '';

// 4️⃣ Aplicar cupom
if (isset($_POST['aplicarCupom'])) {
    $codigo = $_POST['cupom'] ?? '';

    // Garante que o total inicial seja o subtotal antes do desconto
    $resumo->setTotal($subtotal);

    if ($resumo->AtualizarCupom($codigo)) {
        $mensagemCupom = '<p class="mensagem-cupom">Cupom aplicado com sucesso!</p>';

        $cupomCodigo   = $codigo;
        $cupomDesconto = round($subtotal - $resumo->getTotal(), 2);
        $totalReal     = round($resumo->getTotal(), 2);

        // Atualiza a lista de itens para não perder os jogos
        $itens = $pedidoItem->listar();
    } else {
        $mensagemCupom = '<p class="mensagem-erro">Cupom inválido ou expirado.</p>';
    }
}

// 5️⃣ Adicionar jogo
if (isset($_POST['adicionarJogo'])) {
    $nomeJogo = $_POST['jogo'] ?? '';
    $sql = $resumo->conn->prepare("SELECT ID_jogo FROM jogo WHERE nome LIKE :nome LIMIT 1");
    $likeNome = "%$nomeJogo%";
    $sql->bindParam(':nome', $likeNome);
    $sql->execute();
    $jogo = $sql->fetch(PDO::FETCH_ASSOC);

    if ($jogo) {
        $pedidoItem = new Pedido_Item($pedidoAtual['ID_pedido'], $jogo['ID_jogo'], 1);
        if ($pedidoItem->adicionar()) {
            $mensagemCupom = '<p class="mensagem-sucesso">Jogo adicionado ao pedido!</p>';

            // Atualiza lista de itens, subtotal e total
            $itens       = $pedidoItem->listar();
            $subtotal    = calcularSubtotal($itens);
            $totalReal   = $subtotal;
            $cupomCodigo = null;
            $cupomDesconto = 0;
            $resumo->setTotal($totalReal);
        }
    } else {
        $mensagemCupom = '<p class="mensagem-erro">Jogo não encontrado.</p>';
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumo Pedido</title>
   
    <link rel="shortcut icon" href="../../Img/Elementos/Logo SJ.png"sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Resumo_Pedido.css?v=<?= time() ?>">
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

</head>


<body>

  <div class="content">

   <header class="navbar">
  <div class="left-side">
    <div class="logo">
      <a href="#"><img src="../../Img/Elementos/Logo SJ.png" alt="Caveira branca com capuz azul"></a>
      <a class="lin" href=""><span>SKULL<br>JABB</span></a>
    </div>

    <div class="search">
      <input type="text" placeholder="Procurar...">
      <a href="#"><i class="mdi mdi-magnify search-icon"></i></a>
    </div>
  </div>


    <nav class="nav-links">
      <a class="grif" href="#">Home</a>
      <a href="#">Loja</a>
      <a href="#">Suporte</a>
    </nav>

    <div class="icons">
      <a href="#"><i class="mdi mdi-cart icone"></i></a>
      <div class="profile">
        <a href="#"><img src="../../Img/Perfis/Artist_.jpeg" alt="Perfil"></a>
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
      
<br/>
<br/>
<div class="container-jogos">
 <section class="info-01">
    <h2>RESUMO DO PEDIDO #<?= $pedidoAtual['ID_pedido'] ?></h2> 
    </section>
    <div class="container-pedido">
        
      <div class="itens-container">
        <br/>
    <?php foreach($itens as $item): ?>
        <div class="item-jogo">
            <img src="<?= htmlspecialchars($item['Img']) ?>" alt="<?= htmlspecialchars($item['nome']) ?>">
            <div class="item-info">
                <p class="nome"><?= htmlspecialchars($item['nome']) ?></p>
                
                <p class="preco">R$<?= number_format($item['preco'],2,',','.') ?></p>
                <?php if($item['desconto'] > 0): ?>
                    <p class="desconto">-<?= $item['desconto'] ?>%</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
 
<div class="Infos">
     <div class="Linha"></div>
    <div class="resumo-total">

        <!-- Container de botões acima do subtotal -->
        <div class="acoes-pedido">
            <?= $mensagemCupom ?>
            <form method="POST" action="" class="acao-form">
                <button type="button" class="toggle-input" data-target="cupomInput" title="Aplicar cupom">
                    <span class="mdi mdi-ticket-percent-outline"></span>
                </button>
                <div class="input-popup" id="cupomInput">
                    <input class="box-text" type="text" name="cupom" placeholder="Cupom" required>
                    <button class="botao" type="submit" name="aplicarCupom"><span class="mdi mdi-chevron-right"></span></button>
                </div>
            </form>

            <form method="POST" action="" class="acao-form">
                <button type="button" class="toggle-input" data-target="jogoInput" title="Adicionar jogo">
                    <span class="mdi mdi-loupe"></span>
                </button>
                <div class="input-popup" id="jogoInput">
                    <input class="box-text" type="text" name="jogo" placeholder="Jogo" required>
                    <button class="botao" type="submit" name="adicionarJogo"><span class="mdi mdi-chevron-right"></span></button>
                </div>
            </form>
        </div>

        <!-- Subtotal e informações -->
        <p><strong>Subtotal:</strong> R$ <?= number_format($subtotal,2,',','.') ?></p>

        <?php if ($cupomCodigo): ?>
            <p><strong>Cupom:</strong> <?= htmlspecialchars($cupomCodigo) ?>  
            (− R$ <?= number_format($cupomDesconto,2,',','.') ?>)</p>
        <?php endif; ?>
        <div class="Linha"></div>
        <p class="total-text"><strong>Total:</strong> R$ <?= number_format($totalReal,2,',','.') ?></p>

        <button id="btnFinalizarCompra" class="botao-finalizar">Finalizar Compra</button>
    </div>
</div>
<!-- Modal Finalizar Compra -->
<div id="modalPagamento" class="modal">
  <div class="modal-conteudo">
    <span id="fecharModalPagamento" class="fechar">&times;</span>
    <h2>Escolha a forma de pagamento</h2>

    <div id="detalhesPagamento" class="detalhes-pagamento">
      
    </div>
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
<div id="modalConfirmacao" class="modal" style="display:none;">
  <div class="modal-conteudo">
    <span id="fecharModalConfirmacao" class="fechar">&times;</span>
    <p id="mensagemConfirmacao">Pagamento concluído com sucesso!</p>
    <button id="btnFecharConfirmacao">Fechar</button>
  </div>
</div>

<script src="Resumo_Pedido.js"></script>


        </div>
        </div>

<br/>
<br/>
 <footer>
  <div class="social-icons">
    <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
    <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
    <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline"></i></a>
  </div>
  <p>© 2025 Skull Jabb - Todos os direitos reservados.</p>
</footer>
</div>
</body>
</html>
