<?php
// buscar_solicitacoes.php  (versão que consulta tabela `amizade`)
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../Conexao/Conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]);
    exit;
}

$meuId = (int)$_SESSION['usuario_id'];

try {
    $conn = Conexao::getConexao();

    $sql = "SELECT a.id_amizade AS id_solicitacao,
                   a.id_solicitante,
                   a.id_recebedor,
                   a.status,
                   a.data_solicitacao,
                   c.ID_cliente,
                   c.nome,
                   c.usuario,
                   c.foto
            FROM amizade a
            JOIN cliente c ON c.ID_cliente = a.id_solicitante
            WHERE a.id_recebedor = ? AND LOWER(a.status) = 'pendente'
            ORDER BY a.data_solicitacao DESC";

    // suporta tanto PDO quanto mysqli (depende da Conexao)
    $stmt = null;
    $rows = [];

    // tenta PDO (se Conexao retornar PDO)
    $connType = get_class($conn);
    if (stripos($connType, 'pdo') !== false) {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$meuId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // assume mysqli
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $meuId);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) $rows[] = $r;
    }

    $out = array_map(function($r) {
        return [
            'id' => (int)($r['id_solicitacao'] ?? $r['id_amizade'] ?? 0),
            'id_solicitante' => (int)($r['id_solicitante'] ?? 0),
            'nome' => $r['nome'] ?? '',
            'usuario' => $r['usuario'] ?? '',
            'foto' => $r['foto'] ?? '../../Img/Elementos/user.png',
            'data_envio' => $r['data_solicitacao'] ?? ''
        ];
    }, $rows);

    echo json_encode($out);
    exit;
} catch (Exception $e) {
    error_log("buscar_solicitacoes erro: " . $e->getMessage());
    echo json_encode([]);
    exit;
}
