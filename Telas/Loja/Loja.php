<?php
session_start();
require_once "../Conexao/Conexao.php";
require_once "ClasseModelagemLoja.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Login/Login.php");
    exit;
}

require_once "../Perfil/ClasseModelagemPerfil.php";
$perfil = Perfil::buscarPorId($_SESSION['usuario_id']);

$generos = Genero::listarTodos();
$banner = Jogo::buscarPorId(11);
$recomendados = Jogo::listarAleatorio(8);
$populares = Jogo::listarAleatorio(8);
$staff = Jogo::listarAleatorio(8);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja - Skull Jabb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Loja.css">
    <link rel="shortcut icon" href="../../Img/Elementos/Logo SJ.png" sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="content">

        <!-- Header -->
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
                <a href="../Home/Home.php">Home</a> 
                <a class="grif" href="loja.php">Loja</a>
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
    </div>

    <!-- VLibras -->
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>new window.VLibras.Widget('https://vlibras.gov.br/app');</script>

    <div class="container">

        <!-- Banner principal -->
<section class="py-5">
<section class="banner">
    <div class="banner-info">
        <div class="text-group">
            <h2><?= $banner->nome ?></h2>
            <a href="../Tela de Jogos/teladejogos.php?id=<?= $banner->id_jogo ?>">
                <button class="descubra-btn">Descubra</button>
            </a>
        </div>
    </div>
    <div class="banner-img">
        <img src="<?= $banner->img ?>" alt="<?= $banner->nome ?>">
    </div>
</section>
</section>

        <!-- Atalhos -->
<section class="nave-buttons">
    <a href="../Loja de Pontos/lojadepontos.php"><button>Loja de pontos</button></a>

    <div class="dropdown">
        <button class="dropbtn">Categorias</button>
        <div class="dropdown-content">
            <?php foreach ($generos as $g): ?>
                <a href="../Genero/Genero.php?id=<?= $g->id_gen ?>">
                    <?= $g->nome ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <a href=""><button>Informações</button></a>
</section>


        <!-- Recomendado -->
        <section class="mb-5">
            <h3 class="Car">Recomendado para você</h3>
            <div id="carouselRecomendado" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">

                    <div class="carousel-item active">
                        <div class="d-flex justify-content-center gap-5">
                            <?php foreach (array_slice($recomendados, 0, 4) as $jogo): ?>
                                <a href="../Tela de Jogos/teladejogos.php?id=<?= $jogo->id_jogo ?>">
                                    <img class="jogos" src="<?= $jogo->img ?>" alt="<?= $jogo->nome ?>">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="d-flex justify-content-center gap-5">
                            <?php foreach (array_slice($recomendados, 4, 4) as $jogo): ?>
                                <a href="../Tela de Jogos/teladejogos.php?id=<?= $jogo->id_jogo ?>">
                                    <img class="jogos" src="<?= $jogo->img ?>" alt="<?= $jogo->nome ?>">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselRecomendado" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselRecomendado" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </section>

        <br><br>

        <!-- Mais Populares -->
        <section class="mb-5">
            <h3 class="Car">Mais populares</h3>
            <div id="carouselPopulares" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">

                    <div class="carousel-item active">
                        <div class="d-flex justify-content-center gap-5">
                            <?php foreach (array_slice($populares, 0, 4) as $jogo): ?>
                                <a href="../Tela de Jogos/teladejogos.php?id=<?= $jogo->id_jogo ?>">
                                    <img class="jogos" src="<?= $jogo->img ?>" alt="<?= $jogo->nome ?>">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="d-flex justify-content-center gap-5">
                            <?php foreach (array_slice($populares, 4, 4) as $jogo): ?>
                                <a href="../Tela de Jogos/teladejogos.php?id=<?= $jogo->id_jogo ?>">
                                    <img class="jogos" src="<?= $jogo->img ?>" alt="<?= $jogo->nome ?>">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselPopulares" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselPopulares" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </section>

        <!-- Escolha da Staff -->
        <section class="py-5">
            <h3 class="Car">Escolha da Staff</h3>
            <div id="carouselStaff" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">

                    <div class="carousel-item active">
                        <div class="d-flex justify-content-center gap-5">
                            <?php foreach (array_slice($staff, 0, 4) as $jogo): ?>
                                <a href="../Tela de Jogos/teladejogos.php?id=<?= $jogo->id_jogo ?>">
                                    <img class="jogos" src="<?= $jogo->img ?>" alt="<?= $jogo->nome ?>">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="d-flex justify-content-center gap-5">
                            <?php foreach (array_slice($staff, 4, 4) as $jogo): ?>
                                <a href="../Tela de Jogos/teladejogos.php?id=<?= $jogo->id_jogo ?>">
                                    <img class="jogos" src="<?= $jogo->img ?>" alt="<?= $jogo->nome ?>">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselStaff" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselStaff" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </section>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

<footer>
  <div class="social-icons">
    <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
    <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
    <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline"></i></a>
  </div>
  <p>© 2024 Skull Jabb - Todos os direitos reservados.</p>
</footer>
</html>
