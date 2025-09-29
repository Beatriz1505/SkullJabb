<?php
require_once "../Conexao/Conexao.php"; // importa sua conexão com o banco

class Perfil {
    public $id;
    public $nome;
    public $email;
    public $pontos;
    public $usuario;
    public $foto;
    public $moldura;

    // Buscar o perfil pelo ID
    public static function buscarPorId($id) {
        $conn = Conexao::getConexao();

        $sql = "SELECT ID_cliente, nome, email, pontos, usuario, foto, moldura 
                FROM cliente 
                WHERE ID_cliente = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $dados = $resultado->fetch_assoc();
            $perfil = new Perfil();
            $perfil->id      = $dados['ID_cliente'];
            $perfil->nome    = $dados['nome'];
            $perfil->email   = $dados['email'];
            $perfil->pontos  = $dados['pontos'] ?? 0;
            $perfil->usuario = $dados['usuario'] ?? null;
            $perfil->foto    = $dados['foto'] ?? null;
            $perfil->moldura = $dados['moldura'] ?? '';

            return $perfil;
        }

        return null;
    }

    // Atualizar/salvar perfil
    public function salvar() {
        $conn = Conexao::getConexao();

        $sql = "UPDATE cliente 
                SET nome = ?, usuario = ?, foto = ?, moldura = ? 
                WHERE ID_cliente = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", 
            $this->nome, 
            $this->usuario, 
            $this->foto, 
            $this->moldura, 
            $this->id
        );

        return $stmt->execute();
    }
}
