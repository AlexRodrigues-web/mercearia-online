<?php 
namespace App\Model;

use PDO;
use PDOException;

class Conexao
{
    private string $bdType = "mysql";
    private string $host = "localhost";
    private string $user = "root";
    private string $password = "root";
    private int $port = 3306;
    private string $bdname = "mercearia";

    /**
     * Estabelece uma conexão com o banco de dados
     *
     * @return PDO Objeto de conexão PDO
     * @throws PDOException Lança exceção em caso de falha
     */
    protected function conectar(): PDO
    {
        try {
            $con = new PDO(
                "{$this->bdType}:host={$this->host};port={$this->port};dbname={$this->bdname}",
                $this->user,
                $this->password
            );
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $con;
        } catch (PDOException $e) {
            $this->tratarErroConexao($e);
        }
    }

    /**
     * Trata erros de conexão com o banco de dados
     *
     * @param PDOException $e Exceção lançada ao tentar conectar
     * @return void
     */
    private function tratarErroConexao(PDOException $e): void
    {
        $alerta = new Alerta();
        $msg = $alerta->alertaFalha("Não foi possível realizar uma conexão com o banco de dados. Tente novamente!");

        // Define a mensagem de erro para ser exibida ao usuário
        $_SESSION['msg'] = $msg;

        // Registra o erro em um log para depuração (opcional)
        error_log("Erro de conexão: " . $e->getMessage());
    }
}
