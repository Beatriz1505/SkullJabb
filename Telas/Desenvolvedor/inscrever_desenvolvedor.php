<?php
session_start();
$host = "127.0.0.1"; 
$usuario = "root";     
$senha = "";           
$banco = "skulljabb";  
$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$cliente_id = $_SESSION['usuario_id'] ?? 1;

$mensagem = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descricao = trim($conn->real_escape_string($_POST['descricao']));
    $portfolio = trim($conn->real_escape_string($_POST['portfolio'] ?? ""));

    if (!empty($descricao)) {
        $sql = "INSERT INTO desenvolvedor (ID_cliente, descricao, status, portfolio) 
                VALUES ('$cliente_id', '$descricao', 0, " . 
                (!empty($portfolio) ? "'$portfolio'" : "NULL") . ")";
        
        if ($conn->query($sql) === TRUE) {
            $mensagem = "Inscrição enviada com sucesso! Aguarde aprovação.";
        } else {
            $mensagem = "Erro: " . $conn->error;
        }
    } else {
        $mensagem = "Preencha a descrição.";
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Inscrever Desenvolvedor</title>
  <link rel="shortcut icon" href="../Img/Elementos/Logo SJ.png" type="image/png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="inscrever_desenvolvedor.css">
</head>
<body class="img-bg">

<header class="navbar">
  <div class="left-side">
    <div class="logo">
      <img src="../Img/Elementos/Logo SJ.png" alt="Logo">
      <span>SKULL<br>JABB</span>
    </div>

   <div class="search">
      <input type="text" placeholder="Procurar...">
      <a href="#"><i class="mdi mdi-magnify search-icon"></i></a>
    </div>
  </div>

  <div class="nav-links">
    <a href="../Home/Home.php" class="grif">Home</a>
    <a href="../Loja/Loja.php">Loja</a>
    <a href="../Suporte/Suporte.php">Suporte</a>
  </div>

  <div class="icons">
    <a href="../Carrinho/Carrinho.php"><i class="mdi mdi-cart icone"></i></a>

    <div class="profile">
      <?php if ($usuario): ?>
          <a href="../Perfil/Perfil.php">
            <img src="../Img/Perfis/Perfil.png" alt="Perfil">
          </a>
      <?php else: ?>
          <a href="../Login/Login.php">
            <img src="../Img/Elementos/Perfil.png" alt="Entrar">
          </a>
      <?php endif; ?>
    </div>
  </div>
</header>
<br><br>
<div class="page">
    <form method="POST" class="form-dev">
        <h1>Inscrição de Desenvolvedor</h1>

        <?php if ($mensagem): ?>
            <p style="color:#3b7ef5;font-weight:bold;text-align:center;">
                <?= htmlspecialchars($mensagem) ?>
            </p>
        <?php endif; ?>

        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" placeholder="Fale sobre seus projetos, habilidades ou jogos que deseja publicar..." required></textarea>

        <label for="portfolio">Link do Portfólio (opcional)</label>
        <input type="url" id="portfolio" name="portfolio" placeholder="Ex: https://meuportfolio.com">

        <button type="submit" class="btn-cadastrar">Enviar Inscrição</button>
        <p class="possui">Voltar para <a href="Desenvolvedor.php">Área do Desenvolvedor</a></p>
    </form>
</div>

<br><br>
<footer>
  <div class="social-icons">
    <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
    <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
    <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline"></i></a>
  </div>
  <p>© 2025 Skull Jabb - Todos os direitos reservados.</p>
</footer>
</body>
</html>
