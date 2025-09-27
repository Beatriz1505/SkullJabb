<?php
session_start();

// ===== CONEXÃO COM BANCO =====
$host = "localhost";
$user = "root";   
$pass = "";
$db = "skulljabb";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// ===== PEGA O ID DO GÊNERO DA URL =====
$idGenero = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ===== BUSCA O NOME DO GÊNERO =====
$sqlGenero = "SELECT Nome FROM genero WHERE Id_Gen = $idGenero";
$resGenero = $conn->query($sqlGenero);
$nomeGenero = ($resGenero && $resGenero->num_rows > 0) ? $resGenero->fetch_assoc()['Nome'] : "Desconhecido";

// ===== BUSCA O PRIMEIRO JOGO DO GÊNERO (PARA O BANNER) =====
$sqlBanner = "
    SELECT j.ID_jogo, j.nome, j.Img
    FROM jogo j
    INNER JOIN jogo_genero jg ON j.ID_jogo = jg.ID_jogo
    WHERE jg.Id_Gen = $idGenero
    LIMIT 1
";
$resBanner = $conn->query($sqlBanner);
$bannerJogo = ($resBanner && $resBanner->num_rows > 0) ? $resBanner->fetch_assoc() : null;

// ===== BUSCA TODOS OS JOGOS DO GÊNERO (PARA O CARROSSEL) =====
$sqlJogos = "
    SELECT j.ID_jogo, j.nome, j.preco, j.desconto, j.Img
    FROM jogo j
    INNER JOIN jogo_genero jg ON j.ID_jogo = jg.ID_jogo
    WHERE jg.Id_Gen = $idGenero
";
$resJogos = $conn->query($sqlJogos);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nomeGenero ?> - Skull Jabb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Genero.css">
    <link rel="shortcut icon" href="../../Img/Elementos/Logo_SJ.png" sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
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
      <!-- Perfil futuramente -->
    </div>
  </div>
</header>
</div>

<div class="container">

<!-- Banner principal -->
<section class="py-5">
  <section class="banner">
    <div class="banner-info">
      <div class="text-group">
        <h2><?= $nomeGenero ?></h2>
        <?php if ($bannerJogo): ?>
          <a href="detalhes_jogo.php?id=<?= $bannerJogo['ID_jogo'] ?>">
            <button class="descubra-btn">Descubra</button>
          </a>
        <?php endif; ?>
      </div>
    </div>
    <div class="banner-img">
      <?php if ($bannerJogo): ?>
        <img src="<?= $bannerJogo['Img'] ?>" alt="<?= $bannerJogo['nome'] ?>">
      <?php else: ?>
        <img src="../../Img/Elementos/sem_capa.png" alt="Sem capa disponível">
      <?php endif; ?>
    </div>
  </section>
</section>

<!-- Botões de navegação -->
<section class="nave-buttons">
  <button>Loja de pontos</button>
  <button>Categorias</button>
  <button>Informações</button>
</section>

<!-- Carrossel de jogos -->
<section class="mb-5">
  <h3 class="Car"><?= $nomeGenero ?></h3>
  <div id="carouselGenero" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">

      <?php 
      if ($resJogos && $resJogos->num_rows > 0):
          $i = 0;
          while ($jogo = $resJogos->fetch_assoc()):
              if ($i % 3 == 0): ?>
                  <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>">
                    <div class="d-flex justify-content-center gap-5">
              <?php endif; ?>

                      <a href="detalhes_jogo.php?id=<?= $jogo['ID_jogo'] ?>">
                        <img class="jogos" src="<?= $jogo['Img'] ?>" alt="<?= $jogo['nome'] ?>">
                      </a>

              <?php if ($i % 3 == 2 || $i == $resJogos->num_rows - 1): ?>
                    </div>
                  </div>
              <?php endif; ?>
          <?php $i++; endwhile;
      else: ?>
          <p class="text-center">Nenhum jogo encontrado para este gênero.</p>
      <?php endif; ?>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#carouselGenero" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    
    <button class="carousel-control-next" type="button" data-bs-target="#carouselGenero" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>
</section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
