<?php
require_once "../Conexao/Conexao.php";

class Conquista {

    // 🔹 Buscar banner e nome do jogo
    public static function buscarBanner($idJogo) {
        $conn = Conexao::getConexao();

        $sql = "SELECT banner, nome FROM jogo WHERE ID_jogo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idJogo);
        $stmt->execute();

        $resultado = $stmt->get_result()->fetch_assoc();

        if ($resultado) {
            return [
                "banner" => $resultado["banner"],
                "nome_jogo" => $resultado["nome"]
            ];
        }

        return null;
    }

    // 🔹 Listar conquistas com ordenação
    public static function listarPorJogo($idJogo, $ordem) {
        $conn = Conexao::getConexao();

        switch ($ordem) {
            case "mais_pontos":
                $orderBy = "ORDER BY pontos DESC";
                break;
            case "menos_pontos":
                $orderBy = "ORDER BY pontos ASC";
                break;
            case "az":
            default:
                $orderBy = "ORDER BY nome ASC";
                break;
        }

        $sql = "SELECT * FROM conquista WHERE ID_jogo = ? $orderBy";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idJogo);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
