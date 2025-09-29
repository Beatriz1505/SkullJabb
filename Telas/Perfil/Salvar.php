<?php
session_start();
require_once "ClasseModelagemPerfil.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Login/Login.php");
    exit;
}

$id = $_SESSION['usuario_id'];

// Buscar o perfil atual
$perfil = Perfil::buscarPorId($id);
if (!$perfil) {
    echo "Perfil não encontrado!";
    exit;
}

// Atualizar dados do formulário
$perfil->nome    = $_POST['nome'] ?? $perfil->nome;
$perfil->usuario = $_POST['usuario'] ?? $perfil->usuario;
$perfil->moldura = $_POST['moldura'] ?? $perfil->moldura;

// Upload da foto (se enviado)
if (!empty($_FILES['foto']['name'])) {
    $diretorio = "../../Img/Perfis/";
    $caminhoFoto = $diretorio . basename($_FILES["foto"]["name"]);

    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $caminhoFoto)) {
        $perfil->foto = $caminhoFoto;
    }
}

if ($perfil->salvar()) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['user']['foto']    = $perfil->foto;
    $_SESSION['user']['moldura'] = $perfil->moldura;
    header("Location: Perfil.php");
    exit;
} else {
    echo "Erro ao atualizar perfil!";
}

// Salvar no banco
if ($perfil->salvar()) {
    header("Location: Perfil.php");
    exit;
} else {
    echo "Erro ao atualizar perfil!";
}
