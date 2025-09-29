<?php
include_once '../../Conexao/Conectar.php';

class Lista_Desejo {
    private $ID_cliente;
    private $ID_jogo;
    private $conn;

    public function __construct($ID_cliente = null, $ID_jogo = null) {
        $this->ID_cliente = $ID_cliente;
        $this->ID_jogo = $ID_jogo;
        $this->conn = Conectar::getInstance();
    }

    public function setID_cliente($ID_cliente) {
        $this->ID_cliente = $ID_cliente;
    }

    public function setID_jogo($ID_jogo) {
        $this->ID_jogo = $ID_jogo;
    }

    public function adicionar() {
        try {
            $sql = $this->conn->prepare("INSERT INTO lista_desejo (ID_cliente, ID_jogo) VALUES (:cliente, :jogo)");
            $sql->bindParam(':cliente', $this->ID_cliente, PDO::PARAM_INT);
            $sql->bindParam(':jogo', $this->ID_jogo, PDO::PARAM_INT);
            return $sql->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function remover() {
        try {
            $sql = $this->conn->prepare("DELETE FROM lista_desejo WHERE ID_cliente = :cliente AND ID_jogo = :jogo");
            $sql->bindParam(':cliente', $this->ID_cliente, PDO::PARAM_INT);
            $sql->bindParam(':jogo', $this->ID_jogo, PDO::PARAM_INT);
            return $sql->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function ordenar($order_by = 'ORDER BY j.ID_jogo ASC') {
    try {
     $sql = $this->conn->prepare("
    SELECT j.ID_jogo, j.nome, j.preco, j.desconto, j.Img,
           GROUP_CONCAT(g.Nome SEPARATOR ', ') AS generos
    FROM lista_desejo ld
    JOIN jogo j ON ld.ID_jogo = j.ID_jogo
    LEFT JOIN jogo_genero jg ON j.ID_jogo = jg.ID_jogo
    LEFT JOIN genero g ON jg.Id_Gen = g.Id_Gen
    WHERE ld.ID_cliente = :cliente
    GROUP BY j.ID_jogo
    $order_by
");

        $sql->bindParam(':cliente', $this->ID_cliente, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}


   public function adicionarCarrinho() {
    try {
        // Verifica se já está no carrinho
        $check = $this->conn->prepare("SELECT * FROM carrinho WHERE ID_cliente = :cliente AND ID_jogo = :jogo");
        $check->bindParam(':cliente', $this->ID_cliente, PDO::PARAM_INT);
        $check->bindParam(':jogo', $this->ID_jogo, PDO::PARAM_INT);
        $check->execute();
        $item = $check->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            return false; // já está no carrinho
        }

        // Adiciona ao carrinho
        $sql = $this->conn->prepare("INSERT INTO carrinho (ID_cliente, ID_jogo, quantidade) VALUES (:cliente, :jogo, 1)");
        $sql->bindParam(':cliente', $this->ID_cliente, PDO::PARAM_INT);
        $sql->bindParam(':jogo', $this->ID_jogo, PDO::PARAM_INT);
        $sql->execute();

        return true; // sucesso
    } catch (PDOException $e) {
        return false; // erro
    }
}

}
?>
