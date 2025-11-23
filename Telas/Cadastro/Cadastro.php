<?php
session_start();
require_once "../Conexao/Conexao.php";
$conn = Conexao::getConexao();


$usuario = null;
if (isset($_SESSION['ID_cliente'])) {
    $id = $_SESSION['ID_cliente'];
    $sql = "SELECT nome FROM cliente WHERE ID_cliente = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    }
    if ($stmt) $stmt->close();
}


$erro = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $senha_plain = $_POST['senha'] ?? '';

    if ($email === '' || $cpf === '' || $nome === '' || $senha_plain === '') {
        $erro = "Preencha todos os campos.";
    } else {
        $sqlCheck = "SELECT ID_cliente FROM cliente WHERE email = ? OR CPF = ? LIMIT 1";
        $stmt = $conn->prepare($sqlCheck);
        $stmt->bind_param("ss", $email, $cpf);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $erro = "E-mail ou CPF já cadastrado.";
            $stmt->close();
        } else {
            $stmt->close();

            
            $senha_hash = password_hash($senha_plain, PASSWORD_DEFAULT);

            $sql = "INSERT INTO cliente (email, CPF, nome, senha) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $erro = "Erro na preparação da query: " . $conn->error;
            } else {
                $stmt->bind_param("ssss", $email, $cpf, $nome, $senha_hash);
                if ($stmt->execute()) {
                    $stmt->close();
                   
                    header("Location: ../Login/Login.php");
                    exit;
                } else {
                    $erro = "Erro ao cadastrar: " . $stmt->error;
                    $stmt->close();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Cadastro</title>
  <link rel="stylesheet" href="Cadastro.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
  <link rel="shortcut icon" href="../../Img/Elementos/Logo SJ.png" sizes="64x64" type="image/x-icon">
</head>
<body>

<header class="navbar">
  <div class="left-side">
    <div class="logo">
      <img src="../../Img/Elementos/Logo SJ.png" alt="Logo">
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
</header>

<div class="page">
    <form method="POST" class="form-login" novalidate>
        <h1>Cadastro</h1>
        <?php if ($erro): ?>
            <div class="erro"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <p class="nomes">E-mail</p>
        <input type="email" name="email" placeholder="Username@email.com" required value="<?= isset($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : '' ?>">
        
        <p class="nomes">CPF</p>
        <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00" maxlength="14" required value="<?= isset($cpf) ? htmlspecialchars($cpf, ENT_QUOTES, 'UTF-8') : '' ?>">

        <p class="nomes">Nickname</p>
        <input type="text" name="nome" placeholder="MeuNickname" required value="<?= isset($nome) ? htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') : '' ?>">
        
        <p class="nomes">Senha</p>
        <input type="password" name="senha" placeholder="Minha senha" required>
        
        <button type="submit" class="btn-cadastrar">Cadastrar</button>
        <br>
        <p class="possui">Já possui conta? <a href="../Login/Login.php">Conecte-se</a></p>
    </form>
</div>

<br><br>

<footer>
  <div class="social-icons">
    <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
    <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
    <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline icone"></i></a>
  </div>
  <p>© 2025 Skull Jabb - Todos os direitos reservados.</p>
</footer>

<script src="../Login/Login.js"></script>
<script src="cadastro.js"></script>


</body>
</html>
