<?php
require_once "../Conexao/Conexao.php";

class LojaPontos {

    // 🔹 Buscar pontos do cliente logado
    public static function buscarPontosCliente($id_cliente) {
        global $pdo;
        $sql = "SELECT pontos FROM cliente WHERE ID_cliente = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_cliente]);
        return $stmt->fetchColumn(); // retorna só o valor dos pontos
    }

    // 🔹 Listar todos os itens disponíveis na loja
    public static function listarItens() {
        global $pdo;
        $sql = "SELECT * FROM item_pontos";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Comprar item com pontos
    public static function comprarItem($id_cliente, $id_item) {
        global $pdo;

        // 1. Buscar custo do item
        $sql = "SELECT custo_pontos FROM item_pontos WHERE ID_item = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_item]);
        $custo = $stmt->fetchColumn();

        if (!$custo) return "Item não encontrado.";

        // 2. Buscar pontos do cliente
        $sql = "SELECT pontos FROM cliente WHERE ID_cliente = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_cliente]);
        $pontos = $stmt->fetchColumn();

        if ($pontos < $custo) {
            return "Você não tem pontos suficientes.";
        }

        // 3. Descontar pontos
        $sql = "UPDATE cliente SET pontos = pontos - ? WHERE ID_cliente = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$custo, $id_cliente]);

        return "Compra realizada com sucesso!";
    }
}
?>
