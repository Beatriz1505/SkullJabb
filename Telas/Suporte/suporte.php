<?php
session_start();
require_once "../Conexao/Conexao.php";
require_once "../Perfil/ClasseModelagemPerfil.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Login/Login.php");
    exit;
}

$perfil = Perfil::buscarPorId($_SESSION['usuario_id']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mensagem = trim($_POST["mensagem"]);

    if (!empty($mensagem)) {
        try {
            // Conecta ao banco
            $conn = Conexao::getConexao();

            // Insere a mensagem na tabela suporte
            $sql = "INSERT INTO suporte (id_cliente, mensagem) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $_SESSION['usuario_id'], $mensagem);

            if ($stmt->execute()) {
                echo "<script>alert('Sua mensagem foi enviada com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao salvar sua mensagem. Tente novamente.');</script>";
            }

            $stmt->close();
            $conn->close();

        } catch (Exception $e) {
            echo "<script>alert('Erro ao conectar ao banco de dados.');</script>";
        }

    } else {
        echo "<script>alert('Por favor, escreva uma mensagem antes de enviar.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suporte</title>
  <link rel="stylesheet" href="suporte.css">
  <link rel="shortcut icon" href="../../Img/Elementos/Logo SJ.png" sizes="64x64" type="image/x-icon">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css"> 
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@500&display=swap" rel="stylesheet">
</head>

<body>
<div class="content">
  <header class="navbar">
    <div class="left-side">
      <div class="logo">
        <a href="../Home/Home.php"><img src="../../Img/Elementos/Logo SJ.png" alt="Caveira branca com capuz azul"></a>
        <a class="lin" href="../Home/Home.php"><span>SKULL<br>JABB</span></a>
      </div>
      <div class="search">
        <input type="text" placeholder="Procurar...">
        <a href="#"><i class="mdi mdi-magnify search-icon"></i></a>
      </div>
    </div>
    <nav class="nav-links">
      <a class="lin" href="../Home/home.php">Home</a>
      <a class="lin" href="../Loja/loja.php">Loja</a>
      <a class="grif" href="../Suporte/suporte.php">Suporte</a>
    </nav>
    <div class="icons">
      <a href="../Carrinho/carrinho.php"><i class="mdi mdi-cart icone"></i></a>
      <div class="profile">
        <a href="../Perfil/perfil.php"><img src="<?= $perfil->foto ? $perfil->foto : '../../Img/Elementos/user.png' ?>" alt="Perfil"></a>
      </div>
    </div>
  </header>
</div>

<!-- VLibras -->
<div vw class="enabled">
  <div vw-access-button class="active"></div>
  <div vw-plugin-wrapper><div class="vw-plugin-top-wrapper"></div></div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script> new window.VLibras.Widget('https://vlibras.gov.br/app'); </script>

<main>
  <div class="container">
    <form method="POST" action="suporte.php">
      <p class="textform">Encontrou algum erro ou está enfrentando dificuldades?<br>Fale com a nossa equipe</p>
      <textarea name="mensagem" placeholder="Relate seu problema"></textarea>
      <br>
      <button type="submit">Enviar</button>
    </form>
  </div>
</main>

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
