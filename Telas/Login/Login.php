<?php
session_start();
require_once "../Conexao/Conexao.php"; 
$conn = Conexao::getConexao();

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM cliente WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['ID_cliente'];
            $_SESSION['nome']       = $usuario['nome'];
            $_SESSION['email']      = $usuario['email'];
            $_SESSION['pontos']     = $usuario['pontos'];

            echo "<script>
                    alert('Login realizado com sucesso!');
                    window.location.href = '../Home/Home.php'; 
                  </script>";
            exit();
        } else {
            $erro = "Senha incorreta!";
        }
    } else {
        $erro = "Usuário não encontrado!";
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <link rel="shortcut icon" href="../Img/Elementos/Logo SJ.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../Login/Login.css">
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
      <?php if (isset($_SESSION['usuario_id'])): ?>
          <a href="../Perfil/Perfil.php">
            <img src="../Img/Perfis/Perfil.png" alt="Perfil">
          </a>
      <?php else: ?>
          <a href="../Login/Login.php">
            <img src="../Img/Perfis/Perfil.png" alt="Entrar">
          </a>
      <?php endif; ?>
    </div>
  </div>
</header>

<div class="page">
    <form method="POST" class="form-login">
        <h1>Login</h1>

        <?php if($erro != "") { echo "<p style='color:red; text-align:center; margin-bottom:15px;'>$erro</p>"; } ?>

        <p class="nomes">Nome de usuário ou e-mail</p>
        <input type="email" name="email" placeholder="Username@email.com" required>

        <p class="nomes">Senha</p>
        <input type="password" name="senha" placeholder="Minha senha" required>

        <p class="possui">Esqueci minha senha</p>
        <br><br>

        <button type="submit" class="btn-cadastrar">Login</button>
        <p class="possui-two">Não consigo efetuar login</p>
        <br><br><br>
        <p class="possui-three">Não possui uma conta? <a href="../Cadastro/Cadastro.php">Cadastrar-se</a></p>
    </form>
</div>

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
