<?php
require_once "../Conexao/Conexao.php";

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