<?php

namespace App\Model;

use PDOException;

class FornecedorModel extends Model
{
    /**
     * Lista todos os fornecedores.
     *
     * @return array
     */
    public function listar(): array
    {
        try {
            $fornecedores = parent::projetarTodos(
                "SELECT id, nome, nipc, dt_registro, ativo
                 FROM fornecedor
                 ORDER BY nome"
            );

            return $fornecedores ?: [];
        } catch (PDOException $e) {
            error_log("Erro ao listar fornecedores: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca um fornecedor pelo ID.
     *
     * @param int $id
     * @return array|null
     */
    public function buscarPorId(int $id): ?array
    {
        try {
            $fornecedor = parent::projetarEspecifico(
                "SELECT id, nome, nipc, dt_registro, ativo
                 FROM fornecedor
                 WHERE id = :id
                 LIMIT 1",
                ['id' => $id]
            );

            return $fornecedor ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar fornecedor: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Adiciona um novo fornecedor.
     *
     * @param array $dados  Deve conter ao menos ['nome', 'nipc'].
     * @return bool
     */
    public function adicionar(array $dados): bool
    {
        try {
            $params = [
                'nome'        => $dados['nome']  ?? '',
                'nipc'        => $dados['nipc']  ?? '',
                'dt_registro' => date('Y-m-d H:i:s'),
                'ativo'       => 1
            ];

            error_log("Tentando adicionar fornecedor com dados: " . print_r($params, true));

            return parent::implementar(
                "INSERT INTO fornecedor (nome, nipc, dt_registro, ativo)
                 VALUES (:nome, :nipc, :dt_registro, :ativo)",
                $params
            );
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                error_log("Erro ao adicionar fornecedor: NIPC duplicado - " . $e->getMessage());
            } else {
                error_log("Erro ao adicionar fornecedor: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Atualiza um fornecedor existente.
     *
     * @param array $dados  Deve conter ['id', 'nome', 'nipc', 'ativo'].
     * @return bool
     */
    public function atualizar(array $dados): bool
    {
        try {
            $params = [
                'id'    => $dados['id'],
                'nome'  => $dados['nome'],
                'nipc'  => $dados['nipc'],
                'ativo' => isset($dados['ativo']) ? $dados['ativo'] : 1
            ];

            return parent::implementar(
                "UPDATE fornecedor
                 SET nome  = :nome,
                     nipc  = :nipc,
                     ativo = :ativo
                 WHERE id = :id",
                $params
            );
        } catch (PDOException $e) {
            error_log("Erro ao atualizar fornecedor: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui um fornecedor pelo ID.
     *
     * @param int $id
     * @return bool
     */
    public function excluir(int $id): bool
    {
        try {
            return parent::implementar(
                "DELETE FROM fornecedor WHERE id = :id",
                ['id' => $id]
            );
        } catch (PDOException $e) {
            error_log("Erro ao excluir fornecedor: " . $e->getMessage());
            return false;
        }
    }

    public function cadastrarView(): void
    {
        try {
            $view = new \Core\ConfigView("fornecedor/novo");
            $view->renderizar();
        } catch (\Throwable $e) {
            error_log("Erro ao renderizar view de cadastro de fornecedor: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro interno ao carregar a página de cadastro.";
            header("Location: /fornecedor");
            exit();
        }
    }
}
