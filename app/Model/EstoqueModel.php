<?php

namespace App\Model;

use PDOException;

class EstoqueModel extends Model
{
    public function listarTodos(): array
    {
        try {
            error_log("EstoqueModel::listarTodos() iniciado");

            return $this->projetarTodos(
                "SELECT 
                    p.id, 
                    p.nome AS produto_nome, 
                    p.categoria, 
                    p.preco, 
                    p.estoque, 
                    p.imagem 
                 FROM produto p
                 ORDER BY p.nome"
            ) ?? [];
        } catch (PDOException $e) {
            error_log("Erro ao listar estoque: " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId(int $id): ?array
    {
        try {
            error_log("EstoqueModel::buscarPorId() chamado com id={$id}");

            return $this->projetarEspecifico(
                "SELECT 
                    p.id, 
                    p.nome AS produto_nome, 
                    p.categoria, 
                    p.preco, 
                    p.estoque, 
                    p.imagem 
                 FROM produto p
                 WHERE p.id = :id
                 LIMIT 1",
                ['id' => $id]
            );
        } catch (PDOException $e) {
            error_log("Erro ao buscar produto no estoque: " . $e->getMessage());
            return null;
        }
    }

    public function atualizar(array $dados): bool
    {
        if (!isset($dados['id'], $dados['preco'])) {
            error_log("Dados ausentes em atualizar(): " . print_r($dados, true));
            return false;
        }

        error_log("EstoqueModel::atualizar() chamado com dados: " . print_r($dados, true));

        try {
            $resultado = $this->implementar(
                "UPDATE produto 
                 SET preco = :preco 
                 WHERE id = :id",
                [
                    'preco' => (float) $dados['preco'],
                    'id'    => (int)   $dados['id']
                ]
            );

            error_log("Resultado do UPDATE (preço): " . ($resultado ? 'Sucesso' : 'Falhou'));
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar preço do produto ID {$dados['id']}: " . $e->getMessage());
            return false;
        }
    }

    public function adicionarEntrada(int $id, int $quantidade): bool
    {
        if ($id <= 0 || $quantidade <= 0) {
            error_log("Dados inválidos em adicionarEntrada: id={$id}, quantidade={$quantidade}");
            return false;
        }

        error_log("EstoqueModel::adicionarEntrada() chamado com id={$id}, quantidade={$quantidade}");

        try {
            $resultado = $this->implementar(
                "UPDATE produto 
                 SET estoque = estoque + :quantidade 
                 WHERE id = :id",
                [
                    'quantidade' => $quantidade,
                    'id'         => $id
                ]
            );

            error_log("Resultado do UPDATE (entrada): " . ($resultado ? 'Sucesso' : 'Falhou'));
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao adicionar entrada de estoque para o produto ID {$id}: " . $e->getMessage());
            return false;
        }
    }

    public function remover(int $id): bool
    {
        if ($id <= 0) {
            error_log("ID inválido em remover(): {$id}");
            return false;
        }

        error_log("EstoqueModel::remover() chamado para ID {$id}");

        try {
            return $this->implementar(
                "DELETE FROM produto WHERE id = :id",
                ['id' => $id]
            );
        } catch (PDOException $e) {
            error_log("Erro ao remover produto do estoque ID {$id}: " . $e->getMessage());
            return false;
        }
    }

    public function importarCSV(string $filePath): array
    {
        $resultado = [
            'processadas' => 0,
            'sucesso'     => 0,
            'falhas'      => 0,
            'erros'       => []
        ];

        error_log("EstoqueModel::importarCSV() iniciado para: {$filePath}");

        if (!file_exists($filePath)) {
            $resultado['falhas']++;
            $resultado['erros'][] = "Arquivo CSV não encontrado.";
            error_log("Arquivo CSV não encontrado.");
            return $resultado;
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            fgetcsv($handle); 
            while (($linha = fgetcsv($handle, 1000, ',')) !== false) {
                $resultado['processadas']++;
                [$produto_id, $quantidade] = $linha;

                $produto_id = (int) $produto_id;
                $quantidade = (int) $quantidade;

                if ($produto_id <= 0 || $quantidade <= 0) {
                    $resultado['falhas']++;
                    $resultado['erros'][] = "Linha {$resultado['processadas']}: Dados inválidos.";
                    continue;
                }

                $ok = $this->implementar(
                    "UPDATE produto 
                     SET estoque = estoque + :quantidade 
                     WHERE id = :produto_id",
                    [
                        'quantidade' => $quantidade,
                        'produto_id' => $produto_id
                    ]
                );

                if ($ok) {
                    $resultado['sucesso']++;
                } else {
                    $resultado['falhas']++;
                    $resultado['erros'][] = "Linha {$resultado['processadas']}: Falha ao atualizar produto ID {$produto_id}.";
                }
            }
            fclose($handle);
        } else {
            $resultado['falhas']++;
            $resultado['erros'][] = "Não foi possível abrir o arquivo CSV.";
            error_log("Falha ao abrir o arquivo CSV.");
        }

        return $resultado;
    }

    // Método auxiliar para entrada via AJAX
    public function incrementarEstoque(int $id, int $quantidade): bool
    {
        return $this->adicionarEntrada($id, $quantidade);
    }

    // Método auxiliar para edição de preço via AJAX
    public function editarPreco(int $id, float $preco): bool
    {
        return $this->atualizar([
            'id'    => $id,
            'preco' => $preco
        ]);
    }
}
