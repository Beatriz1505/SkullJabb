<?php
session_start();
$_SESSION['ID_cliente'] = 1; // teste sem login

$ID_cliente = $_SESSION['ID_cliente'] ?? null;
if(!$ID_cliente){ 
    echo "Usuário não logado!"; 
    exit; 
}

include_once 'Lista_Desejo.php';
$lista = new Lista_Desejo($ID_cliente);

$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);

// Dropdown de ordenação
$ordenar = filter_input(INPUT_GET, 'ordenar', FILTER_SANITIZE_STRING) ?? '';
$order_by = '';

switch($ordenar){
    case 'nome_asc':
        $order_by = 'ORDER BY j.nome ASC';
        break;
    case 'preco_asc':
        $order_by = 'ORDER BY j.preco ASC';
        break;
    case 'preco_desc':
        $order_by = 'ORDER BY j.preco DESC';
        break;
    case 'desconto_desc':
        $order_by = 'ORDER BY j.desconto DESC';
        break;
    default:
        $order_by = 'ORDER BY j.ID_jogo ASC';
}

// Ações
if(isset($_GET['remover'])){
    $lista->setID_jogo($_GET['remover']);
    $lista->remover();
}

if(isset($_GET['carrinho'])){
    $lista->setID_jogo($_GET['carrinho']);
    $resultado = $lista->adicionarCarrinho();
    
    if($resultado === true){
        $_SESSION['mensagem'] = "O Jogo foi adicionado ao carrinho!";
    } else {
        $_SESSION['mensagem'] = "O Jogo já está no carrinho!";
    }

    header("Location: Lista_De_Desejo.php?ordenar=$ordenar");
    exit;
}


// Agora o método ordenar já deve trazer também os gêneros concatenados
$jogos = $lista->ordenar($order_by);
?>


<?php
$jogos = $lista->ordenar($order_by);
?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Desejo</title>
   
    <link rel="shortcut icon" href="../../Img/Elementos/Logo SJ.png"sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Lista_Desejo.css?v=<?= time() ?>">


</head>


<body>

  <div class="content">

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
      <a class="grif" href="#">Home</a>
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
      


  
    <div class="container-jogos">
    <br/>
    <section class="info-01">
    <h2>LISTA DE DESEJOS</h2> 
    </section>
    <br/>
   <?php if($mensagem): ?>
       <p id="mensagem" class="mensagem"><?php echo $mensagem; ?></p>
   <?php endif; ?>


  <form method="get" id="ordenar-form">
  <div class="dropdown">
    <button type="button" class="dropbtn">
      Ordenar
    </button>
    <div class="dropdown-content">
      <a href="?ordenar=nome_asc" class="<?= ($ordenar=='nome_asc')?'selected':'' ?>">Nome A-Z</a>
      <a href="?ordenar=preco_asc" class="<?= ($ordenar=='preco_asc')?'selected':'' ?>">Menor Preço</a>
      <a href="?ordenar=preco_desc" class="<?= ($ordenar=='preco_desc')?'selected':'' ?>">Maior Preço</a>
      <a href="?ordenar=desconto_desc" class="<?= ($ordenar=='desconto_desc')?'selected':'' ?>">Desconto</a>
    </div>
  </div>
</form>

   
    <?php if(count($jogos) === 0): ?>
    <br/>
    <br/>
    <p class="list-void">Sua Lista de Desejos está vazia.</p>
    <br/>
    <?php else: ?>
   <?php foreach($jogos as $jogo): ?>
    <div class="card-jogo">
        <img src="<?= htmlspecialchars($jogo['Img']) ?>" alt="<?= htmlspecialchars($jogo['nome']) ?>">

        <div class="card-texto">
            <div class="col-esq">
                <h3><?= htmlspecialchars($jogo['nome']) ?></h3>
                <p class="genero"><?= htmlspecialchars($jogo['generos'] ?? 'Sem gênero') ?></p>
            </div>
            <div class="col-dir">
                <div class="espaço"></div> 

                <div class="preco-desconto">
                    <?php if($jogo['desconto']): ?>
                        <p class="desconto">-<?= $jogo['desconto'] ?>%</p>
                    <?php endif; ?>
                    <p class="preco">R$ <?= number_format($jogo['preco'],2,',','.') ?></p>
                </div>

                <div class="botoes">
                    <a href="Lista_De_Desejo.php?remover=<?= $jogo['ID_jogo'] ?>&ordenar=<?= $ordenar ?>" class="remover">Remover</a>
                    <a href="Lista_De_Desejo.php?carrinho=<?= $jogo['ID_jogo'] ?>&ordenar=<?= $ordenar ?>" class="carrinho">Carrinho+</a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php endif; ?>
</div>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
    
    const dropbtn = document.querySelector('.dropbtn');
    if(dropbtn){
        const dropdown = dropbtn.parentElement;
        dropbtn.addEventListener('click', function(e) {
            e.stopPropagation(); 
            dropdown.classList.toggle('open');
        });
        window.addEventListener('click', function() {
            dropdown.classList.remove('open');
        });
    }

    
    const mensagem = document.getElementById('mensagem');
    if(mensagem){
        setTimeout(() => {
            mensagem.style.opacity = '0';
            setTimeout(() => mensagem.remove(), 500);
        }, 3000);
    }
});
</script>


<br/>
<br/>

 <footer>
  <div class="social-icons">
    <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i></a>
    <a href="#"><i class="mdi mdi2 mdi-whatsapp icone"></i></a>
    <a href="mailto:SkullJabb@gmail.com" target="_blank"><i class="mdi mdi2 mdi-email-outline"></i></a>
  </div>
  <p>© 2025 Skull Jabb - Todos os direitos reservados.</p>
</footer>
</div>

</body>
</html>
