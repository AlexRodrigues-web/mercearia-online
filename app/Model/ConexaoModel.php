<?php
namespace App\Model;

use PDO;
use PDOException;

class ConexaoModel
{
    private static ?PDO $conn = null; 

    private string $bdType = "mysql";
    private string $host;
    private string $user;
    private string $password;
    private int $port;
    private string $bdname;

    public function __construct()
    {
        error_log("🔄 ConexaoModel::__construct chamado.");
        $this->carregarVariaveisAmbiente();
        $this->validarVariaveis();
    }

    /**
     * Carrega as variáveis do .env caso exista e define como variáveis de ambiente.
     */
    private function carregarVariaveisAmbiente(): void
    {
        $envPath = __DIR__ . '/../../.env';

        if (file_exists($envPath)) {
            error_log("Arquivo .env encontrado. Carregando variáveis...");
            foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
                if (strpos($linha, '=') !== false) {
                    list($chave, $valor) = explode('=', $linha, 2);
                    $valor = trim($valor);
                    putenv("$chave=$valor");
                }
            }
        } else {
            error_log("Arquivo .env não encontrado. Usando configurações padrão.");
        }

        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
        $this->bdname = getenv('DB_NAME') ?: 'mercearia';
        $this->port = getenv('DB_PORT') ? (int) getenv('DB_PORT') : 3306;

        error_log("Variáveis carregadas: host={$this->host}, user={$this->user}, db={$this->bdname}, port={$this->port}");
    }

    /**
     * Verifica se as variáveis essenciais foram carregadas corretamente.
     */
    private function validarVariaveis(): void
    {
        if (empty($this->host) || empty($this->user) || empty($this->bdname)) {
            error_log("Erro crítico: Configurações do banco de dados não estão corretamente definidas.");
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
                error_log("Iniciando tentativa de conexão com o banco de dados...");
                $instance = new self(); // Instância única
                $dsn = "{$instance->bdType}:host={$instance->host};port={$instance->port};dbname={$instance->bdname};charset=utf8mb4";

                self::$conn = new PDO($dsn, $instance->user, $instance->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);

                error_log("Conexão com o banco de dados estabelecida com sucesso.");

            } catch (PDOException $e) {
                self::logErroConexao($e);
                die("Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
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
