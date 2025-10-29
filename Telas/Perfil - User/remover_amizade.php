<?php
session_start();
require_once __DIR__ . '/../Conexao/Conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'msg' => 'Não logado']);
    exit;
}

$meuId = (int)$_SESSION['usuario_id'];
$id_outro = isset($_POST['id_outro']) ? (int)$_POST['id_outro'] : 0;

if ($id_outro <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
    exit;
}

try {
    $conn = Conexao::getConexao();
    
    $sql = "DELETE FROM amizade 
            WHERE ((id_solicitante = ? AND id_recebedor = ?) 
            OR (id_solicitante = ? AND id_recebedor = ?)) 
            AND status = 'aceito'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $meuId, $id_outro, $id_outro, $meuId);
    $result = $stmt->execute();
    
    if ($result && $stmt->affected_rows > 0) {
        echo json_encode(['ok' => true, 'msg' => 'Amizade removida com sucesso']);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Amizade não encontrada']);
    }
    
} catch (Exception $e) {
    error_log("Erro remover_amizade: " . $e->getMessage());
    echo json_encode(['ok' => false, 'msg' => 'Erro ao remover amizade']);
}
?>