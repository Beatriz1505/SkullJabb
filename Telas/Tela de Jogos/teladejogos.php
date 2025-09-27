<?php
session_start(); // INICIA SESSÃO

// ===== CONEXÃO COM BANCO =====
$host = "localhost";
$user = "root";   
$pass = "";
$db = "skulljabb";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// ===== PEGA O ID DO JOGO =====
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ===== BUSCA O JOGO + GÊNEROS =====
$sql = "
    SELECT 
        j.id_jogo,
        j.nome,
        j.preco,
        j.desconto,
        j.img,
        GROUP_CONCAT(g.nome ORDER BY g.nome SEPARATOR ', ') AS generos
    FROM jogo AS j
    LEFT JOIN jogo_genero AS jg ON j.id_jogo = jg.id_jogo
    LEFT JOIN genero AS g ON jg.id_gen = g.id_gen
    WHERE j.id_jogo = ?
    GROUP BY j.id_jogo, j.nome, j.preco, j.desconto, j.img
";

// Prepara a query
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

// Pega o resultado
$result = $stmt->get_result();
$jogo = $result->fetch_assoc(); // agora usamos $jogo direto

$stmt->close();

if (!$jogo) {
    die("Jogo não encontrado.");
}

// ===== CALCULA DESCONTO SE HOUVER =====
$preco = $jogo['preco'];
$desconto = $jogo['desconto'];
$precoFinal = $desconto ? $preco - ($preco * ($desconto / 100)) : $preco;

// ===== VERIFICA SE O USUÁRIO ESTÁ LOGADO =====
$id_cliente = isset($_SESSION['id_cliente']) ? $_SESSION['id_cliente'] : 0;

// ===== BOTÕES DE AÇÃO =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!$id_cliente) {
        header("Location: teladejogo.php?id=$id&msg=Você precisa fazer login ou cadastrar-se");
        exit;
    }

    if (isset($_POST['add_carrinho'])) {
        $check = $conn->query("SELECT * FROM carrinho WHERE ID_cliente=$id_cliente AND ID_jogo=$id");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE carrinho SET quantidade = quantidade + 1 WHERE ID_cliente=$id_cliente AND ID_jogo=$id");
        } else {
            $conn->query("INSERT INTO carrinho (ID_cliente, ID_jogo, quantidade) VALUES ($id_cliente, $id, 1)");
        }
        header("Location: teladejogo.php?id=$id&msg=Adicionado ao carrinho");
        exit;
    }

    if (isset($_POST['add_lista'])) {
        $check = $conn->query("SELECT * FROM lista_desejo WHERE ID_cliente=$id_cliente AND ID_jogo=$id");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO lista_desejo (ID_cliente, ID_jogo) VALUES ($id_cliente, $id)");
        }
        header("Location: teladejogo.php?id=$id&msg=Adicionado à lista de desejos");
        exit;
    }

    if (isset($_POST['comprar'])) {
        $stmt = $conn->prepare("INSERT INTO resumo_pedido (ID_cliente, total, status, metodo_pagamento) VALUES (?, ?, 'pendente', 'pix')");
        $stmt->bind_param("id", $id_cliente, $precoFinal);
        $stmt->execute();
        $id_pedido = $stmt->insert_id;
        $conn->query("INSERT INTO pedido_item (ID_pedido, ID_jogo, quantidade) VALUES ($id_pedido, $id, 1)");
        header("Location: confirmacao.php?pedido=$id_pedido");
        exit;
    }

    if (isset($_POST['avaliar'])) {
        $nota = intval($_POST['nota']);
        $comentario = $conn->real_escape_string($_POST['comentario']);

        $sql = "INSERT INTO avaliacao (ID_cliente, ID_jogo, nota, comentario) 
                VALUES ($id_cliente, $id, $nota, '$comentario')";
        $conn->query($sql);

        header("Location: teladejogo.php?id=$id&msg=Avaliação enviada com sucesso!");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">


<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($jogo['nome']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="teladejogo.css">
    <link rel="shortcut icon" href="../../Img/Elementos/Logo_SJ.png"sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap" rel="stylesheet">

</head>


<body>

    <div class="content">

<header class="navbar">
  <div class="left-side">
    <div class="logo">
      <a href="#"><img src="../../Img/Elementos/Logo_SJ.png" alt="Caveira branca com capuz azul"></a>
      <a class="lin" href=""><span>SKULL<br>JABB</span></a>
    </div>
    <div class="search">
      <input type="text" name="buscar" placeholder="Procurar...">
      <a href="#"><i class="mdi mdi-magnify search-icon"></i></a>
    </div>
  </div>
  <nav class="nav-links">
      <a href="home.php">Home</a>
      <a href="loja.php">Loja</a>
      <a href="suporte.php">Suporte</a>
  </nav>
  <div class="icons">
    <a href="../carrinho/carrinho.php"><i class="mdi mdi-cart icone"></i></a>
    <div class="profile">

 <!-- Adicionar o perfil aqui-->   
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

<!-- CONTEÚDO -->
<main class="container">
    <div class="card-jogo">
        <div class="img-jogo">
            <img src="<?= htmlspecialchars($jogo['img']) ?>" alt="<?= htmlspecialchars($jogo['nome']) ?>">
        </div>
        <div class="game-info">
            <h2 class="game-title"><?= htmlspecialchars($jogo['nome']) ?></h2>
            <p><strong>Gênero:</strong> <?= htmlspecialchars($jogo['generos'] ?? 'Não informado') ?></p>

            <div class="price-box">
                <?php if ($jogo['desconto'] > 0): ?>
                    <span class="old-price">R$<?= number_format($jogo['preco'], 2, ',', '.') ?></span>
                    <span class="discounted-price">R$<?= number_format($precoFinal, 2, ',', '.') ?></span>
                    <span class="discount">-<?= $jogo['desconto'] ?>%</span>
                <?php else: ?>
                    <span class="discounted-price">R$<?= number_format($jogo['preco'], 2, ',', '.') ?></span>
                <?php endif; ?>
            </div>

            <div class="btn-box">
                <form method="POST" style="display:inline;">
                    <button type="submit" name="comprar" class="buy-btn">Comprar</button>
                </form>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="add_lista" class="wishlist-btn">Lista de desejos</button>
                </form>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="add_carrinho" class="cart-btn">Carrinho</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Descrição -->
    <div class="game-description">
        <h3>Descrição</h3>
        <p><?= htmlspecialchars($jogo['nome']) ?> é um jogo do gênero <?= strtolower(htmlspecialchars($jogo['generos'] ?? 'não informado')) ?> disponível na nossa loja. Aproveite para jogar com desconto exclusivo se disponível!</p>

        <?php
        $sqlMedia = "SELECT AVG(nota) as media, COUNT(*) as total FROM avaliacao WHERE ID_jogo = $id";
        $resMedia = $conn->query($sqlMedia);
        $dadosMedia = $resMedia->fetch_assoc();

        if ($dadosMedia['total'] > 0) {
            $media = number_format($dadosMedia['media'], 1, ',', '.');
            echo "<p><strong>Média de avaliações:</strong> ⭐ $media / 5 ({$dadosMedia['total']} avaliações)</p>";
        } else {
            echo "<p><strong>Média de avaliações:</strong> Este jogo ainda não foi avaliado.</p>";
        }
        ?>
    </div>

    <!-- Parte inferior -->
    <div class="bottom-section">
        <!-- Avaliações -->
        <div class="chat-box">
            <h3>Avaliações</h3>

            <div class="messages">
                <?php
                $sqlAval = "SELECT a.comentario, a.nota, a.data_avaliacao, c.nome 
                            FROM avaliacao a
                            JOIN cliente c ON a.ID_cliente = c.ID_cliente
                            WHERE a.ID_jogo = $id
                            ORDER BY a.data_avaliacao DESC";
                $resAval = $conn->query($sqlAval);

                if ($resAval->num_rows > 0) {
                    while ($row = $resAval->fetch_assoc()) {
                        echo "<p><strong>{$row['nome']} ({$row['nota']}/5):</strong> {$row['comentario']} 
                              <br><small>{$row['data_avaliacao']}</small></p>";
                    }
                } else {
                    echo "<p>Este jogo ainda não possui avaliações. Seja o primeiro!</p>";
                }
                ?>
            </div>

            <?php if ($id_cliente): ?>
            <form class="chat-form" method="POST">
                <textarea name="comentario" placeholder="Escreva sua avaliação..." required></textarea>
                <select name="nota" required>
                    <option value="">Nota</option>
                    <option value="1">1 - Péssimo</option>
                    <option value="2">2 - Ruim</option>
                    <option value="3">3 - Mediano</option>
                    <option value="4">4 - Bom</option>
                    <option value="5">5 - Excelente</option>
                </select>
                <button type="submit" name="avaliar">Enviar</button>
            </form>
            <?php else: ?>
                <p><em>Faça login para deixar sua avaliação.</em></p>
            <?php endif; ?>
        </div>

        <?php if ($id_cliente): ?>
        <div class="achievements-box">
            <h3>Conquistas</h3>
            <ul>
                <li><strong>Primeiro Chefe:</strong> Derrote o primeiro boss</li>
                <li><strong>Perfeição:</strong> Vença sem tomar dano</li>
                <li><strong>Velocista:</strong> Termine uma fase em menos de 2 minutos</li>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- FOOTER -->
<footer>
    <div class="social-icons">
        <a href="#"><i class="mdi mdi-instagram"></i></a>
        <a href="#"><i class="mdi mdi-whatsapp"></i></a>
        <a href="#"><i class="mdi mdi-email-outline"></i></a>
    </div>
    <p>© 2025 Skull Jabb - Todos os direitos reservados.</p>
</footer>

</body>
</html>
