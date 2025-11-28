<?php
session_start();
require_once "../Conexao/Conexao.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Login/Login.php");
    exit;
}

require_once "../Perfil/ClasseModelagemPerfil.php";
$perfil = Perfil::buscarPorId($_SESSION['usuario_id']);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - Skull Jabb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Sobre_Nós.css">
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

      
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
         <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>new window.VLibras.Widget('https://vlibras.gov.br/app');</script>

    <br/>
    <!-- Info -01 -->
     <div class="container-1">
        <div class="Lado-Esq">
            <h3>Sobre nós</h3>
            <p>No Skull Jabb, nossa missão é transformar o mundo dos games, dando voz e espaço 
                aos pequenos desenvolvedores e promovendo uma acessibilidade real. Acreditamos 
                que os jogos devem ser para todos, e por isso focamos em criar experiências que 
                sejam acessíveis financeiramente e inclusivas para pessoas com deficiência ou fobias 
                específicas, como daltonismo e aracnofobia.</p>
            
            <p>Com um foco especial em jogos indies, buscamos destacar o trabalho criativo de desenvolvedores 
                independentes, ajudando-os a compartilhar suas criações com o mundo. Nosso compromisso é 
                proporcionar um ambiente onde qualquer pessoa possa encontrar e desfrutar de jogos únicos, 
                inovadores e acessíveis.</p>
            
            <p>Acreditamos no poder dos games para conectar pessoas e criar novas histórias, e queremos que essas 
                histórias sejam para todos, sem barreiras.</p>
                
            
        </div>

        <div class="Lado-Dir">
            <div class="ret-01">
                <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi2 mdi-instagram icone"></i>
                <h4>@Skulljabb</h4>
                </a>
            </div>
            <br/>
            <div class="ret-02">
                <a href="https://www.instagram.com/skulljabb/" target="_blank"><i class="mdi mdi-email-outline"></i>
                <h4>SkullJabb@gmail.com</h4>
                </a>
            </div>
            <br/>
            <div class="img1">
                 <img src="../../Img/Elementos/Logo SJ.png" alt="Logo">
            </div>
        </div>

     </div>
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