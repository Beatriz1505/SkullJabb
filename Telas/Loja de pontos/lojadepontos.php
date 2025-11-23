<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../Conexao/Conexao.php";
require_once "../Perfil/ClasseModelagemPerfil.php";
require_once "ClasseModelagemLojaPontos.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Login/Login.php");
    exit;
}

$idUsuario = (int) $_SESSION['usuario_id'];

function normalizarCaminhoImagem($imgPath) {
    if (!$imgPath) return '../../Img/Jogos/gris.png';
    $imgPath = trim($imgPath);
    if (preg_match('#^(https?:)?//#i', $imgPath) || strpos($imgPath, '/') === 0) {
        return $imgPath;
    }
    $imgPath = preg_replace('#^(\.\./|\.\/)+#', '', $imgPath);
    return '../../' . ltrim($imgPath, '/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['buy_item'])) {
        $id_item = (int)$_POST['buy_item'];
        $result = LojaPontos::comprarItem($idUsuario, $id_item);
        
        if (is_array($result)) {
            $_SESSION['purchase_message'] = $result['msg'] ?? 'Resposta inesperada.';
            $_SESSION['purchase_success'] = $result['success'] ?? false;
        } else {
            $_SESSION['purchase_message'] = (string)$result;
            $_SESSION['purchase_success'] = stripos($result, 'sucesso') !== false;
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['claim'])) {
        $result = LojaPontos::resgatarCheckinDiario($idUsuario);

        if (is_array($result)) {
            $_SESSION['claim_message'] = $result['msg'] ?? 'Resposta inesperada.';
            $_SESSION['claim_success'] = $result['success'] ?? false;
        } else {
            $_SESSION['claim_message'] = (string)$result;
            $_SESSION['claim_success'] = stripos($result, 'sucesso') !== false;
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$perfil = Perfil::buscarPorId($idUsuario);
if (!$perfil) {
    $perfil = new stdClass();
    $perfil->pontos = 0;
    $perfil->foto = '../../Img/Elementos/user.png';
    $perfil->ultimo_resgate = null;
}

$items = LojaPontos::listarItens();

$jaResgatouHoje = false;

if (!empty($perfil->ultimo_resgate)) {
    $ultimo = new DateTime($perfil->ultimo_resgate);
    $agora = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
    $diff = $ultimo->diff($agora);

    if ($diff->h < 24 && $diff->days == 0) {
        $jaResgatouHoje = true;
    }
}

if (isset($_SESSION['purchase_message'])) {
    $purchase_message = $_SESSION['purchase_message'];
    $purchase_success = $_SESSION['purchase_success'];
    unset($_SESSION['purchase_message'], $_SESSION['purchase_success']);
}

if (isset($_SESSION['claim_message'])) {
    $claim_message = $_SESSION['claim_message'];
    $claim_success = $_SESSION['claim_success'];
    unset($_SESSION['claim_message'], $_SESSION['claim_success']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Loja de Pontos</title>
  <link rel="stylesheet" href="LojadePontos.css">
  <link rel="shortcut icon" href="../../Img/Elementos/Logo SJ.png" sizes="64x64" type="image/x-icon">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css"> 
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@400..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
  <div class="content">
    <header class="navbar">
      <div class="left-side">
        <div class="logo">
          <a href="../Home/home.php"><img src="../../Img/Elementos/Logo SJ.png" alt="Logo Skull"></a>
          <a class="lin" href=""><span>SKULL<br>JABB</span></a>
        </div>
        <div class="search">
          <input type="text" placeholder="Pesquisar...">
          <a href="#"><i class="mdi mdi-magnify search-icon"></i></a>
        </div>
      </div>
      <nav class="nav-links">
        <a class="grif" href="../Home/Home.php">Home</a> 
        <a href="../Loja/loja.php">Loja</a>
        <a href="../Suporte/Suporte.php">Suporte</a> 
      </nav>
      <div class="icons">
        <a href="../Carrinho/Carrinho.php"><i class="mdi mdi-cart icone"></i></a>
        <div class="profile">
          <a href="../Perfil/Perfil.php">
            <img src="<?= htmlspecialchars($perfil->foto ?: '../../Img/Elementos/user.png', ENT_QUOTES) ?>" alt="Perfil">
          </a>
        </div>
      </div>
    </header>

    <main class="loja-container">
      <h2 class="titulo-loja">LOJA DE PONTOS</h2>

      <?php if (!empty($purchase_message)): ?>
        <div class="alert alert-<?= !empty($purchase_success) ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($purchase_message) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if (!empty($claim_message)): ?>
        <div class="alert alert-<?= !empty($claim_success) ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($claim_message) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-md-3">
          <div class="box points-box">
            <h3 class="title-box">PONTOS:</h3>
            <div class="points-content">
              <img src="../../Img/Elementos/SJ.png" alt="Moeda" class="box-icon">
              <p class="valor-pontos"><?= (int)$perfil->pontos ?></p>
            </div>
          </div>

          <div class="box checkin-box">
            <h3 class="title-box">Check-in Diário:</h3>
            <div class="checkin-row">
              <img src="../../Img/Elementos/gift.png" alt="Presente" class="gift-icon">
              <div class="checkin-info">
                <p class="valor">+30</p>
                <?php if ($jaResgatouHoje): ?>
                  <button class="btn-resgatar" disabled style="background: #6c757d; cursor: not-allowed;">Já resgatado</button>
                <?php else: ?>
                  <form method="POST" style="display:inline;">
                    <button type="submit" name="claim" value="1" class="btn-resgatar">Resgatar</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-9">
          <div class="row g-3">
            <?php if (!empty($items)): ?>
              <?php foreach ($items as $item):
                $imgRaw = $item['Img'] ?? $item['img'] ?? ($item['img_path'] ?? '');
                $img = normalizarCaminhoImagem($imgRaw);
                $cost = $item['custo_pontos'] ?? $item['preco'] ?? 0;
                $type = strtolower(trim($item['tipo'] ?? ''));
                $imageClass = ($type === 'jogo') ? 'gam' : (($type === 'moldura') ? 'mold' : 'gam');

                $alreadyBought = LojaPontos::verificarItemComprado($idUsuario, $item['ID_item']);
              ?>
                <div class="col-md-4">
                  <?php if ($alreadyBought): ?>
                    <div class="item-card item-comprado">
                      <div style="position:relative;">
                        <div class="comprado-badge">COMPRADO</div>
                        <div class="item-image">
                          <img class="<?= $imageClass ?>" src="<?= htmlspecialchars($img, ENT_QUOTES) ?>" alt="Item">
                        </div>
                      </div>
                      <div class="price-container">
                        <img class="SJ" src="../../Img/Elementos/SJ.png" alt="Moeda">
                        <div class="game-price"><?= number_format($cost, 0, ',', '.') ?></div>
                      </div>
                      <div class="item-actions">
                        <button class="btn-comprado" disabled></button>
                      </div>
                    </div>
                  <?php else: ?>
                    <form method="POST" class="w-100">
                      <input type="hidden" name="buy_item" value="<?= (int)$item['ID_item'] ?>">
                      <button type="submit" class="item-card item-compravel">
                        <div class="item-image">
                          <img class="<?= $imageClass ?>" src="<?= htmlspecialchars($img, ENT_QUOTES) ?>" alt="Item">
                        </div>
                        <div class="price-container">
                          <img class="SJ" src="../../Img/Elementos/SJ.png" alt="Moeda">
                          <div class="game-price"><?= number_format($cost, 0, ',', '.') ?></div>
                        </div>
                        <div class="item-actions" style="margin-top:10px;">
                          <div class="btn-comprar"></div>
                        </div>
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-12">
                <p class="text-center text-white">Nenhum item disponível na loja.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main> 

    <footer>
      <div class="social-icons">
        <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
        <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
        <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline icone"></i></a>
      </div>
      <p>© 2024 Skull Jabb - Todos os direitos reservados.</p>
    </footer>
  </div>

  <div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper><div class="vw-plugin-top-wrapper"></div></div>
  </div>

  <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
  <script> new window.VLibras.Widget('https://vlibras.gov.br/app'); </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
