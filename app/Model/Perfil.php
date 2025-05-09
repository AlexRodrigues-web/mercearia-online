<?php

namespace App\Model;

use PDOException;

class Perfil extends Model
{
    private array $form_obrigatorio = [];
    private int $form_obrigatorio_quantidade = 0;

    /**
     * Lista os dados do perfil do usuário.
     */
    public function listar(int $id): array
    {
        try {
            return parent::projetarEspecifico(
                "SELECT f.nome, c.nome AS cargo, n.nome AS nivel
                 FROM funcionario f
                 INNER JOIN cargo c ON f.cargo_id = c.id
                 INNER JOIN nivel n ON f.nivel_id = n.id
                 WHERE f.id = :id LIMIT 1",
                ['id' => $id]
            ) ?? [];
        } catch (PDOException $e) {
            error_log("[Erro Perfil] " . $e->getMessage());
            return [];
        }
    }

    public function atualizar(array $dados, string $tokenCSRF): void
    {
        $this->validarSessao($tokenCSRF);

        $this->form_obrigatorio = ['credencial', 'senhaAtual', 'btn_atualizar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (!parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $this->definirMensagemSessao('msg_error', "Preencha todos os campos obrigatórios.");
            return;
        }

        $dados['id'] = $_SESSION['usuario_id'];
        $dados['credencial'] = htmlspecialchars(trim($dados['credencial']), ENT_QUOTES, 'UTF-8');

        if (!$this->validarCredencial($dados['credencial'], $dados['id']) || !$this->validarSenhas($dados)) {
            return;
        }

        try {
            if (!empty($dados['senha'])) {
                $dados['senha'] = password_hash(trim($dados['senha']), PASSWORD_DEFAULT);
            } else {
                unset($dados['senha']);
            }

            unset($dados['btn_atualizar'], $dados['senhaAtual'], $dados['senhaRepetida']);

            parent::implementar(
                "UPDATE funcionario SET credencial = :credencial" . 
                (isset($dados['senha']) ? ", senha = :senha" : "") . 
                " WHERE id = :id",
                $dados
            );

            $this->definirMensagemSessao('msg_success', "Dados atualizados com sucesso!");
            header("Location: /perfil/editar?id=" . $dados['id']);
            exit();
        } catch (PDOException $e) {
            error_log("[Erro Perfil] " . $e->getMessage());
            $this->definirMensagemSessao('msg_error', "Erro ao atualizar perfil.");
        }
    }

    private function validarCredencial(string $credencial, int $id): bool
    {
        $usuario = parent::projetarEspecifico(
            "SELECT credencial FROM funcionario WHERE credencial = :credencial AND id != :id LIMIT 1",
            ['credencial' => $credencial, 'id' => $id]
        );

        if (!empty($usuario)) {
            $this->definirMensagemSessao('Erro_form', ['credencial' => 'Essa credencial já existe. Escolha outra.']);
            return false;
        }

        return true;
    }

    private function validarSenhas(array $dados): bool
    {
        if (!password_verify(trim($dados['senhaAtual']), $_SESSION['usuario_senha'])) {
            $this->definirMensagemSessao('Erro_form', ['senhaAtual' => 'A senha atual está incorreta.']);
            return false;
        }

        if (!empty($dados['senha']) && $dados['senha'] !== $dados['senhaRepetida']) {
            $this->definirMensagemSessao('Erro_form', ['senhaRepetida' => 'A nova senha e a confirmação não coincidem.']);
            return false;
        }

        return true;
    }

    private function validarSessao(string $tokenCSRF): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenCSRF)) {
            $this->definirMensagemSessao('msg_error', "Erro de segurança. Recarregue a página.");
            exit();
        }

        if (!isset($_SESSION['usuario_id'], $_SESSION['usuario_senha'])) {
            $this->definirMensagemSessao('msg_error', "Erro de sessão. Faça login novamente.");
            exit();
        }
    }

    private function definirMensagemSessao(string $tipo, mixed $mensagem): void
    {
        $_SESSION[$tipo] = is_array($mensagem) ? $mensagem : [$mensagem];
    }
}
