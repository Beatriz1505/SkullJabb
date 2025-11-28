<?php
require_once "../Conexao/Conexao.php";

class LojaPontos {

    // 🔹 Buscar pontos do cliente
    public static function buscarPontosCliente($id_cliente) {
        $conn = Conexao::getConexao();
        $id_cliente = (int)$id_cliente;
        
        if ($conn instanceof PDO) {
            $sql = "SELECT pontos FROM cliente WHERE ID_cliente = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_cliente]);
            return (int) $stmt->fetchColumn();
        } else {
            // mysqli
            $sql = "SELECT pontos FROM cliente WHERE ID_cliente = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_cliente);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return (int) ($row['pontos'] ?? 0);
        }
    }

    // 🔹 Listar todos os itens da loja de pontos
    public static function listarItens() {
        $conn = Conexao::getConexao();
        
        if ($conn instanceof PDO) {
            $sql = "SELECT * FROM item_pontos";
            $stmt = $conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // mysqli
            $sql = "SELECT * FROM item_pontos";
            $result = $conn->query($sql);
            $itens = [];
            while ($row = $result->fetch_assoc()) {
                $itens[] = $row;
            }
            return $itens;
        }
    }

    // 🔹 Verificar se o item já foi comprado pelo cliente
    public static function verificarItemComprado($id_cliente, $id_item) {
        $conn = Conexao::getConexao();
        $id_cliente = (int)$id_cliente;
        $id_item = (int)$id_item;

        if ($conn instanceof PDO) {
            $sql = "SELECT 1 FROM inventario_pontos WHERE id_cliente = ? AND id_item = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_cliente, $id_item]);
            return (bool) $stmt->fetchColumn();
        } else {
            // mysqli
            $sql = "SELECT 1 FROM inventario_pontos WHERE id_cliente = ? AND id_item = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id_cliente, $id_item);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result && $result->num_rows > 0;
        }
    }

    // 🔹 Comprar item usando pontos
    public static function comprarItem($id_cliente, $id_item) {
        $conn = Conexao::getConexao();
        $id_cliente = (int)$id_cliente;
        $id_item = (int)$id_item;

        try {

            // Verifica se já foi comprado
            if (self::verificarItemComprado($id_cliente, $id_item)) {
                return ['success' => false, 'msg' => 'Você já comprou este item!'];
            }

            // Buscar custo do item
            if ($conn instanceof PDO) {
                $sql = "SELECT custo_pontos FROM item_pontos WHERE ID_item = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id_item]);
                $custo = $stmt->fetchColumn();
            } else {
                $sql = "SELECT custo_pontos FROM item_pontos WHERE ID_item = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_item);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $custo = $row['custo_pontos'] ?? false;
            }

            if ($custo === false) {
                return ['success' => false, 'msg' => 'Item não encontrado.'];
            }

            // Buscar pontos do cliente
            $pontos = self::buscarPontosCliente($id_cliente);

            if ($pontos < $custo) {
                return ['success' => false, 'msg' => 'Você não tem pontos suficientes.'];
            }

            // Iniciar transação
            if ($conn instanceof PDO) {
                $conn->beginTransaction();

                // Descontar pontos
                $sql = "UPDATE cliente SET pontos = pontos - ? WHERE ID_cliente = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$custo, $id_cliente]);

                // Inserir no inventário
                $sql = "INSERT INTO inventario_pontos (id_cliente, id_item, data_compra) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id_cliente, $id_item]);

                $conn->commit();

            } else {
                $conn->begin_transaction();

                // Descontar pontos
                $sql = "UPDATE cliente SET pontos = pontos - ? WHERE ID_cliente = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $custo, $id_cliente);
                $stmt->execute();

                // Inserir no inventário
                $sql = "INSERT INTO inventario_pontos (id_cliente, id_item, data_compra) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $id_cliente, $id_item);
                $stmt->execute();

                $conn->commit();
            }

            return ['success' => true, 'msg' => 'Item comprado com sucesso!'];

        } catch (Exception $e) {

            if ($conn instanceof PDO) {
                $conn->rollBack();
            } else {
                $conn->rollback();
            }

            return [
                'success' => false, 
                'msg' => 'Erro ao processar a compra: ' . $e->getMessage()
            ];
        }
    }

    // 🔹 Resgatar check-in diário
    public static function resgatarCheckinDiario($id_cliente) {

        $conn = Conexao::getConexao();
        $id_cliente = (int)$id_cliente;

        try {

            if ($conn instanceof PDO) {

                $conn->beginTransaction();
                $sql = "SELECT pontos, ultimo_resgate FROM cliente WHERE ID_cliente = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id_cliente]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

            } else {

                $conn->begin_transaction();
                $sql = "SELECT pontos, ultimo_resgate FROM cliente WHERE ID_cliente = ? FOR UPDATE";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_cliente);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
            }

            if (!$row) {
                return ['success'=>false, 'msg'=>'Cliente não encontrado.'];
            }

            $pontosAtual = (int) $row['pontos'];
            $ultimo = $row['ultimo_resgate'];

            $agora = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
            $hoje = $agora->format('Y-m-d');

            if ($ultimo === $hoje) {
                return ['success'=>false, 'msg'=>'Você já resgatou seus 30 pontos hoje!'];
            }

            $novosPontos = $pontosAtual + 30;

            if ($conn instanceof PDO) {

                $sql = "UPDATE cliente SET pontos = ?, ultimo_resgate = ? WHERE ID_cliente = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$novosPontos, $hoje, $id_cliente]);

                $conn->commit();

            } else {

                $sql = "UPDATE cliente SET pontos = ?, ultimo_resgate = ? WHERE ID_cliente = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isi", $novosPontos, $hoje, $id_cliente);
                $stmt->execute();

                $conn->commit();
            }

            return [
                'success'=>true, 
                'msg'=>'+30 pontos adicionados!',
                'pontos'=>$novosPontos
            ];

        } catch (Exception $e) {

            if ($conn instanceof PDO) {
                $conn->rollBack();
            } else {
                $conn->rollback();
            }

            return [
                'success'=>false,
                'msg'=>'Erro ao resgatar: '.$e->getMessage()
            ];
        }
    }

    public static function listarItensComprados($id_cliente) {
    $conn = Conexao::getConexao();
    $id = (int)$id_cliente;

    // MYSQLI
    if ($conn instanceof mysqli) {
        $sql = "SELECT item_pontos.* 
                FROM inventario_pontos 
                JOIN item_pontos ON item_pontos.ID_item = inventario_pontos.id_item
                WHERE inventario_pontos.id_cliente = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $itens = [];
        while ($row = $result->fetch_assoc()) {
            $itens[] = $row;
        }
        return $itens;
    }

    // PDO (se um dia usar)
    $sql = "SELECT item_pontos.* 
            FROM inventario_pontos 
            JOIN item_pontos ON item_pontos.ID_item = inventario_pontos.id_item
            WHERE inventario_pontos.id_cliente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}
?>
