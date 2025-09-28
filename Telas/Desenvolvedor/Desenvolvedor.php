<?php
session_start(); // sempre no topo, antes de qualquer saída

class Conexao {
    private static $host = "localhost";
    private static $user = "root";      
    private static $pass = "";           
    private static $db   = "skulljabb"; 
    private static $conn = null;

    public static function getConexao() {
        if (self::$conn === null) {
            self::$conn = new mysqli(self::$host, self::$user, self::$pass, self::$db);
            if (self::$conn->connect_error) {
                die("Erro na conexão: " . self::$conn->connect_error);
            }
        }
        return self::$conn;
    }
}

$conn = Conexao::getConexao();

// Busca todos os desenvolvedores aprovados junto com dados do cliente
$sql = "SELECT d.ID_desenvolvedor, c.nome, c.email, d.descricao, d.portfolio
        FROM desenvolvedor d
        INNER JOIN cliente c ON d.ID_cliente = c.ID_cliente
        WHERE d.status = 1
        ORDER BY d.ID_desenvolvedor DESC";

$result = $conn->query($sql);

// Verificar sessão do usuário
$usuario = isset($_SESSION['usuario_id']);
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Desenvolvedor</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
  <link rel="shortcut icon" href="../Img/Logo SJ.png" type="image/png">
  <link rel="stylesheet" href="Desenvolvedor.css">
</head>
<body>
  
<header class="navbar">
  <div class="left-side">
    <div class="logo">
      <img src="../Img/Logo SJ.png" alt="Logo">
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
            <img src="../Img/Perfil.png" alt="Perfil">
          </a>
      <?php else: ?>
          <a href="../Login/Login.php">
            <img src="../Img/Perfil.png" alt="Entrar">
          </a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main>
  <div class="container">
    <h1>Apoio ao Desenvolvedor</h1>
    <br><br>
    
    <div class="card-bg">
      <p>Na Skull Jabb, acreditamos que o apoio ao desenvolvedor é fundamental para o sucesso de nossos projetos e para a inovação no setor de jogos. Nossa missão é criar um ambiente colaborativo que estimule a criatividade e a troca de ideias.</p>
      <a href="inscrever_desenvolvedor.php"><button>Inscrever-se</button></a>
    </div>

    <div class="cards">
      <div class="card">
        <h2>Canal De Comunicação</h2>
        <ul>
          <li>Acesso a ferramentas de desenvolvimento e design, como software de modelagem 3D e motores de jogo (Unity, Unreal).</li>
        </ul>
      </div>

      <div class="card">
        <h2>Suporte Financeiro</h2>
        <ul>
          <li>Possibilidade de financiamento para projetos promissores ou protótipos.</li>
          <li>Parcerias com plataformas de crowdfunding para financiar novos jogos.</li>
        </ul>
      </div>

      <div class="card">
        <h2>Recursos e Ferramentas</h2>
        <ul>
          <li>Licenças de software essenciais e recursos de aprendizado.</li>
          <li>Acesso a ferramentas de desenvolvimento e design.</li>
        </ul>
      </div>
    </div>

    <h2 style="margin:40px 0 20px; font-family:'Orbitron', sans-serif; text-align:center; color:white;">
      Desenvolvedores Aprovados
    </h2>

    <div class="cards">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="card">
            <h2><?php echo htmlspecialchars($row['nome']); ?></h2>
            <p><?php echo htmlspecialchars($row['email']); ?></p>
            <?php if (!empty($row['descricao'])): ?>
              <p><?php echo nl2br(htmlspecialchars($row['descricao'])); ?></p>
            <?php endif; ?>
            <?php if (!empty($row['portfolio'])): ?>
              <a href="<?php echo htmlspecialchars($row['portfolio']); ?>" target="_blank">Ver Portfólio</a>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="color:white; text-align:center; width:100%;">Nenhum desenvolvedor aprovado ainda.</p>
      <?php endif; ?>
    </div>
  </div>
</main>

<footer>
  <div class="social-icons">
    <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
    <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
    <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline"></i></a>
  </div>
  <p>© 2025 Skull Jabb - Todos os direitos reservados.</p>
</footer>

<script src="Desenvolvedor.js"></script>
</body>
</html>
