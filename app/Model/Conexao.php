<?php
namespace App\Model;

use PDO;
use PDOException;

class Conexao
{
    private static ?PDO $conn = null; // Implementação Singleton

    private string $bdType = "mysql";
    private string $host;
    private string $user;
    private string $password;
    private int $port;
    private string $bdname;

    public function __construct()
    {
        $this->carregarVariaveisAmbiente();
        $this->validarVariaveis();
    }

    /**
     * Carrega as variáveis do .env caso exista.
     */
    private function carregarVariaveisAmbiente(): void
    {
        $envPath = __DIR__ . '/../../.env';

        if (!file_exists($envPath)) {
            error_log("⚠️ Arquivo .env não encontrado. Usando configurações padrão.");
            $env = [];
        } else {
            $env = parse_ini_file($envPath, false, INI_SCANNER_RAW);
            if (!$env) {
                error_log("⚠️ Falha ao carregar o arquivo .env. Usando configurações padrão.");
                $env = [];
            }
        }

        // Define os valores com fallback para segurança
        $this->host = $env['DB_HOST'] ?? 'localhost';
        $this->user = $env['DB_USER'] ?? 'root';
        $this->password = $env['DB_PASS'] ?? '';
        $this->bdname = $env['DB_NAME'] ?? 'mercearia';
        $this->port = isset($env['DB_PORT']) && is_numeric($env['DB_PORT']) ? (int) $env['DB_PORT'] : 3306;
    }

    /**
     * Verifica se as variáveis essenciais foram carregadas corretamente.
     */
    private function validarVariaveis(): void
    {
        if (empty($this->host) || empty($this->user) || empty($this->bdname)) {
            error_log("❌ Erro crítico: Configurações do banco de dados não estão corretamente definidas.");
            throw new PDOException("Erro crítico: Configurações do banco de dados inválidas.");
        }
    }

    /**
     * Estabelece uma conexão com o banco de dados (Singleton).
     *
     * @return PDO Objeto de conexão PDO
     * @throws PDOException Lança exceção em caso de falha
     */
    public static function conectar(): PDO
    {
        if (self::$conn === null) {
            try {
                $instance = new self(); // Instância única para carregar as variáveis
                $dsn = "{$instance->bdType}:host={$instance->host};port={$instance->port};dbname={$instance->bdname};charset=utf8mb4";
                self::$conn = new PDO($dsn, $instance->user, $instance->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false, // Segurança contra injeção SQL
                ]);
            } catch (PDOException $e) {
                self::logErroConexao($e);
                die("⚠️ Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
            }
        }

        return self::$conn;
    }

    /**
     * Registra erros de conexão com o banco de dados no log.
     *
     * @param PDOException $e Exceção lançada ao tentar conectar
     */
    private static function logErroConexao(PDOException $e): void
    {
        error_log("[Erro de Conexão] {$e->getMessage()} em {$e->getFile()}:{$e->getLine()}");
    }
}
