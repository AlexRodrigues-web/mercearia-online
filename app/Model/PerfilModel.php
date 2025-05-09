<?php

namespace App\Model;

use PDOException;

class PerfilModel extends Model
{
    private string $nomeFotoSalva = '';

    public function obterDadosUsuario(int $usuarioId): array
    {
        try {
            return parent::projetarEspecifico(
                "SELECT id, nome, email, foto FROM usuarios WHERE id = :id LIMIT 1",
                ['id' => $usuarioId]
            ) ?? [];
        } catch (PDOException $e) {
            error_log("[Erro Perfil] Erro ao obter dados do usuário: " . $e->getMessage());
            return [];
        }
    }

    public function atualizar(array $dados, string $tokenCSRF): bool
    {
        error_log("[DEBUG - PerfilModel] Início atualização");
        $this->validarSessao($tokenCSRF);

        $dados['id'] = $_SESSION['usuario_id'];

        error_log("[DEBUG - PerfilModel] Dados recebidos: " . print_r($dados, true));

        $senhaDB = parent::projetarEspecifico(
            "SELECT senha FROM usuarios WHERE id = :id LIMIT 1",
            ['id' => $dados['id']]
        );

        if (!$senhaDB || !password_verify(trim($dados['senhaAtual']), $senhaDB['senha'] ?? '')) {
            $this->definirMensagemSessao('msg_error', "A senha atual está incorreta.");
            error_log("[DEBUG - PerfilModel] Senha atual inválida.");
            return false;
        }

        if (!empty($dados['senha'])) {
            if ($dados['senha'] !== $dados['senhaRepetida']) {
                $this->definirMensagemSessao('msg_error', "A nova senha e a confirmação não coincidem.");
                return false;
            }
            $senhaCriptografada = password_hash(trim($dados['senha']), PASSWORD_DEFAULT);
        }

        if (!empty($_FILES['foto']['name'])) {
            $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nomeArquivo = uniqid('perfil_') . '.' . $extensao;
            $destino = __DIR__ . "/../Assets/image/perfil/" . $nomeArquivo;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                $dados['foto'] = $nomeArquivo;
                $this->nomeFotoSalva = $nomeArquivo; 
                error_log("[DEBUG - PerfilModel] Foto salva como: {$nomeArquivo}");
            } else {
                $this->definirMensagemSessao('msg_error', "Erro ao salvar a foto.");
                return false;
            }
        }

        $campos = [
            'nome' => trim($dados['nome'] ?? ''),
            'email' => trim($dados['email'] ?? ''),
            'id' => $dados['id']
        ];
        if (!empty($senhaCriptografada)) {
            $campos['senha'] = $senhaCriptografada;
        }
        if (!empty($dados['foto'])) {
            $campos['foto'] = $dados['foto'];
        }

        unset($dados['senha'], $dados['senhaAtual'], $dados['senhaRepetida']);

        try {
            $sql = "UPDATE usuarios SET nome = :nome, email = :email";
            if (isset($campos['senha'])) {
                $sql .= ", senha = :senha";
            }
            if (isset($campos['foto'])) {
                $sql .= ", foto = :foto";
            }
            $sql .= " WHERE id = :id";

            error_log("[DEBUG - PerfilModel] SQL gerado: $sql");
            parent::implementar($sql, $campos);

            $_SESSION['usuario']['nome'] = $campos['nome'];
            $_SESSION['usuario']['email'] = $campos['email'];
            if (!empty($campos['foto'])) {
                $_SESSION['usuario']['foto'] = $campos['foto'];
                error_log("[Perfil] Sessão atualizada com nova foto (final): " . $campos['foto']);
            }

            error_log("[DEBUG - PerfilModel] Atualização concluída e sessão atualizada");
            return true;
        } catch (PDOException $e) {
            error_log("[Erro Perfil] " . $e->getMessage());
            $this->definirMensagemSessao('msg_error', "Erro ao atualizar perfil.");
            return false;
        }
    }

    public function getNomeArquivoSalvo(): string
    {
        return $this->nomeFotoSalva;
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

        if (!isset($_SESSION['usuario_id'])) {
            $this->definirMensagemSessao('msg_error', "Erro de sessão. Faça login novamente.");
            exit();
        }
    }

    private function definirMensagemSessao(string $tipo, mixed $mensagem): void
    {
        $_SESSION[$tipo] = is_array($mensagem) ? $mensagem : [$mensagem];
    }
}
