<?php
// Perfil.php — versão robusta (suporta mysqli e PDO)
require_once __DIR__ . '/../Conexao/Conexao.php';

class Perfil {
    public $id;
    public $ID_cliente; // para compatibilidade com código antigo que usa esse nome
    public $nome;
    public $email;
    public $pontos;
    public $usuario;
    public $foto;
    public $moldura;

    /**
     * Buscar o perfil pelo ID (retorna instancia de Perfil ou null)
     */
    public static function buscarPorId($id) {
        $id = (int)$id;
        $conn = Conexao::getConexao();

        $sql = "SELECT ID_cliente, nome, email, pontos, usuario, foto, moldura
                FROM cliente
                WHERE ID_cliente = ? LIMIT 1";

        try {
            if ($conn instanceof PDO) {
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id]);
                $dados = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                // assume mysqli
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    error_log("Perfil::buscarPorId prepare failed: " . $conn->error);
                    return null;
                }
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $res = $stmt->get_result();
                $dados = $res && $res->num_rows ? $res->fetch_assoc() : null;
            }

            if ($dados) {
                $perfil = new Perfil();
                $perfil->ID_cliente = (int)$dados['ID_cliente'];
                $perfil->id = $perfil->ID_cliente; // duplicar por compatibilidade
                $perfil->nome = $dados['nome'] ?? '';
                $perfil->email = $dados['email'] ?? '';
                $perfil->pontos = isset($dados['pontos']) ? (int)$dados['pontos'] : 0;
                $perfil->usuario = $dados['usuario'] ?? '';
                $perfil->foto = $dados['foto'] ?? null;
                $perfil->moldura = $dados['moldura'] ?? '';

                return $perfil;
            }

            return null;
        } catch (Exception $e) {
            error_log("Perfil::buscarPorId erro: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Salvar/atualizar perfil.
     * Retorna true em sucesso, false em falha.
     * Observação: espera que $this->id (ou $this->ID_cliente) esteja preenchido.
     */
    public function salvar() {
        $id = (int)($this->id ?? $this->ID_cliente ?? 0);
        if ($id <= 0) return false;

        $conn = Conexao::getConexao();

        $sql = "UPDATE cliente
                SET nome = ?, usuario = ?, foto = ?, moldura = ?
                WHERE ID_cliente = ?";

        try {
            if ($conn instanceof PDO) {
                $stmt = $conn->prepare($sql);
                return $stmt->execute([
                    $this->nome,
                    $this->usuario,
                    $this->foto,
                    $this->moldura,
                    $id
                ]);
            } else {
                // mysqli
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    error_log("Perfil::salvar prepare failed: " . $conn->error);
                    return false;
                }
                $stmt->bind_param(
                    "ssssi",
                    $this->nome,
                    $this->usuario,
                    $this->foto,
                    $this->moldura,
                    $id
                );
                return (bool)$stmt->execute();
            }
        } catch (Exception $e) {
            error_log("Perfil::salvar erro: " . $e->getMessage());
            return false;
        }
    }
}
