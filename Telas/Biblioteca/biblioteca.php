<?php
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
<footer class="rodape">
    <p>© 2024 Skull Jabb - Todos os direitos reservados.</p>
</footer>

</body>
</html>
