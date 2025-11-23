<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../Conexao/Conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['estado' => 'nenhum']);
    exit;
}

$meuId = (int)$_SESSION['usuario_id'];

// Se foi passado um ID de usuário específico, verifica o estado da relação
if (isset($_GET['id_usuario'])) {
    $idOutro = (int)$_GET['id_usuario'];
    
    if ($idOutro <= 0) {
        echo json_encode(['estado' => 'nenhum']);
        exit;
    }

    try {
        $conn = Conexao::getConexao();
        $estado = 'nenhum';

        // Verifica se são amigos (status = 'aceito')
        $sqlAmigos = "SELECT id_solicitante, status 
                      FROM amizade 
                      WHERE ((id_solicitante = ? AND id_recebedor = ?) OR (id_solicitante = ? AND id_recebedor = ?))
                      AND status = 'aceito' 
                      LIMIT 1";
        
        if ($conn instanceof PDO) {
            $stmt = $conn->prepare($sqlAmigos);
            $stmt->execute([$meuId, $idOutro, $idOutro, $meuId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $estado = 'amigos';
            } else {
                // Verifica solicitações pendentes
                $sqlPendente = "SELECT id_solicitante, status 
                               FROM amizade 
                               WHERE ((id_solicitante = ? AND id_recebedor = ?) OR (id_solicitante = ? AND id_recebedor = ?))
                               AND status = 'pendente' 
                               LIMIT 1";
                $stmtPendente = $conn->prepare($sqlPendente);
                $stmtPendente->execute([$meuId, $idOutro, $idOutro, $meuId]);
                $resultPendente = $stmtPendente->fetch(PDO::FETCH_ASSOC);
                
                if ($resultPendente) {
                    $idSolicitante = (int)$resultPendente['id_solicitante'];
                    $estado = ($idSolicitante === $meuId) ? 'pendente_enviado' : 'pendente_recebido';
                }
            }
        } else {
            // mysqli
            $stmt = $conn->prepare($sqlAmigos);
            $stmt->bind_param("iiii", $meuId, $idOutro, $idOutro, $meuId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $estado = 'amigos';
            } else {
                // Verifica solicitações pendentes
                $sqlPendente = "SELECT id_solicitante, status 
                               FROM amizade 
                               WHERE ((id_solicitante = ? AND id_recebedor = ?) OR (id_solicitante = ? AND id_recebedor = ?))
                               AND status = 'pendente' 
                               LIMIT 1";
                $stmtPendente = $conn->prepare($sqlPendente);
                $stmtPendente->bind_param("iiii", $meuId, $idOutro, $idOutro, $meuId);
                $stmtPendente->execute();
                $resultPendente = $stmtPendente->get_result();
                
                if ($resultPendente && $resultPendente->num_rows > 0) {
                    $row = $resultPendente->fetch_assoc();
                    $idSolicitante = (int)$row['id_solicitante'];
                    $estado = ($idSolicitante === $meuId) ? 'pendente_enviado' : 'pendente_recebido';
                }
            }
        }

        echo json_encode(['estado' => $estado]);
        
    } catch (Exception $e) {
        error_log("Erro verificar_amizade: " . $e->getMessage());
        echo json_encode(['estado' => 'nenhum']);
    }
    
} else {
    // Comportamento original: retorna lista de solicitações pendentes recebidas
    try {
        $conn = Conexao::getConexao();

        $sql = "SELECT 
                    a.id_amizade AS id_solicitacao,
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
                WHERE a.id_recebedor = ? AND a.status = 'pendente'
                ORDER BY a.data_solicitacao DESC";

        $rows = [];

        // Verifica se é PDO ou mysqli
        if ($conn instanceof PDO) {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$meuId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $meuId);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
        }

        $out = array_map(function ($r) {
            return [
                'id' => (int)($r['id_solicitacao'] ?? 0),
                'id_solicitante' => (int)($r['id_solicitante'] ?? 0),
                'nome' => $r['nome'] ?? '',
                'usuario' => $r['usuario'] ?? '',
                'foto' => $r['foto'] ?? '../../Img/Elementos/user.png',
                'data_envio' => $r['data_solicitacao'] ?? ''
            ];
        }, $rows);

        echo json_encode($out);
        
    } catch (Exception $e) {
        error_log("Erro ao buscar solicitações: " . $e->getMessage());
        echo json_encode([]);
    }
}
exit;
?>