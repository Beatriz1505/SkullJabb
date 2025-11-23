<?php
session_start();
require_once "../Loja de pontos/ClasseModelagemLojaPontos.php";
require_once "ClasseModelagemPerfil.php";
require_once "../Conexao/Conexao.php";
require_once "../Perfil - User/ClasseModelagemPerfilUser.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Login/Login.php");
    exit;
}

$meuId = (int) $_SESSION['usuario_id'];
$idPerfil = isset($_GET['id']) ? (int)$_GET['id'] : $meuId;
$meuPerfil = ($meuId === $idPerfil);

$perfil = Perfil::buscarPorId($idPerfil);
if (!$perfil) {
    echo "Perfil não encontrado!";
    exit;
}

$amigos = [];
$jogosRecentes = [];
$pendentes = [];

$itensComprados = LojaPontos::listarItensComprados($meuId) ?? [];

if (class_exists('PerfilUser')) {
    $amigos = PerfilUser::buscarAmigos($idPerfil);
    $jogosRecentes = PerfilUser::buscarJogosRecentes($idPerfil);
    if ($meuPerfil) {
        $pendentes = PerfilUser::buscarSolicitacoesRecebidas($meuId);
    }
}

$estadoRelacao = 'nenhum';
if (class_exists('PerfilUser') && method_exists('PerfilUser', 'obterEstadoRelacao')) {
    try {
        $tmp = PerfilUser::obterEstadoRelacao((int)$meuId, (int)$idPerfil);
        if (!empty($tmp)) $estadoRelacao = (string)$tmp;
    } catch (Exception $e) { /* silent fallback */ }
}

if ($estadoRelacao === 'nenhum') {
    try {
        $conn = Conexao::getConexao();
        if ($conn) {
            $sql = "SELECT id_solicitante, id_recebedor, status
                    FROM amizade
                    WHERE (id_solicitante = ? AND id_recebedor = ?) OR (id_solicitante = ? AND id_recebedor = ?)
                    ORDER BY data_solicitacao DESC
                    LIMIT 1";
            $st = $conn->prepare($sql);
            if ($st) {
                $st->bind_param("iiii", $meuId, $idPerfil, $idPerfil, $meuId);
                $st->execute();
                $res = $st->get_result();
                if ($res && $res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    $status = strtolower(trim((string)($row['status'] ?? '')));
                    if (in_array($status, ['aceito','1','3','amigos'])) {
                        $estadoRelacao = 'amigos';
                    } elseif (in_array($status, ['pendente','0','pend'])) {
                        $id_solicitante = (int)$row['id_solicitante'];
                        $estadoRelacao = ($id_solicitante === (int)$meuId) ? 'pendente_enviado' : 'pendente_recebido';
                    } else {
                        $estadoRelacao = 'nenhum';
                    }
                } else {
                    $estadoRelacao = 'nenhum';
                }
                $st->close();
            }
        }
    } catch (Exception $e) {
        $estadoRelacao = 'nenhum';
    }
}

$fotoPerfil = $perfil->foto ? $perfil->foto : '../../Img/Elementos/user.png';
$molduraPerfil = $perfil->moldura ?? '';
$displayNome = htmlspecialchars($perfil->nome ?? '');
$displayUsuario = htmlspecialchars($perfil->usuario ?? strtolower(str_replace(" ", "", $perfil->nome ?? '')));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Perfil - <?= $displayNome ?></title>

  <link rel="stylesheet" href="Perfil.css" />
  <link rel="shortcut icon" href="../../Img/Elementos/Logo SJ.png" sizes="64x64" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@500&display=swap" rel="stylesheet">

</head>
<body>
  <div class="content">
    <header class="navbar">
      <div class="left-side">
        <div class="logo">
          <a href="../Home/home.php"><img src="../../Img/Elementos/Logo SJ.png" alt="Logo"></a>
          <a class="lin" href="../Home/home.php"><span>SKULL<br>JABB</span></a>
        </div>
      <div class="search">
        <input type="text" placeholder="Procurar...">
        <a href="#"><i class="mdi mdi-magnify search-icon"></i></a>
      </div>
      </div>

      <nav class="nav-links">
        <a class="grif" href="../Home/home.php">Home</a>
        <a href="../Loja/loja.php">Loja</a>
        <a href="../Suporte/suporte.php">Suporte</a>
      </nav>

      <div class="icons">
        <a href="../Carrinho/carrinho.php"><i class="mdi mdi-cart icone"></i></a>
        <div class="profile">
          <a href="perfil.php" title="Meu perfil">
            <img src="<?= htmlspecialchars(Perfil::buscarPorId($meuId)->foto ?? '../../Img/Elementos/user.png') ?>" alt="Meu Perfil">
          </a>
        </div>
      </div>
    </header>
  </div>

  <div vw class="enabled"><div vw-access-button class="active"></div><div vw-plugin-wrapper><div class="vw-plugin-top-wrapper"></div></div></div>
  <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
  <script> new window.VLibras.Widget('https://vlibras.gov.br/app'); </script>

  <div class="perfil-wrapper">
    <div class="perfil-box esquerda">
      <div class="avatar-wrapper">
        <?php if (!empty($molduraPerfil)): ?>
          <img src="<?= htmlspecialchars($molduraPerfil) ?>" class="moldura-ativa" alt="moldura">
        <?php endif; ?>
        <img src="<?= htmlspecialchars($fotoPerfil) ?>" alt="Perfil" class="avatar">
      </div>
      <div class="perfil-textos">
        <p class="nome"><?= $displayNome ?></p>
        <p class="username">@<?= $displayUsuario ?></p>
      </div>
    </div>

    <div class="perfil-box direita">
      <div class="botoes">
        <?php if ($meuPerfil): ?>
          <form method="post" action="../Login/logout.php" style="display:inline-block;">
            <button type="submit">Desconectar-se</button>
          </form>
        <?php endif; ?>
      </div>

      <?php if ($meuPerfil): ?>
        <a href="#" id="abrir-popup">Editar perfil</a>
        <a href="../Biblioteca/biblioteca.php">Biblioteca</a>
        <a href="../Lista de Desejos/Lista_De_Desejo.php">Lista de desejos</a>
        <a href="#" id="abrir-solicitacoes">Solicitações <span class="badge-solicitacoes"><?= count($pendentes) ?></span></a>
      <?php else: ?>
        <div class="acao-amizade-wrap">
          <?php
            if ($estadoRelacao === 'amigos') {
                echo '<button id="btn-friend" class="btn-remove" data-action="remover">Remover</button>';
            } elseif ($estadoRelacao === 'pendente_enviado') {
                echo '<button id="btn-friend" class="btn-disabled" disabled>Solicitação enviada</button>';
            } elseif ($estadoRelacao === 'pendente_recebido') {
                echo '<button id="btn-friend" class="btn-disabled" disabled>Responder solicitação</button>';
            } else {
                echo '<button id="btn-friend" class="btn-add" data-action="adicionar">+ Adicionar</button>';
            }
          ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($meuPerfil): ?>
  <div class="popup" id="popup-editar" style="display:none;">
    <div class="popup-content editar-content">
      <form action="salvar.php" method="post" enctype="multipart/form-data" id="form-editar-perfil">
        <div class="avatar-box">
          <img id="preview" src="<?= htmlspecialchars($fotoPerfil) ?>" alt="Foto de perfil">
          <label for="foto-upload" class="edit-icon"><i class="mdi mdi-pencil"></i></label>
          <input type="file" name="foto" id="foto-upload" accept="image/*" style="display:none;">
        </div>

        <input type="text" name="nome" value="<?= htmlspecialchars($perfil->nome) ?>" placeholder="Nome" class="input-field">
        <input type="text" name="usuario" value="<?= htmlspecialchars($perfil->usuario ?? '') ?>" placeholder="Usuário" class="input-field">

<div class="molduras">

  <!-- Moldura: Nenhuma -->
  <label>
    <input type="radio" name="moldura" value=""
      <?= empty($perfil->moldura) ? "checked" : "" ?>>
    <span style="color:#fff">Nenhuma</span>
  </label>

  <!-- Molduras fixas -->
  <?php  
  $moldurasFixas = [
      "../../Img/Loja de Pontos/moldura_caveira.png",
      "../../Img/Loja de Pontos/moldura_coracao.png",
      "../../Img/Loja de Pontos/moldura_cogumelo.png",
      "../../Img/Loja de Pontos/moldura_gatos_azuis.png"
  ];

  foreach ($moldurasFixas as $m):
  ?>
      <label>
        <input type="radio" name="moldura" value="<?= $m ?>"
          <?= ($perfil->moldura ?? '') === $m ? "checked" : "" ?>>
        <img src="<?= $m ?>">
      </label>
  <?php endforeach; ?>


  <!-- Molduras compradas na loja -->
  <?php foreach ($itensComprados as $item):
      if (strtolower($item['tipo']) !== 'moldura') continue;
      $img = htmlspecialchars($item['Img']);
  ?>
      <label>
        <input type="radio" name="moldura" value="<?= $img ?>"
          <?= ($perfil->moldura ?? '') === $img ? "checked" : "" ?>>
        <img src="<?= $img ?>">
      </label>
  <?php endforeach; ?>

</div>

        <div class="editar-botoes">
          <button type="submit" class="salvar">Salvar</button>
          <button type="button" class="sair" id="fechar-popup">Sair</button>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <div class="popup" id="popup-solicitacoes">
    <div class="popup-content">
      <h3>Solicitações recebidas (<?= count($pendentes) ?>)</h3>
      <div id="requests-list" class="requests-list">
        <?php if (empty($pendentes)): ?>
          <p>Nenhuma solicitação pendente.</p>
        <?php else: ?>
          <?php foreach ($pendentes as $p):
              $reqId = (int)($p['id'] ?? $p['id_solicitante'] ?? 0);
              $nome = htmlspecialchars($p['nome'] ?? $p['usuario'] ?? 'Usuário');
              $foto = htmlspecialchars($p['foto'] ?? '../../Img/Elementos/user.png');
          ?>
            <div id="req-<?= $reqId ?>" class="request-item" style="display:flex; gap:10px; align-items:center; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.03);">
              <img src="<?= $foto ?>" alt="avatar" class="small-avatar" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
              <div class="request-info" style="flex:1;">
                <strong><?= $nome ?></strong>
                
              </div>
              <div class="request-actions" style="display:flex;gap:6px;">
                <button class="btn-accept accept-btn" data-id="<?= $reqId ?>">Aceitar</button>
                <button class="btn-decline decline-btn" data-id="<?= $reqId ?>">Recusar</button>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div style="margin-top:12px; text-align:right;">
        <button id="fechar-solicitacoes" class="sair">Fechar</button>
      </div>
    </div>
  </div>

  <div class="containers-wrapper">
    <div class="container">
      <h1>Amigos</h1>
      <?php if (!empty($amigos)): ?>
        <?php foreach ($amigos as $a): ?>
          <div class="friend">
            <a href="perfil.php?id=<?= (int)($a['ID_cliente'] ?? $a['id']) ?>">
              <img src="<?= htmlspecialchars($a['foto'] ?? '../../Img/Elementos/user.png') ?>" alt="Avatar" class="small-avatar">
            </a>
            <p><?= htmlspecialchars($a['nome'] ?? '') ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Sem amigos exibidos.</p>
      <?php endif; ?>
    </div>

    <div class="container">
      <h1>Jogos recentes</h1>
      <br>
<?php if (!empty($jogosRecentes)): ?>
  <?php foreach ($jogosRecentes as $j): ?>
    <?php
      $jogoId = (int)($j['jogo_id'] ?? $j['id_jogo'] ?? 0);
      $capa = htmlspecialchars(trim($j['capa'] ?? '../../Img/Jogos/unpacking_perfil.png'));
      $nome = htmlspecialchars(trim($j['nome_jogo'] ?? ('Jogo #' . $jogoId)));
      $horas = htmlspecialchars(trim($j['horas_jogadas'] ?? '0'));
      $link = $jogoId > 0 ? "../Tela de Jogos/teladejogos.php?id={$jogoId}" : "../Loja/loja.php";
    ?>
    <div class="game">
      <a href="<?= $link ?>">
        <img src="<?= $capa ?>" alt="<?= $nome ?>" style="max-width:120px; height:auto;">
      </a>
      <div class="game-info">
        <h2><a href="<?= $link ?>" style="color:inherit; text-decoration:none;"><?= $nome ?></a></h2>
        <span>Horas jogadas: <?= $horas ?></span>
      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p>Nenhum jogo recente encontrado.</p>
<?php endif; ?>
</div>
  </div>

  <script>
    window.PERFIL_CONFIG = {
      perfilId: <?= (int)$idPerfil ?>,
      meuId: <?= (int)$meuId ?>,
      basePath: '../Perfil - User/'
    };
  </script>
  <script src="Perfil.js"></script>

  <footer>
    <div class="social-icons">
      <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
      <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
      <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline icone"></i></a>
    </div>
    <p>© 2024 Skull Jabb - Todos os direitos reservados.</p>
  </footer>
</body>
</html>
