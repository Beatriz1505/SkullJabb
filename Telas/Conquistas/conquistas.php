<?php
session_start();
require_once "../Conexao/Conexao.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Login/Login.php");
    exit;
}

require_once "../Perfil/ClasseModelagemPerfil.php";
require_once "classemodelagemconquistas.php";

$perfil = Perfil::buscarPorId($_SESSION['usuario_id']);

$idJogo = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Banner + nome
$dadosJogo = Conquista::buscarBanner($idJogo);

// Ordenação
$ordem = isset($_GET['ordem']) ? $_GET['ordem'] : "az";

// Lista de conquistas
$conquistas = Conquista::listarPorJogo($idJogo, $ordem);

// Progresso (SEM concluida)
$total = count($conquistas);
$feitas = 0;
$porcentagem = $total > 0 ? number_format(($feitas / $total) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conquistas</title>
    <link rel="stylesheet" href="Conquistas.css">
    <link rel="shortcut icon" href="../../Img/Elementos/Logo SJ.png" sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@500&display=swap" rel="stylesheet">
</head>

<body>

<div class="content">
    <header class="navbar">
        <div class="left-side">
            <div class="logo">
                <a href="../Home/Home.php"><img src="../../Img/Elementos/Logo SJ.png" alt="Logo Skull Jabb"></a>
                <a class="lin" href="#"><span>SKULL<br>JABB</span></a>
            </div>

            <div class="search">
                <input type="text" placeholder="Procurar...">
                <a href="#"><i class="mdi mdi-magnify search-icon"></i></a>
            </div>
        </div>

        <nav class="nav-links">
            <a href="../Home/Home.php">Home</a>
            <a href="../Loja/Loja.php">Loja</a>
            <a href="../Suporte/Suporte.php">Suporte</a>
        </nav>

        <div class="icons">
            <a href="../Carrinho/Carrinho.php"><i class="mdi mdi-cart icone"></i></a>
            <div class="profile">
                <a href="../Perfil/Perfil.php">
                    <img src="<?= $perfil->foto ? $perfil->foto : '../../Img/Elementos/user.png' ?>" alt="Perfil">
                </a>
            </div>
        </div>
    </header>
</div>

<!-- BANNER -->
<div class="banner">
    <img src="<?= $dadosJogo['banner'] ?>" alt="Banner do jogo">
    <h1 class="titulo-jogo"><?= $dadosJogo['nome_jogo'] ?></h1>
</div>

<main class="conquistas-container">

    <div class="status-bar">

        <div class="caixa">Seu progresso: <span><?= $feitas ?>/<?= $total ?></span></div>
        <div class="caixa"><?= $porcentagem ?>%</div>

<div class="filtro-container">
    <button type="button" class="botao-filtro" id="btnFiltro">
        Ordenar <i class="mdi mdi-filter-outline"></i>
    </button>

    <div class="menu-filtro" id="menuFiltro">
        <a href="conquistas.php?id=<?= $idJogo ?>&ordem=mais_pontos">
            <button type="button">Mais pontos</button>
        </a>

        <a href="conquistas.php?id=<?= $idJogo ?>&ordem=menos_pontos">
            <button type="button">Menos pontos</button>
        </a>

        <a href="conquistas.php?id=<?= $idJogo ?>&ordem=az">
            <button type="button">A - Z</button>
        </a>
    </div>
</div>

    </div>
</div>


    </div>

    <!-- CARDS -->
    <div class="cards">
        <?php foreach ($conquistas as $c): ?>
        <div class="card">
            <img src="<?= $c['img'] ?>" alt="<?= $c['nome'] ?>">

            <div class="info">
                <h2><?= htmlspecialchars($c["nome"]) ?></h2>

                <p><?= $c['descricao'] ?></p>

                <span class="preco">
                    <img src="../../Img/Elementos/SJ.png" alt="Moedas SJ">
                    <?= $c['pontos'] ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</main>

<!-- VLibras -->
<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
        <div class="vw-plugin-top-wrapper"></div>
    </div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script> new window.VLibras.Widget('https://vlibras.gov.br/app'); </script>

<footer>
    <div class="social-icons">
        <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
        <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
        <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline"></i></a>
    </div>
    <p>© 2024 Skull Jabb - Todos os direitos reservados.</p>
</footer>

<script src="conquistas.js"></script>

</body>
</html>
