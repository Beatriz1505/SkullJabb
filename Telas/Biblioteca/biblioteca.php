<?php
<<<<<<< HEAD
$jogos = [
    ["nome" => "GRIS", "horas" => 6.5, "img" => "../../Img/Jogos/gris.png"],
    ["nome" => "Unpacking", "horas" => 8.5, "img" => "../../Img/Jogos/unpacking.png"],
    ["nome" => "Oknytt", "horas" => 8.0, "img" => "../../Img/Jogos/oknytt.png"],
    ["nome" => "Bakery Simulator", "horas" => 30.0, "img" => "../../Img/Jogos/bakery.png"],
    ["nome" => "Bus Simulator 21", "horas" => 60.0, "img" => "../../Img/Jogos/bus.png"],
    ["nome" => "BeamNG.drive", "horas" => 200.0, "img" => "../../Img/Jogos/car.png"],
    ["nome" => "Cuphead", "horas" => 20.0, "img" => "../../Img/Jogos/cuphead.png"],
    ["nome" => "Cult of the Lamb", "horas" => 25.0, "img" => "../../Img/Jogos/cult_of_the_lamb.png"],
    ["nome" => "Buckshot Roulette", "horas" => 3.0, "img" => "../../Img/Jogos/buckshot_roulette.png"],
    ["nome" => "Goat Simulator", "horas" => 15.0, "img" => "../../Img/Jogos/goat.png"],
    ["nome" => "Kyora", "horas" => 7.0, "img" => "../../Img/Jogos/Kyora.png"],
];

$recentes = [
    ["nome" => "Unpacking", "horas" => 8.1, "img" => "../../Img/Jogos/unpacking.png"],
    ["nome" => "GRIS", "horas" => 5.2, "img" => "../../Img/Jogos/gris.png"],
    ["nome" => "Five Night at Freddy's", "horas" => 9.0, "img" => "../../Img/Jogos/into_the_pit.png"],
    ["nome" => "Cuphead", "horas" => 16.5, "img" => "../../Img/Jogos/cuphead.png"],
    ["nome" => "Cult of the Lamb", "horas" => 22.7, "img" => "../../Img/Jogos/cult_of_the_lamb.png"],
    ["nome" => "Buckshot Roulette", "horas" => 2.0, "img" => "../../Img/Jogos/buckshot_roulette.png"],
    ["nome" => "Goat Simulator", "horas" => 12.4, "img" => "../../Img/Jogos/goat.png"],
    ["nome" => "Kyora", "horas" => 6.0, "img" => "../../Img/Jogos/Kyora.png"],
];
?>


<!DOCTYPE html>
<html lang="en">


<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conquistas</title>
    <link rel="stylesheet" href="biblioteca.css">
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
      <input type="text" placeholder="Procurar...">
      <a href="#"><i class="mdi mdi-magnify search-icon"></i></a alt="Pesquisar">
    </div>
  </div>


    <nav class="nav-links">
      <a href="#">Home</a>
      <a href="#">Loja</a>
      <a href="#">Suporte</a>
    </nav>

    <div class="icons">
      <a href="#"><i class="mdi mdi-cart icone"></i></a>
      <div class="profile">
        <a href="#"><img src="../../Img/Perfis/Artist_.jpeg" alt="Perfil"></a>
      </div>
    </div>
  </header>
=======
// Simulação de dados (depois você puxa do banco)
$jogos = [
    ["nome" => "GRIS", "horas" => 12.8, "img" => "img/gris.jpg"],
    ["nome" => "Undertale", "horas" => 24.6, "img" => "img/undertale.jpg"],
    ["nome" => "Unpacking", "horas" => 60.8, "img" => "img/unpacking.jpg"],
    ["nome" => "Anomaly 2", "horas" => 0.0, "img" => "img/anomaly2.jpg"],
    ["nome" => "Oknytt", "horas" => 12.8, "img" => "img/oknytt.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
];

$recentes = [
    ["nome" => "Unpacking", "horas" => 60.8, "img" => "img/unpacking.jpg"],
    ["nome" => "GRIS", "horas" => 12.8, "img" => "img/gris.jpg"],
    ["nome" => "Five Night at Freddy's", "horas" => 37.8, "img" => "img/fnaf.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
    ["nome" => "Hollow Knight", "horas" => 109.8, "img" => "img/hollow.jpg"],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Biblioteca</title>
    <link rel="stylesheet" href="biblioteca.css">
</head>
<body>

<!-- Cabeçalho -->
<header class="topo">
    <div class="logo">🎮 SKULL JABB</div>
    <input type="text" placeholder="Procurar...">
    <nav>
        <a href="#">Home</a>
        <a href="#">Loja</a>
        <a href="#">Suporte</a>
        <a href="#">🛒</a>
    </nav>
</header>
>>>>>>> f42ab21f7456bcc81b1e446ecdd7d41122e23459

<!-- Container principal -->
<div class="container">
    <!-- Lista lateral -->
    <aside class="lista-jogos">
        <h2>JOGOS</h2>
        <ul>
            <?php foreach($jogos as $j): ?>
                <li>
                    <img src="<?= $j['img'] ?>" alt="<?= $j['nome'] ?>">
                    <div>
                        <span><?= $j['nome'] ?></span><br>
                        <small>Horas: <?= $j['horas'] ?></small>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <!-- Jogados recentemente -->
    <section class="recentes">
        <h2>JOGADOS RECENTEMENTE</h2>
        <div class="grid-recentes">
            <?php foreach($recentes as $r): ?>
                <div class="jogo-recente">
                    <img src="<?= $r['img'] ?>" alt="<?= $r['nome'] ?>">
                    <h3><?= $r['nome'] ?></h3>
                    <p>Horas: <?= $r['horas'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<!-- Rodapé -->
<<<<<<< HEAD
<footer>
  <div class="social-icons">
    <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
    <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
    <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline"></i></a>
  </div>
  <p>© 2024 Skull Jabb - Todos os direitos reservados.</p>
=======
<footer class="rodape">
    <p>© 2024 Skull Jabb - Todos os direitos reservados.</p>
>>>>>>> f42ab21f7456bcc81b1e446ecdd7d41122e23459
</footer>

</body>
</html>
