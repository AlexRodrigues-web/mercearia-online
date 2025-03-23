<?php
declare(strict_types=1);

namespace App\Model;

class CadastroModel extends Model
{
    /**
     * Cadastra um novo pedido na tabela de pedidos.
     *
     * @param array $dados Dados do pedido (produto, quantidade, cliente_id).
     * @return bool Retorna true se o cadastro for bem-sucedido, false caso contrário.
     */
    public function cadastrarPedido(array $dados): bool
    {
        try {
            $sql = "INSERT INTO pedidos (produto_id, quantidade, cliente_id, data_pedido) 
                    VALUES (:produto_id, :quantidade, :cliente_id, NOW())";

            return $this->implementar($sql, [
                'produto_id' => $dados['produto_id'],
                'quantidade' => $dados['quantidade'],
                'cliente_id' => $dados['cliente_id']
            ]);
        } catch (\Exception $e) {
            error_log("Erro ao cadastrar pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cadastra um novo cliente na tabela de clientes.
     *
     * @param array $dados Dados do cliente (nome, email, telefone).
     * @return bool Retorna true se o cadastro for bem-sucedido, false caso contrário.
     */
    public function cadastrarCliente(array $dados): bool
    {
        try {
            $sql = "INSERT INTO clientes (nome, email, telefone, data_cadastro) 
                    VALUES (:nome, :email, :telefone, NOW())";

            return $this->implementar($sql, [
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'telefone' => $dados['telefone']
            ]);
        } catch (\Exception $e) {
            error_log("Erro ao cadastrar cliente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cadastra qualquer dado em qualquer tabela.
     *
     * @param string $tabela Nome da tabela.
     * @param array $dados Dados a serem inseridos no formato ['coluna' => 'valor'].
     * @return bool Retorna true se o cadastro for bem-sucedido, false caso contrário.
     */
    public function cadastrarGenerico(string $tabela, array $dados): bool
    {
        try {
            $colunas = implode(', ', array_keys($dados));
            $valores = ':' . implode(', :', array_keys($dados));

            $sql = "INSERT INTO {$tabela} ({$colunas}) VALUES ({$valores})";

            return $this->implementar($sql, $dados);
        } catch (\Exception $e) {
            error_log("Erro ao cadastrar na tabela '{$tabela}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cadastra um novo registro na tabela especificada.
     *
     * @param string $tabela Nome da tabela onde os dados serão inseridos.
     * @param array $dados Dados a serem cadastrados no formato ['coluna' => 'valor'].
     * @return bool Retorna true se o cadastro for bem-sucedido, false caso contrário.
     */
    public function cadastrar(string $tabela, array $dados): bool
    {
        try {
            $colunas = implode(', ', array_keys($dados));
            $valores = ':' . implode(', :', array_keys($dados));

            $sql = "INSERT INTO {$tabela} ({$colunas}) VALUES ({$valores})";

            return $this->implementar($sql, $dados);
        } catch (\Exception $e) {
            error_log("Erro ao cadastrar na tabela '{$tabela}': " . $e->getMessage());
            return false;
        }
    }
}
