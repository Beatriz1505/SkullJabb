<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();
require_once "../Conexao/Conexao.php";
$conn = Conexao::getConexao();
if (!$conn) { die("Erro: sem conexão ao banco"); }

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Login/Login.php");
    exit;
}
$usuario_id = (int)$_SESSION['usuario_id'];


$usuario = $usuario_id > 0;

// REMOVER ITEM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $remove_id = (int)$_POST['remove_id'];
    $stmt = $conn->prepare("DELETE FROM carrinho WHERE ID_carrinho = ?");
    $stmt->bind_param("i", $remove_id);
    $stmt->execute();
    $stmt->close();

    header("Location: Carrinho.php");
    exit;
}

// VERIFICAR SE EXISTE TABELA DE GÊNERO
$has_jogo_genero = false;
$res_check = $conn->query("SHOW TABLES LIKE 'jogo_genero'");
if ($res_check && $res_check->num_rows > 0) {
    $has_jogo_genero = true;
}

// QUERY DO CARRINHO
if ($has_jogo_genero) {
    $sql = "SELECT c.ID_carrinho AS cart_id,
                   c.quantidade,
                   j.ID_jogo AS produto_id,
                   j.nome,
                   j.preco,
                   j.desconto,
                   j.Img,
                   GROUP_CONCAT(g.Nome SEPARATOR ', ') AS genero
            FROM carrinho c
            INNER JOIN jogo j ON c.ID_jogo = j.ID_jogo
            LEFT JOIN jogo_genero jg ON j.ID_jogo = jg.ID_jogo
            LEFT JOIN genero g ON jg.Id_Gen = g.Id_Gen
            WHERE c.ID_cliente = ?
            GROUP BY c.ID_carrinho, j.ID_jogo";
} else {
    $sql = "SELECT c.ID_carrinho AS cart_id,
                   c.quantidade,
                   j.ID_jogo AS produto_id,
                   j.nome,
                   j.preco,
                   j.desconto,
                   j.Img
            FROM carrinho c
            INNER JOIN jogo j ON c.ID_jogo = j.ID_jogo
            WHERE c.ID_cliente = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// MONTAR CARRINHO
$cart_items = [];
$total = 0.0;
while ($row = $result->fetch_assoc()) {
    $preco = (float)$row['preco'];
    $desconto = isset($row['desconto']) ? (float)$row['desconto'] : 0.0;

    if ($desconto > 0 && $desconto <= 100) {
        $preco_final = $preco - ($preco * ($desconto / 100.0));
    } else {
        $preco_final = max(0, $preco - $desconto);
    }

    $row['preco_final'] = $preco_final;
    $row['Img_path'] = $row['Img'];

    $total += $preco_final * (int)$row['quantidade'];
    $cart_items[] = $row;
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Login/Login.php");
    exit;
}

require_once "../Perfil/ClasseModelagemPerfil.php";
$perfil = Perfil::buscarPorId($_SESSION['usuario_id']);

?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Carrinho</title>
<link rel="shortcut icon" href="../../Img/Elementos/Logo SJ.png" type="image/png">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
<link rel="stylesheet" href="Carrinho.css">
</head>
<body class="img-bg">

<header class="navbar">
            <div class="left-side">
                <div class="logo">
                    <a href="../Home/home.png"><img src="../../Img/Elementos/Logo SJ.png" alt="Caveira branca com capuz azul"></a>
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
<br><br>
<main>
<div class="cart-container">
  <h2 class="cart-title">MEU CARRINHO</h2>
  <div class="cart-box">
    <div class="cart-items" id="cart-items">
      <?php if (!empty($cart_items)): ?>
        <?php foreach ($cart_items as $row): ?>
          <div class="cart-item" data-id="<?php echo $row['cart_id']; ?>" data-preco="<?php echo $row['preco_final'] * (int)$row['quantidade']; ?>">
            <img src="<?php echo htmlspecialchars($row['Img_path']); ?>" 
                  alt="<?php echo htmlspecialchars($row['nome']); ?>">
            <div class="cart-info">
              <h3><?php echo htmlspecialchars($row['nome']); ?></h3>
              <?php if (!empty($row['genero'])): ?>
                <p><?php echo htmlspecialchars($row['genero']); ?></p>
              <?php endif; ?>
              <p>Quantidade: <?php echo (int)$row['quantidade']; ?></p>
            </div>
            <div class="cart-price">
              <span>R$ <?php echo number_format($row['preco_final'] * (int)$row['quantidade'],2,',','.'); ?></span>
              <form method="post" style="display:inline;" class="remove-form">
                  <input type="hidden" name="remove_id" value="<?php echo (int)$row['cart_id']; ?>">
                  <button type="submit" class="remove-icon"><i class="mdi mdi-trash-can-outline"></i></button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Seu carrinho está vazio.</p>
      <?php endif; ?>
    </div>
    <div class="cart-summary">
      <p>Total: R$ <span id="cart-total"><?php echo number_format($total,2,',','.'); ?></span></p>
      <div class="cart-buttons">
          <form method="post">
              <a href="../Resumo Pedido/Resumo_Do_Pedido.php"><button type="submit" name="finalizar" class="blue-btn">Prosseguir</button></a>
          </form>
          <a href="../Loja/loja.php"><button type="submit" name="finalizar" class="blue-btn">Continuar Comprando</button></a>
      </div>
    </div>
  </div>
</div>
</main>
<br><br><br>
<footer>
  <div class="social-icons">
    <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
    <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
    <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline"></i></a>
  </div>
  <span><p>© 2025 Skull Jabb - Todos os direitos reservados.</p></span>
</footer>

<script>
function atualizarTotal() {
    let total = 0;
    document.querySelectorAll('#cart-items .cart-item').forEach(item => {
        total += parseFloat(item.getAttribute('data-preco'));
    });
    document.getElementById('cart-total').textContent = total.toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2});
}
</script>
</body>
</html>
