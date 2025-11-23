<?php
include_once '../Conexao/Conexao.php';

class Lista_Desejo {
    private $ID_cliente;
    private $ID_jogo;
    private $conn;

    public function __construct($ID_cliente = null, $ID_jogo = null) {
        $this->ID_cliente = $ID_cliente;
        $this->ID_jogo = $ID_jogo;
        $this->conn = Conexao::getConexao(); // retorna mysqli
    }

    public function setID_cliente($ID_cliente) {
        $this->ID_cliente = $ID_cliente;
    }

    public function setID_jogo($ID_jogo) {
        $this->ID_jogo = $ID_jogo;
    }

    public function adicionar() {
        if (empty($this->ID_cliente) || empty($this->ID_jogo)) return false;

        $check = $this->conn->prepare("SELECT 1 FROM lista_desejo WHERE ID_cliente = ? AND ID_jogo = ? LIMIT 1");
        if (!$check) return false;
        $check->bind_param("ii", $this->ID_cliente, $this->ID_jogo);
        $check->execute();
        $res = $check->get_result();

        if ($res && $res->num_rows > 0) {
            $check->close();
            return true;
        }
        $check->close();

        $ins = $this->conn->prepare("INSERT INTO lista_desejo (ID_cliente, ID_jogo) VALUES (?, ?)");
        if (!$ins) return false;
        $ins->bind_param("ii", $this->ID_cliente, $this->ID_jogo);
        $ok = $ins->execute();
        $ins->close();
        return $ok;
    }

    public function remover() {
        if (empty($this->ID_cliente) || empty($this->ID_jogo)) return false;

        $del = $this->conn->prepare("DELETE FROM lista_desejo WHERE ID_cliente = ? AND ID_jogo = ?");
        if (!$del) return false;
        $del->bind_param("ii", $this->ID_cliente, $this->ID_jogo);
        $ok = $del->execute();
        $affected = $del->affected_rows;
        $del->close();
        return ($ok && $affected >= 0);
    }

    public function ordenar($order_by = 'j.ID_jogo ASC') {
        if (empty($this->ID_cliente)) return [];

        if (!preg_match('/^[\w\.]+\s+(ASC|DESC)$/i', trim($order_by))) {
            $order_by = 'j.ID_jogo ASC';
        }

        $sql = "
            SELECT j.ID_jogo, j.nome, j.preco, j.desconto, j.Img,
                   GROUP_CONCAT(g.Nome SEPARATOR ', ') AS generos
            FROM lista_desejo ld
            JOIN jogo j ON ld.ID_jogo = j.ID_jogo
            LEFT JOIN jogo_genero jg ON j.ID_jogo = jg.ID_jogo
            LEFT JOIN genero g ON jg.Id_Gen = g.Id_Gen
            WHERE ld.ID_cliente = ?
            GROUP BY j.ID_jogo
            ORDER BY $order_by
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param("i", $this->ID_cliente);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }

    public function adicionarCarrinho() {
        if (empty($this->ID_cliente) || empty($this->ID_jogo)) return false;

        $check = $this->conn->prepare("SELECT quantidade FROM carrinho WHERE ID_cliente = ? AND ID_jogo = ? LIMIT 1");
        if (!$check) return false;
        $check->bind_param("ii", $this->ID_cliente, $this->ID_jogo);
        $check->execute();
        $res = $check->get_result();

        if ($res && $res->num_rows > 0) {
            $check->close();
            $upd = $this->conn->prepare("UPDATE carrinho SET quantidade = quantidade + 1 WHERE ID_cliente = ? AND ID_jogo = ?");
            if (!$upd) return false;
            $upd->bind_param("ii", $this->ID_cliente, $this->ID_jogo);
            $ok = $upd->execute();
            $upd->close();
            return $ok;
        } else {
            $check->close();
            $ins = $this->conn->prepare("INSERT INTO carrinho (ID_cliente, ID_jogo, quantidade) VALUES (?, ?, 1)");
            if (!$ins) return false;
            $ins->bind_param("ii", $this->ID_cliente, $this->ID_jogo);
            $ok = $ins->execute();
            $ins->close();
            return $ok;
        }
    }
}
?>
