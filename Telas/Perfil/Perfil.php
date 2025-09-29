<?php
session_start();
require_once "ClasseModelagemPerfil.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Login/Login.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$perfil = Perfil::buscarPorId($id_usuario);

if (!$perfil) {
    echo "Perfil não encontrado!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil</title>
  <link rel="stylesheet" href="Perfil.css">
  <link rel="shortcut icon" href="../../Img/Elementos/Logo SJ.png" sizes="64x64" type="image/x-icon">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css"> 
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@500&display=swap" rel="stylesheet">
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
      <a class="grif" href="../Home/home.php">Home</a>
      <a href="../Loja/loja.php">Loja</a>
      <a href="../Suporte/suporte.php">Suporte</a>
    </nav>
    <div class="icons">
      <a href="../Carrinho/carrinho.php"><i class="mdi mdi-cart icone"></i></a>
      <div class="profile">
        <a href="#"><img src="<?= $perfil->foto ? $perfil->foto : '../../Img/Elementos/user.png' ?>" alt="Perfil"></a>
      </div>
    </div>
  </header>
</div>

<div vw class="enabled">
  <div vw-access-button class="active"></div>
  <div vw-plugin-wrapper><div class="vw-plugin-top-wrapper"></div></div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script> new window.VLibras.Widget('https://vlibras.gov.br/app'); </script>

<div class="perfil-wrapper">
  <div class="perfil-box esquerda">
    <div class="avatar-wrapper">
      <?php if (!empty($perfil->moldura)) : ?>
        <img src="<?= $perfil->moldura ?>" class="moldura-ativa">
      <?php endif; ?>
      <img src="<?= $perfil->foto ? $perfil->foto : '../../Img/Elementos/user.png' ?>" alt="Perfil" class="avatar">
    </div>
    <div class="perfil-textos">
      <p class="nome"><?= htmlspecialchars($perfil->nome) ?></p>
      <p class="username">@<?= htmlspecialchars($perfil->usuario ?? strtolower(str_replace(" ", "", $perfil->nome))) ?></p>
    </div>
  </div>
  <div class="perfil-box direita">
    <div class="botoes">
      <form method="post" action="../Login/logout.php">
        <button type="submit">Desconectar-se</button>
      </form>
    </div>
    <a href="#" id="abrir-popup">Editar perfil</a>
    <a href="../Biblioteca/biblioteca.php">Biblioteca</a>
    <a href="../Lista de Desejos/Lista_De_Desejo.php">Lista de desejos</a>
  </div>
</div>

<div class="popup" id="popup-editar">
  <div class="popup-content">
    <form action="salvar.php" method="post" enctype="multipart/form-data">
      <div class="avatar-box">
        <img id="preview" src="<?= $perfil->foto ? $perfil->foto : '../../Img/Elementos/user.png' ?>" alt="Foto de perfil">
        <label for="foto-upload" class="edit-icon"><i class="mdi mdi-pencil"></i></label>
        <input type="file" name="foto" id="foto-upload" style="display:none;">
      </div>
      <input type="text" name="nome" value="<?= htmlspecialchars($perfil->nome) ?>" placeholder="Nome" class="input-field">
      <input type="text" name="usuario" value="<?= htmlspecialchars($perfil->usuario ?? '') ?>" placeholder="Usuário" class="input-field">
      <div class="molduras">
  <label>
    <input type="radio" name="moldura" value="" <?= empty($perfil->moldura) ? "checked" : "" ?>>
    <span>None</span>
  </label>
  <label>
    <input type="radio" name="moldura" value="../../Img/Loja de Pontos/moldura_caveira.png" <?= ($perfil->moldura ?? '') === "../../Img/Loja de Pontos/moldura_caveira.png" ? "checked" : "" ?>>
    <img src="../../Img/Loja de Pontos/moldura_caveira.png">
  </label>
  <label>
    <input type="radio" name="moldura" value="../../Img/Loja de Pontos/moldura_coracao.png" <?= ($perfil->moldura ?? '') === "../../Img/Loja de Pontos/moldura_coracao.png" ? "checked" : "" ?>>
    <img src="../../Img/Loja de Pontos/moldura_coracao.png">
  </label>
    <label>
    <input type="radio" name="moldura" value="../../Img/Loja de Pontos/moldura_cogumelo.png" <?= ($perfil->moldura ?? '') === "../../Img/Loja de Pontos/moldura_cogumelo.png" ? "checked" : "" ?>>
    <img src="../../Img/Loja de Pontos/moldura_cogumelo.png">
  </label>
  <label>
    <input type="radio" name="moldura" value="../../Img/Loja de Pontos/moldura_gatos_azuis.png" <?= ($perfil->moldura ?? '') === "../../Img/Loja de Pontos/moldura_gatos_azuis.png" ? "checked" : "" ?>>
    <img src="../../Img/Loja de Pontos/moldura_gatos_azuis.png">
  </label>
  
</div>
        
      <div class="botoes">
        <button type="submit" class="salvar">Salvar</button>
        <button type="button" class="sair" id="fechar-popup">Sair</button>
      </div>
    </form>
  </div>
</div>

<div class="containers-wrapper">
  <div class="container">
    <h1>Amigos</h1>
    <div class="friend">
      <a href="#"><img src="../../Img/Perfis/User 01.png" alt="Avatar"></a>
      <p>Star Girl</p>
    </div>
    <div class="friend">
      <a href=""><img src="../../Img/Perfis/User 02.png" alt="Avatar"></a>
      <p>Mamim</p>
    </div>
    <div class="friend">
      <a href=""><img src="../../Img/Perfis/User 03.jpeg" alt="Avatar"></a>
      <p>Abby</p>
    </div>
  </div>
  <div class="container">
    <h1>Jogos recentes</h1><br>
    <div class="game">
      <a href=""><img src="../../Img/Jogos/unpacking_perfil.png" alt="Unpacking"></a>
      <div class="game-info">
        <h2>Unpacking</h2>
        <span>Horas: 60,4</span>
      </div>
    </div>
    <div class="game">
      <a href=""><img src="../../Img/Jogos/gris_perfil.png" alt="GRIS"></a>
      <div class="game-info">
        <h2>GRIS</h2>
        <span>Horas: 12,4</span>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.2/color-thief.umd.js"></script>
<script src="Perfil.js"></script>

<footer>
  <div class="social-icons">
    <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
    <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
    <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline"></i></a>
  </div>
  <p>© 2024 Skull Jabb - Todos os direitos reservados.</p>
</footer>
</body>
</html>
