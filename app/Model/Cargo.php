<?php
namespace App\Model;

use PDOException;

class Cargo extends Model
{
    private array $formValido = [];

    /**
     * Lista todos os cargos
     *
     * @return array|null Lista de cargos ou null se nenhum encontrado
     */
    public function listar(): ?array
    {
        try {
            return parent::projetarTodos("SELECT id, nome FROM `cargo`") ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao listar os cargos: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtém os dados de um cargo específico para edição
     *
     * @param int $id ID do cargo
     * @return array|null Dados do cargo ou null se não encontrado
     */
    public function editar(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $this->formValido = [parent::valida_int($id, 'id', '*ID do cargo inválido.', 1)];

        if (parent::formularioValido($this->formValido)) {
            try {
                return parent::projetarEspecifico(
                    "SELECT id, nome FROM `cargo` WHERE id = :id LIMIT 1",
                    ['id' => $id],
                    false
                ) ?: null;
            } catch (PDOException $e) {
                error_log("Erro ao buscar o cargo: " . $e->getMessage());
                return null;
            }
        }

        return null;
    }

    /**
     * Cadastra um novo cargo
     *
     * @param array $dados Dados do cargo
     * @return bool Sucesso ou falha
     */
    public function cadastrar(array $dados): bool
    {
        if (!isset($dados['nome']) || empty(trim($dados['nome']))) {
            $_SESSION['msg_erro'] = "O nome do cargo não pode estar vazio.";
            return false;
        }

        // Permite letras, números, espaços e "-"
        if (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s-]+$/', $dados['nome'])) {
            $_SESSION['msg_erro'] = "O nome do cargo contém caracteres inválidos.";
            return false;
        }

        $this->formValido = [
            parent::valida_tamanho($dados['nome'], 'nome', '*Informe um cargo válido, limite 45 caracteres.', 45, 2)
        ];

        if (!parent::formularioValido($this->formValido)) {
            return false;
        }

        try {
            parent::implementar("INSERT INTO cargo (nome) VALUES (:nome)", [
                'nome' => trim($dados['nome'])
            ]);
            $_SESSION['msg_sucesso'] = "Cargo cadastrado com sucesso!";
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar o cargo: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao cadastrar o cargo. Verifique se já existe um cargo com esse nome.";
            return false;
        }
    }

    /**
     * Exclui um cargo
     *
     * @param int $id ID do cargo
     * @return bool Sucesso ou falha
     */
    public function excluir(int $id): bool
    {
        if ($id <= 0) {
            $_SESSION['msg_erro'] = "ID do cargo inválido.";
            return false;
        }

        // Verifica se há funcionários vinculados ao cargo
        $vinculos = parent::projetarEspecifico("SELECT COUNT(*) AS total FROM funcionarios WHERE cargo_id = :id", ['id' => $id], true);

        if ($vinculos['total'] > 0) {
            $_SESSION['msg_erro'] = "Não é possível excluir o cargo, pois está vinculado a funcionários.";
            return false;
        }

        try {
            parent::implementar("DELETE FROM `cargo` WHERE id = :id", ['id' => $id]);
            $_SESSION['msg_sucesso'] = "Cargo excluído com sucesso!";
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao excluir o cargo: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao excluir o cargo.";
            return false;
        }
    }

    /**
     * Atualiza os dados de um cargo
     *
     * @param array $dados Dados do cargo
     * @return bool Sucesso ou falha
     */
    public function atualizar(array $dados): bool
    {
        if (!isset($dados['id']) || $dados['id'] <= 0 || !isset($dados['nome']) || empty(trim($dados['nome']))) {
            $_SESSION['msg_erro'] = "Dados inválidos para atualização.";
            return false;
        }

        // Permite letras, números, espaços e "-"
        if (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s-]+$/', $dados['nome'])) {
            $_SESSION['msg_erro'] = "O nome do cargo contém caracteres inválidos.";
            return false;
        }

        // Verifica se já existe um cargo com o mesmo nome
        $cargoExistente = parent::projetarEspecifico("SELECT id FROM cargo WHERE nome = :nome AND id != :id", [
            'nome' => trim($dados['nome']),
            'id' => $dados['id']
        ], true);

        if ($cargoExistente) {
            $_SESSION['msg_erro'] = "Já existe um cargo com esse nome.";
            return false;
        }

        $this->formValido = [
            parent::valida_int($dados['id'], 'id', '*ID do cargo inválido.', 1),
            parent::valida_tamanho($dados['nome'], 'nome', '*Nome do cargo inválido. Limite de 45 caracteres.', 45, 2)
        ];

        if (!parent::formularioValido($this->formValido)) {
            return false;
        }

        try {
            parent::implementar(
                "UPDATE cargo SET nome = :nome WHERE id = :id",
                ['id' => $dados['id'], 'nome' => trim($dados['nome'])]
            );
            $_SESSION['msg_sucesso'] = "Cargo atualizado com sucesso!";
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar o cargo: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao atualizar o cargo.";
            return false;
        }
    }
}
