<?php
// ClasseModelagemPerfilUser.php (versão corrigida para usar apenas a tabela `amizade`)
require_once __DIR__ . '/../Conexao/Conexao.php';
require_once __DIR__ . '/../Perfil/ClasseModelagemPerfil.php';

class PerfilUser {

    /**
     * Retorna o estado da relação entre dois usuários:
     * 'amigos', 'pendente_enviado', 'pendente_recebido' ou 'nenhum'
     */
    public static function obterEstadoRelacao($id_logado, $id_outro) {
        $conn = Conexao::getConexao();
        $id_logado = (int)$id_logado;
        $id_outro  = (int)$id_outro;

        // 1) verificar amizade aceita
        $sql1 = "SELECT id_solicitante FROM amizade
                 WHERE ((id_solicitante = ? AND id_recebedor = ?) OR (id_solicitante = ? AND id_recebedor = ?))
                   AND (LOWER(status) = 'aceito' OR status = '1' OR status = '3')
                 LIMIT 1";
        $stmt1 = $conn->prepare($sql1);
        if ($stmt1) {
            $stmt1->bind_param("iiii", $id_logado, $id_outro, $id_outro, $id_logado);
            $stmt1->execute();
            $r1 = $stmt1->get_result();
            if ($r1 && $r1->num_rows > 0) {
                return 'amigos';
            }
        } else {
            error_log("PerfilUser::obterEstadoRelacao prepare1 failed: " . $conn->error);
        }

        // 2) verificar pendente (ordenando pela data mais recente)
        $sql2 = "SELECT id_solicitante, id_recebedor, status
                 FROM amizade
                 WHERE ((id_solicitante = ? AND id_recebedor = ?) OR (id_solicitante = ? AND id_recebedor = ?))
                   AND (LOWER(status) = 'pendente' OR status = '0' OR LOWER(status) = 'pend')
                 ORDER BY data_solicitacao DESC
                 LIMIT 1";
        $stmt2 = $conn->prepare($sql2);
        if (!$stmt2) {
            error_log("PerfilUser::obterEstadoRelacao prepare2 failed: " . $conn->error);
            return 'nenhum';
        }
        $stmt2->bind_param("iiii", $id_logado, $id_outro, $id_outro, $id_logado);
        $stmt2->execute();
        $r2 = $stmt2->get_result();
        if ($r2 && $r2->num_rows > 0) {
            $row = $r2->fetch_assoc();
            if ((int)$row['id_solicitante'] === $id_logado) {
                return 'pendente_enviado';
            } else {
                return 'pendente_recebido';
            }
        }

        return 'nenhum';
    }

    /**
     * Aceita ou recusa uma solicitação pendente.
     * $acao: 'aceitar' ou 'recusar'
     * Retorna true em sucesso, false caso contrário.
     */
    public static function responderSolicitacao($id_solicitante, $id_destinatario, $acao) {
        $conn = Conexao::getConexao();
        $id_solicitante = (int)$id_solicitante;
        $id_destinatario = (int)$id_destinatario;
        $acao = strtolower(trim($acao));

        if (!in_array($acao, ['aceitar', 'recusar', 'aceito', 'recusado'])) {
            return false;
        }

        try {
            if ($acao === 'aceitar' || $acao === 'aceito') {
                // atualiza o registro pendente para 'aceito'
                $sql = "UPDATE amizade
                        SET status = 'aceito', data_resposta = NOW()
                        WHERE id_solicitante = ? AND id_recebedor = ? AND (LOWER(status) = 'pendente' OR status = '0' OR LOWER(status) = 'pend')";
                $stmt = $conn->prepare($sql);
                if (!$stmt) { error_log("responderSolicitacao prepare update failed: ".$conn->error); return false; }
                $stmt->bind_param("ii", $id_solicitante, $id_destinatario);
                $ok = $stmt->execute();
                return (bool)$ok;
            } else {
                // recusar = remover o registro pendente
                $sql = "DELETE FROM amizade
                        WHERE id_solicitante = ? AND id_recebedor = ? AND (LOWER(status) = 'pendente' OR status = '0' OR LOWER(status) = 'pend')";
                $stmt = $conn->prepare($sql);
                if (!$stmt) { error_log("responderSolicitacao prepare delete failed: ".$conn->error); return false; }
                $stmt->bind_param("ii", $id_solicitante, $id_destinatario);
                $ok = $stmt->execute();
                return (bool)$ok;
            }
        } catch (Exception $e) {
            error_log("PerfilUser::responderSolicitacao erro: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retorna lista de solicitações recebidas (pendentes) para o usuário id_usuario.
     * Formato de retorno: array de assoc rows com campos id, id_solicitante, nome, usuario, foto, data_envio
     */
    public static function buscarSolicitacoesRecebidas($id_usuario) {
        $conn = Conexao::getConexao();
        $id_usuario = (int)$id_usuario;

        $sql = "SELECT a.id_amizade AS id, a.id_solicitante, a.id_recebedor, a.data_solicitacao,
                       c.ID_cliente, c.nome, c.usuario, c.foto
                FROM amizade a
                JOIN cliente c ON c.ID_cliente = a.id_solicitante
                WHERE a.id_recebedor = ? AND (LOWER(a.status) = 'pendente' OR a.status = '0' OR LOWER(a.status) = 'pend')
                ORDER BY a.data_solicitacao DESC";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("buscarSolicitacoesRecebidas prepare failed: " . $conn->error);
            return [];
        }
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($r = $res->fetch_assoc()) {
            $out[] = [
                'id' => (int)($r['id'] ?? 0),
                'id_solicitante' => (int)($r['id_solicitante'] ?? 0),
                'nome' => $r['nome'] ?? '',
                'usuario' => $r['usuario'] ?? '',
                'foto' => $r['foto'] ?? '../../Img/Elementos/user.png',
                'data_envio' => $r['data_solicitacao'] ?? ''
            ];
        }
        return $out;
    }

    /**
     * Buscar amigos (status aceito) do usuário.
     * Retorna array com ID_cliente, nome, usuario, foto
     */
    public static function buscarAmigos($id_usuario) {
        $conn = Conexao::getConexao();
        $id_usuario = (int)$id_usuario;

        $sql = "SELECT DISTINCT c.ID_cliente, c.nome, c.usuario, c.foto
                FROM amizade a
                JOIN cliente c ON (
                    (a.id_solicitante = ? AND a.id_recebedor = c.ID_cliente)
                    OR
                    (a.id_recebedor = ? AND a.id_solicitante = c.ID_cliente)
                )
                WHERE (a.id_solicitante = ? OR a.id_recebedor = ?) AND (LOWER(a.status) = 'aceito' OR a.status = '1' OR a.status = '3')
                ORDER BY c.nome ASC";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("buscarAmigos prepare failed: " . $conn->error);
            return [];
        }
        $stmt->bind_param("iiii", $id_usuario, $id_usuario, $id_usuario, $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        return $out;
    }

public static function buscarJogosRecentes(int $idUsuario, int $limit = 6): array {
    $result = [];
    try {
        $conn = Conexao::getConexao();
        if (!$conn) {
            error_log("buscarJogosRecentes: sem conexao");
            return $result;
        }

        $sql = "SELECT b.id_biblioteca,
                       b.id_cliente,
                       b.id_jogo,
                       j.ID_jogo AS jogo_id,
                       j.nome AS nome_jogo,
                       j.Img AS capa,
                       b.data_compra
                FROM biblioteca b
                LEFT JOIN jogo j ON j.ID_jogo = b.id_jogo
                WHERE b.id_cliente = ?
                ORDER BY b.data_compra DESC
                LIMIT ?";

        $st = $conn->prepare($sql);
        if (!$st) {
            error_log("buscarJogosRecentes: prepare falhou -> " . $conn->error);
            return $result;
        }

        $st->bind_param("ii", $idUsuario, $limit);
        if (!$st->execute()) {
            error_log("buscarJogosRecentes: execute falhou -> " . $st->error);
            return $result;
        }

        $res = $st->get_result();
        if (!$res) {
            error_log("buscarJogosRecentes: get_result falhou -> " . $st->error);
            return $result;
        }

        while ($row = $res->fetch_assoc()) {
            $nome = isset($row['nome_jogo']) ? trim($row['nome_jogo']) : ('Jogo #' . ($row['id_jogo'] ?? 'desconhecido'));
            $imgRaw = isset($row['capa']) ? trim($row['capa']) : '';

            // Normaliza caminho da imagem:
            // se já for URL absoluta (http/https) mantém; se contiver 'Img/' mantém; senão prefixa com pasta padrão
            $imgTrim = $imgRaw;
            if ($imgTrim !== '') {
                $imgTrim = preg_replace('/\s+/', '', $imgTrim); // tira quebras/espacos
                if (!preg_match('#^https?://#i', $imgTrim) && stripos($imgTrim, 'Img/') === false) {
                    $imgTrim = '../../Img/Jogos/' . ltrim($imgTrim, '/\\');
                }
            }
            $img = $imgTrim !== '' ? $imgTrim : '../../Img/Jogos/unpacking_perfil.png';

            $result[] = [
                'jogo_id' => (int)($row['jogo_id'] ?? ($row['id_jogo'] ?? 0)),
                'nome_jogo' => $nome,
                'capa' => $img,
                'horas_jogadas' => '0',
                'data_compra' => $row['data_compra'] ?? null
            ];
        }
        $st->close();
    } catch (Exception $e) {
        error_log("Erro buscarJogosRecentes: " . $e->getMessage());
    }
    return $result;
}


}


