<?php 
include_once '../Conexao/Conectar.php';

class Resumo_Pedido {
    private $ID_pedido;
    private $ID_cliente;
    private $data_pedido;
    private $status;
    private $metodo_pagamento;
    private $total;
    private $ID_cupom;
    private $conn;

    public function __construct($ID_cliente = null, $ID_pedido = null) {
        $this->ID_cliente = $ID_cliente;
        $this->ID_pedido = $ID_pedido;
        $this->conn = Conexao::getConexao();
    }

    // ================= Setters =================
    public function setID_pedido($ID_pedido) {
        $this->ID_pedido = $ID_pedido;
    }

    public function setID_cliente($ID_cliente) {
        $this->ID_cliente = $ID_cliente;
    }

    public function setMetodoPagamento($metodo) {
        $this->metodo_pagamento = $metodo;
    }

    public function setTotal($total) {
        $this->total = $total;
    }
    public function getTotal() {
    return $this->total;
}

    public function setIDCupom($ID_cupom) {
        $this->ID_cupom = $ID_cupom;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
    

    // ================= Adicionar um novo pedido =================
    public function adicionar() {
        try {
            $sql = $this->conn->prepare("
                INSERT INTO resumo_pedido 
                (ID_cliente, status, metodo_pagamento, total, ID_cupom) 
                VALUES 
                (:cliente, :status, :metodo, :total, :cupom)
            ");
            $sql->bindParam(':cliente', $this->ID_cliente, PDO::PARAM_INT);
            $sql->bindParam(':status', $this->status, PDO::PARAM_STR);
            $sql->bindParam(':metodo', $this->metodo_pagamento, PDO::PARAM_STR);
            $sql->bindParam(':total', $this->total);
            $sql->bindParam(':cupom', $this->ID_cupom, PDO::PARAM_INT);
            if($sql->execute()) {
                $this->ID_pedido = $this->conn->lastInsertId();
                return $this->ID_pedido;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }
    
public function finalizar() {
    // 1️⃣ Criar pedido com status pendente e total 0
    $this->setStatus('pendente');
    $this->setTotal(0);
    $novoPedidoID = $this->adicionar();
    if (!$novoPedidoID) return false;

    // 2️⃣ Pegar itens do carrinho do cliente
    $sql = Conectar::getInstance()->prepare("SELECT * FROM carrinho WHERE ID_cliente = :cliente");
    $sql->bindParam(':cliente', $this->ID_cliente, PDO::PARAM_INT);
    $sql->execute();
    $itensCarrinho = $sql->fetchAll(PDO::FETCH_ASSOC);

    // 3️⃣ Adicionar cada item no pedido_item
    include_once 'Pedido_Item.php';
    foreach ($itensCarrinho as $item) {
        $pedidoItem = new Pedido_Item($novoPedidoID, $item['ID_jogo'], $item['quantidade']);
        $pedidoItem->adicionar();
    }

    // 4️⃣ Limpar carrinho
    $limpar = Conectar::getInstance()->prepare("DELETE FROM carrinho WHERE ID_cliente = :cliente");
    $limpar->bindParam(':cliente', $this->ID_cliente, PDO::PARAM_INT);
    $limpar->execute();

    return $novoPedidoID;
}

    // ================= Cancelar pedido =================
  public function cancelar() {
    try {
        $sql = $this->conn->prepare("
            UPDATE resumo_pedido
            SET status = 'cancelado'
            WHERE ID_pedido = :pedido AND ID_cliente = :cliente AND status != 'finalizado'
        ");
        $sql->bindParam(':pedido', $this->ID_pedido, PDO::PARAM_INT);
        $sql->bindParam(':cliente', $this->ID_cliente, PDO::PARAM_INT);
        return $sql->execute();
    } catch (PDOException $e) {
        return false;
    }
}


    // ================= Listar pedidos do cliente =================
    public function listar($order_by = 'ORDER BY ID_pedido ASC') {
        try {
            $sql = $this->conn->prepare("
                SELECT rp.*, c.codigo AS cupom_codigo, c.desconto AS cupom_desconto, c.tipo AS cupom_tipo
                FROM resumo_pedido rp
                LEFT JOIN cupom c ON rp.ID_cupom = c.ID_cupom
                WHERE rp.ID_cliente = :cliente
                $order_by
            ");
            $sql->bindParam(':cliente', $this->ID_cliente, PDO::PARAM_INT);
            $sql->execute();
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // ================= Aplicar cupom (validação) =================
    public function aplicarCupom($codigo_cupom) {
        try {
            $sql = $this->conn->prepare("
                SELECT * FROM cupom 
                WHERE codigo = :codigo AND ativo = 1 AND validade >= CURDATE()
            ");
            $sql->bindParam(':codigo', $codigo_cupom);
            $sql->execute();
            $cupom = $sql->fetch(PDO::FETCH_ASSOC);

            if ($cupom) {
                return [
                    'valido' => true,
                    'desconto' => $cupom['desconto'],
                    'tipo' => $cupom['tipo'],
                    'ID_cupom' => $cupom['ID_cupom']
                ];
            } else {
                return ['valido' => false];
            }
        } catch (PDOException $e) {
            return ['valido' => false];
        }
    }

   // ================= Atualizar total aplicando cupom =================
public function AtualizarCupom($codigo_cupom) {
    $cupomInfo = $this->aplicarCupom($codigo_cupom);

    if ($cupomInfo['valido']) {
        if ($cupomInfo['tipo'] === 'percentual') {
            $this->total -= ($this->total * $cupomInfo['desconto'] / 100);
        } else { 
            $this->total -= $cupomInfo['desconto'];
        }
        $this->ID_cupom = $cupomInfo['ID_cupom'];

      
        try {
            $sql = $this->conn->prepare("
                UPDATE resumo_pedido
                SET total = :total, ID_cupom = :cupom
                WHERE ID_pedido = :pedido
            ");
            $sql->bindParam(':total', $this->total);
            $sql->bindParam(':cupom', $this->ID_cupom, PDO::PARAM_INT);
            $sql->bindParam(':pedido', $this->ID_pedido, PDO::PARAM_INT);
            $sql->execute();
            return true; // sucesso
        } catch (PDOException $e) {
            echo "Erro ao atualizar pedido: " . $e->getMessage();
            return false; // falha
        }
    } else {
       
        return false;
    }
}

}
?>
