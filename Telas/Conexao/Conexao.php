<?php
class Conexao {
    private static $host = "localhost";
    private static $user = "root";
    private static $pass = "";
    private static $db   = "skulljabb";
    private static $conn = null;

    public static function getConexao() {
        if (self::$conn === null) {
            self::$conn = new mysqli(self::$host, self::$user, self::$pass, self::$db);
            if (self::$conn->connect_error) {
                die("Erro de conexão: " . self::$conn->connect_error);
            }
            self::$conn->set_charset("utf8mb4");
        }
        return self::$conn;
    }
}
?>
