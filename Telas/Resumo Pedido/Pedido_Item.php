<?php
include_once '../Conexao/Conexao.php';

class Pedido_Item {
    private $ID_item;
    private $ID_pedido;
    private $ID_jogo;
    private $quantidade;
    private $conn;

    public function __construct($ID_pedido = null, $ID_jogo = null, $quantidade = 1) {
        $this->ID_pedido = $ID_pedido;
        $this->ID_jogo = $ID_jogo;
        $this->quantidade = $quantidade;
        $this->conn = Conexao::getConexao();
    }

    // Setters
    public function setID_item($ID_item) {
        $this->ID_item = $ID_item;
    }

    public function setID_pedido($ID_pedido) {
        $this->ID_pedido = $ID_pedido;
    }

    public function setID_jogo($ID_jogo) {
        $this->ID_jogo = $ID_jogo;
    }

    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
    }

    // Adicionar item ao pedido
    public function adicionar() {
        try {
            $sql = $this->conn->prepare("
                INSERT INTO pedido_item (ID_pedido, ID_jogo, quantidade) 
                VALUES (:pedido, :jogo, :quantidade)
            ");
            $sql->bindParam(':pedido', $this->ID_pedido, PDO::PARAM_INT);
            $sql->bindParam(':jogo', $this->ID_jogo, PDO::PARAM_INT);
            $sql->bindParam(':quantidade', $this->quantidade, PDO::PARAM_INT);
            if($sql->execute()) {
                $this->ID_item = $this->conn->lastInsertId();
                return $this->ID_item;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    // Remover item do pedido
    public function remover() {
        try {
            $sql = $this->conn->prepare("
                DELETE FROM pedido_item 
                WHERE ID_item = :item OR (ID_pedido = :pedido AND ID_jogo = :jogo)
            ");
            $sql->bindParam(':item', $this->ID_item, PDO::PARAM_INT);
            $sql->bindParam(':pedido', $this->ID_pedido, PDO::PARAM_INT);
            $sql->bindParam(':jogo', $this->ID_jogo, PDO::PARAM_INT);
            return $sql->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Listar todos os itens de um pedido
    public function listar($ID_pedido = null) {
        if ($ID_pedido) {
            $this->ID_pedido = $ID_pedido;
        }
        try {
            $sql = $this->conn->prepare("
                SELECT pi.ID_item, pi.ID_pedido, pi.ID_jogo, pi.quantidade, 
                       j.nome, j.preco, j.desconto, j.Img
                FROM pedido_item pi
                JOIN jogo j ON pi.ID_jogo = j.ID_jogo
                WHERE pi.ID_pedido = :pedido
            ");
            $sql->bindParam(':pedido', $this->ID_pedido, PDO::PARAM_INT);
            $sql->execute();
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
