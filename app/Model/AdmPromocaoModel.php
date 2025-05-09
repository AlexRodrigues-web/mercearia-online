<?php
namespace App\Model;

use PDO;
use PDOException;

class AdmPromocaoModel extends Model
{
    public function listarPromocoes(): array
    {
        try {
            error_log("[AdmPromocaoModel] 🔍 Listando promoções...");

            $sql = "SELECT 
                        promocao.*, 
                        produto.nome AS nome_produto 
                    FROM promocao 
                    INNER JOIN produto ON produto.id = promocao.produto_id
                    ORDER BY promocao.inicio DESC";

            $promocoes = $this->projetarTodos($sql) ?? [];

            error_log("[AdmPromocaoModel] 📦 Total de promoções encontradas: " . count($promocoes));
            return $promocoes;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao listar promoções", $e);
            return [];
        }
    }

    public function buscarNomeProduto(int $id): ?array
    {
        try {
            $sql = "SELECT nome FROM produto WHERE id = :id LIMIT 1";
            return $this->projetarEspecifico($sql, ['id' => $id]) ?: null;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao buscar nome do produto", $e);
            return null;
        }
    }

    public function buscarPorId(int $id): ?array
    {
        try {
            error_log("[AdmPromocaoModel] 🔍 Buscando promoção ID: $id");

            $sql = "SELECT 
                        promocao.*, 
                        produto.nome AS nome_produto 
                    FROM promocao 
                    INNER JOIN produto ON produto.id = promocao.produto_id
                    WHERE promocao.id = :id 
                    LIMIT 1";

            $promo = $this->projetarEspecifico($sql, ['id' => $id]);

            return $promo ?: null;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao buscar promoção por ID", $e);
            return null;
        }
    }

    public function salvarPromocao(array $dados): bool
{
    try {
        if (empty($dados['produto_id'])) {
            error_log("[AdmPromocaoModel] ❌ produto_id está vazio ou inválido.");
            return false;
        }

        $sql = "INSERT INTO promocao 
                (produto_id, desconto, tipo, selo, inicio, fim, ativo, vis_home, vis_banner, vis_pagina) 
                VALUES 
                (:produto_id, :desconto, :tipo, :selo, :inicio, :fim, :ativo, :vis_home, :vis_banner, :vis_pagina)";

        // Garantir que os campos obrigatórios estejam definidos, mesmo que vazios
        $params = [
            'produto_id' => $dados['produto_id'],
            'desconto'   => $dados['desconto'] ?? 0,
            'tipo'       => $dados['tipo'] ?? 'percentual',
            'selo'       => $dados['selo'] ?? null,
            'inicio'     => substr($dados['inicio'] ?? '', 0, 10), // só a data (YYYY-MM-DD)
            'fim'        => substr($dados['fim'] ?? '', 0, 10),
            'ativo'      => 1,
            'vis_home'   => isset($dados['vis_home']) ? 1 : 0,
            'vis_banner' => isset($dados['vis_banner']) ? 1 : 0,
            'vis_pagina' => isset($dados['vis_pagina']) ? 1 : 0,
        ];

        $ok = $this->implementar($sql, $params);
        error_log("[AdmPromocaoModel] ✅ Inserção " . ($ok ? "bem-sucedida" : "falhou"));
        return $ok;
    } catch (PDOException $e) {
        $this->registrarErro("Erro ao salvar promoção", $e);
        return false;
    }
}


    public function atualizarPromocao(array $dados): bool
    {
        try {
            if (empty($dados['id']) || empty($dados['produto_id'])) {
                error_log("[AdmPromocaoModel] ❌ ID ou produto_id ausente para atualização.");
                return false;
            }

            error_log("[AdmPromocaoModel] 🔁 Atualizando promoção ID: {$dados['id']}");

            $sql = "UPDATE promocao
                    SET produto_id = :produto_id,
                        desconto = :desconto,
                        inicio = :inicio,
                        fim = :fim,
                        ativo = :ativo
                    WHERE id = :id";

            $ok = $this->implementar($sql, $dados);
            error_log("[AdmPromocaoModel] ✅ Atualização " . ($ok ? "realizada" : "falhou"));
            return $ok;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao atualizar promoção", $e);
            return false;
        }
    }

    // ✅ Método compatível com o controller
    public function atualizar(array $dados): bool
    {
        return $this->atualizarPromocao($dados);
    }

    public function excluirPromocao(int $id): bool
    {
        try {
            error_log("[AdmPromocaoModel] 🗑️ Excluindo promoção ID: $id");

            $sql = "DELETE FROM promocao WHERE id = :id";
            return $this->implementar($sql, ['id' => $id]);
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao excluir promoção", $e);
            return false;
        }
    }

    private function registrarErro(string $msg, PDOException $e): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['msg_erro']) || !is_array($_SESSION['msg_erro'])) {
            $_SESSION['msg_erro'] = [];
        }

        $_SESSION['msg_erro'][] = $msg;
        error_log("[AdmPromocaoModel] $msg: " . $e->getMessage());
    }
}
