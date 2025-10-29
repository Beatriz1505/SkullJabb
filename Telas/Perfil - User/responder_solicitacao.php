<?php

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../Conexao/Conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Não autenticado']);
    exit;
}

$meuId = (int) $_SESSION['usuario_id'];
$body = $_POST;
$id_item = isset($body['id_solicitacao']) ? (int)$body['id_solicitacao'] : 0;
$acao_raw = isset($body['acao']) ? trim($body['acao']) : '';

if ($id_item <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'id inválido']);
    exit;
}

if (!in_array($acao_raw, ['aceitar','recusar'])) {
    echo json_encode(['ok' => false, 'msg' => 'Ação inválida']);
    exit;
}

$novo_status = ($acao_raw === 'aceitar') ? 'aceito' : 'recusado';

$conn = Conexao::getConexao();
if (!$conn) {
    echo json_encode(['ok' => false, 'msg' => 'Erro na conexão com DB']);
    exit;
}

try {
    // buscamos o registro em `amizade` (visto que solicitacao_amizade não existe mais)
    $sql = "SELECT id_amizade, id_solicitante, id_recebedor, status FROM amizade WHERE id_amizade = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Prepare falhou: " . $conn->error);
    $stmt->bind_param("i", $id_item);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res || $res->num_rows === 0) {
        echo json_encode(['ok' => false, 'msg' => 'Registro de amizade não encontrado']);
        exit;
    }
    $row = $res->fetch_assoc();

    // autorização: só o recebedor pode responder
    $id_recebedor = (int)$row['id_recebedor'];
    $id_solicitante = (int)$row['id_solicitante'];
    if ($id_recebedor !== $meuId) {
        echo json_encode(['ok' => false, 'msg' => 'Você não tem permissão para responder esta solicitação']);
        exit;
    }

    // só processa quando estiver pendente
    $status_atual = trim(strtolower((string)$row['status']));
    if (!in_array($status_atual, ['pendente','0','pend'])) {
        echo json_encode(['ok' => false, 'msg' => 'Solicitação já respondida (status: ' . $status_atual . ')']);
        exit;
    }

    // transaction
    $conn->begin_transaction();

    // atualiza status e data_resposta
    $sqlUpd = "UPDATE amizade SET status = ?, data_resposta = NOW() WHERE id_amizade = ? LIMIT 1";
    $stUpd = $conn->prepare($sqlUpd);
    if (!$stUpd) throw new Exception("Prepare update falhou: " . $conn->error);
    $stUpd->bind_param("si", $novo_status, $id_item);
    if (!$stUpd->execute()) throw new Exception("Execute update falhou: " . $stUpd->error);

    // se aceito, não precisa inserir (registro já está na tabela 'amizade')
    // se recusado, apenas atualizamos o status (já feito)

    $conn->commit();
    echo json_encode(['ok' => true, 'msg' => ($novo_status === 'aceito' ? 'Solicitação aceita' : 'Solicitação recusada')]);
    exit;

} catch (Exception $e) {
    if ($conn) $conn->rollback();
    // devolve erro detalhado (tem que remover em produção)
    echo json_encode(['ok' => false, 'msg' => 'Erro interno', 'debug' => $e->getMessage()]);
    exit;
}
