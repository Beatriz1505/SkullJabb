<?php
require_once "../Conexao/Conexao.php";

class Jogo {
    public $id_jogo;
    public $nome;
    public $img;

    // Lista jogos aleatórios (usado nos carrosséis)
    public static function listarAleatorio($limite = 8) {
        $conn = Conexao::getConexao();
        $sql = "SELECT id_jogo, nome, img 
                FROM jogo 
                WHERE id_jogo NOT IN (13,30,22,38,32,41) 
                ORDER BY RAND() 
                LIMIT $limite";
        $result = $conn->query($sql);

        $jogos = [];
        while ($row = $result->fetch_assoc()) {
            $jogo = new Jogo();
            $jogo->id_jogo = $row['id_jogo'];
            $jogo->nome    = $row['nome'];
            $jogo->img     = $row['img'];
            $jogos[] = $jogo;
        }
        return $jogos;
    }

public static function buscarPorId($id) {
    $conn = Conexao::getConexao();
    $sql = "SELECT id_jogo, nome, img FROM jogo WHERE id_jogo = $id";
    $result = $conn->query($sql);

    if ($row = $result->fetch_assoc()) {
        $jogo = new Jogo();
        $jogo->id_jogo = $row['id_jogo'];
        $jogo->nome    = $row['nome'];
        $jogo->img     = $row['img'];
        return $jogo;
    }
    return null;
}

}

class Genero {
    public $id_gen;
    public $nome;

    public static function listarTodos() {
        $conn = Conexao::getConexao();
        $sql = "SELECT id_gen, nome FROM genero ORDER BY nome ASC";
        $result = $conn->query($sql);

        $generos = [];
        while ($row = $result->fetch_assoc()) {
            $g = new Genero();
            $g->id_gen = $row['id_gen'];
            $g->nome   = $row['nome'];
            $generos[] = $g;
        }
        return $generos;
    }
}
?>
