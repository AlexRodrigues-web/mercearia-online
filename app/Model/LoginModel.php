<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use PDOException;

class LoginModel extends Model
{
    public function buscarUsuario(string $credencial): array|false
    {
        $credencial = trim($credencial);
        if (empty($credencial)) {
            error_log("Erro ao validar login: credencial vazia.");
            return false;
        }

        try {
            $query = "SELECT id, nome, email, senha, nivel_id, foto, 'cliente'  AS tipo 
                      FROM usuarios 
                      WHERE email = :credencial 
                      LIMIT 1";

            $usuarioDados = $this->projetarEspecifico($query, ['credencial' => $credencial]);

            if ($usuarioDados) {
                error_log("Usuário encontrado em 'usuarios': " . print_r($usuarioDados, true));

                $usuarioDados['nivel'] = isset($usuarioDados['nivel_id'])
                    ? $this->mapearNivel((int) $usuarioDados['nivel_id'])
                    : 'cliente';

                error_log("Nível mapeado (usuarios): " . $usuarioDados['nivel']);
            } else {
                $query = "SELECT f.id, f.nome, f.credencial AS email, f.senha, f.nivel_id, 'funcionario' AS tipo
                          FROM funcionario f 
                          WHERE f.credencial = :credencial 
                          LIMIT 1";

                $usuarioDados = $this->projetarEspecifico($query, ['credencial' => $credencial]);

                if ($usuarioDados) {
                    error_log("Usuário encontrado em 'funcionario': " . print_r($usuarioDados, true));

                    $usuarioDados['nivel'] = $this->mapearNivel($usuarioDados['nivel_id']);
                    error_log("Nível mapeado (funcionario): " . $usuarioDados['nivel']);

                    unset($usuarioDados['nivel_id']);
                }
            }

            return $usuarioDados ?: false;
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário: " . $e->getMessage());
            return false;
        }
    }

    private function mapearNivel(int $nivelId): string
    {
        error_log("Mapeando nivel_id: $nivelId");
        return match ($nivelId) {
            1 => 'admin',
            2 => 'funcionario',
            default => 'funcionario',
        };
    }

    public function buscarPermissoes(int $usuarioId, string $tipo): array
    {
        try {
            if ($tipo === 'cliente') {
                $permissoes = $this->projetarLista(
                    "SELECT p.nome FROM usuario_pg_privada up
                     JOIN pg_privada p ON up.pg_privada_id = p.id
                     WHERE up.usuario_id = :usuario_id",
                    ['usuario_id' => $usuarioId]
                );
            } else {
                $permissoes = $this->projetarLista(
                    "SELECT p.nome FROM funcionario_pg_privada fp
                     JOIN pg_privada p ON fp.pg_privada_id = p.id
                     WHERE fp.funcionario_id = :usuario_id",
                    ['usuario_id' => $usuarioId]
                );
            }

            return !empty($permissoes) ? array_column($permissoes, 'nome') : ["pagina_publica", "perfil"];
        } catch (PDOException $e) {
            error_log("Erro ao buscar permissões do usuário: " . $e->getMessage());
            return ["pagina_publica", "perfil"];
        }
    }

    public function buscarNivelFuncionario(int $funcionarioId): string
    {
        try {
            $nivel = $this->projetarEspecifico(
                "SELECT n.nome FROM funcionario f 
                 JOIN nivel n ON f.nivel_id = n.id 
                 WHERE f.id = :id LIMIT 1",
                ['id' => $funcionarioId]
            );

            return $nivel['nome'] ?? 'funcionario';
        } catch (PDOException $e) {
            error_log("Erro ao buscar nível do funcionário: " . $e->getMessage());
            return 'funcionario';
        }
    }
}
