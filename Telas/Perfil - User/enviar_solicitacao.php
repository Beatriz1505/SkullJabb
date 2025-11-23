<?php
// enviar_solicitacao.php
require_once "../Conexao/Conexao.php";
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok'=>false, 'msg'=>'Não autenticado']);
    exit;
}

$meuId = (int) $_SESSION['usuario_id'];
$id_dest = isset($_POST['id_destinatario']) ? (int) $_POST['id_destinatario'] : 0;

if ($id_dest <= 0 || $id_dest === $meuId) {
    echo json_encode(['ok'=>false, 'msg'=>'ID inválido']);
    exit;
}

try {
    $conn = Conexao::getConexao();

    // já existe amizade aceita?
    $sql = "SELECT status FROM amizade WHERE (id_solicitante = ? AND id_recebedor = ?) OR (id_solicitante = ? AND id_recebedor = ?) LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $meuId, $id_dest, $id_dest, $meuId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $stat = strtolower($row['status']);
        if ($stat === 'aceito') {
            echo json_encode(['ok'=>false, 'msg'=>'Vocês já são amigos']);
            exit;
        }
        if ($stat === 'pendente') {
            echo json_encode(['ok'=>false, 'msg'=>'Já existe uma solicitação pendente']);
            exit;
        }
    }

    // insere a solicitação
    $sql = "INSERT INTO amizade (id_solicitante, id_recebedor, status, data_solicitacao) VALUES (?, ?, 'pendente', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $meuId, $id_dest);
    $ok = $stmt->execute();

    if ($ok) {
        echo json_encode(['ok'=>true, 'msg'=>'Solicitação enviada com sucesso']);
    } else {
        echo json_encode(['ok'=>false, 'msg'=>'Erro ao salvar solicitação']);
    }
    exit;

} catch (Exception $e) {
    error_log("enviar_solicitacao erro: " . $e->getMessage());
    echo json_encode(['ok'=>false, 'msg'=>'Erro no servidor']);
    exit;
}
?>
