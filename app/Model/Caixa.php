<?php 
namespace App\Model;

use PDOException;

class Caixa extends Model
{   
    private array $formObrigatorio;
    private int $formObrigatorioQuantidade;
    private array $formValido = [];

    public function cadastrar(array $dados): array
    {
        $this->formObrigatorio = [
            'produto_id', 'quantidade', 'total', 'subTotal', 'pagamento',
            'valor', 'troco', 'btn_cadastrar'
        ];

        $this->formObrigatorioQuantidade = count($this->formObrigatorio);

        if (!$this->validarCamposFormulario($dados)) {
            return ['erro' => 'Por favor, preencha todos os campos corretamente.'];
        }

        // Sanitização dos valores de dinheiro
        $dados['total'] = $this->removerFormatoDinheiro($dados['total']);
        $dados['valor'] = $this->removerFormatoDinheiro($dados['valor']);
        $dados['troco'] = $this->removerFormatoDinheiro($dados['troco']);

        if (!empty($dados['subTotal']) && is_array($dados['subTotal'])) {
            foreach ($dados['subTotal'] as $key => $value) {
                $dados['subTotal'][$key] = $this->removerFormatoDinheiro($value);
            }
        } else {
            return ['erro' => 'Erro: valores de SubTotal inválidos.'];
        }

        // Validação dos campos obrigatórios
        $this->validarEntrada($dados);

        if (!parent::formularioValido($this->formValido)) {
            return ['erro' => 'Erro: total divergente. Tente novamente!'];
        }

        try {
            $this->processarVenda($dados);
            return ['sucesso' => 'Compra concluída com sucesso!'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Erro ao processar a venda: " . $e->getMessage());
            return ['erro' => 'Erro ao processar a venda. Tente novamente.'];
        }
    }

    private function validarCamposFormulario(array $dados): bool
    {
        foreach ($this->formObrigatorio as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return false;
            }
        }
        return true;
    }

    private function validarEntrada(array $dados): void
    {
        $this->arrayFormValido('int', $dados['produto_id'], 'produto_id', '*ID do produto inválido.', 1);
        $this->arrayFormValido('int', $dados['quantidade'], 'quantidade', '*Quantidade inválida.', 1);
        $this->arrayFormValido('float', $dados['subTotal'], 'subTotal', '*SubTotal inválido.', 1);
        $this->arrayFormValido('string', $dados['pagamento'], 'pagamento', '*Forma de pagamento inválida.', 3);
        $this->arrayFormValido('int', $dados['cliente_id'], 'cliente_id', '*ID do cliente inválido.', 1);

        array_push($this->formValido,
            parent::valida_float($dados['total'], 'total', '*Total informado inválido.', 0),
            parent::valida_float($dados['troco'], 'troco', '*Troco informado inválido.', 0)
        );
    }

    private function processarVenda(array $dados): void
    {
        if (empty($dados['cliente_id'])) {
            throw new PDOException('Erro: cliente não definido.');
        }

        if ($this->conn->inTransaction() === false) {
            $this->conn->beginTransaction();
        }

        $data = date('Y-m-d H:i:s');

        foreach ($dados['produto_id'] as $key => $idProduto) {
            $this->processarProduto($idProduto, $dados['quantidade'][$key], $dados['subTotal'][$key], $data, $dados['cliente_id']);
        }

        parent::implementar("INSERT INTO caixa (cliente_id, total, pagamento, valor, troco, dt_registro) 
            VALUES (:cliente_id, :total, :pagamento, :valor, :troco, :dt_registro)", [
            'cliente_id' => $dados['cliente_id'],
            'total' => $dados['total'],
            'pagamento' => $dados['pagamento'],
            'valor' => $dados['valor'],
            'troco' => $dados['troco'],
            'dt_registro' => $data
        ]);

        $this->conn->commit();
    }

    private function processarProduto(int $idProduto, int $quantidade, float $subTotal, string $data, int $clienteId): void
    {
        $produto = parent::projetarEspecifico("SELECT quantidade FROM estoque WHERE produto_id = :produto_id", ['produto_id' => $idProduto], true);
        
        if (!$produto || $produto['quantidade'] < $quantidade) {
            throw new PDOException("Estoque insuficiente para o produto ID {$idProduto}");
        }

        $novoEstoque = $produto['quantidade'] - $quantidade;

        parent::implementar("UPDATE estoque SET quantidade = :quantidade WHERE produto_id = :produto_id", [
            'quantidade' => $novoEstoque,
            'produto_id' => $idProduto
        ]);

        parent::implementar("INSERT INTO produtos_caixa (cliente, produto_id, quantidade, subTotal, dt_registro) 
            VALUES (:cliente, :produto_id, :quantidade, :subTotal, :dt_registro)", [
            'cliente' => $clienteId,
            'produto_id' => $idProduto,
            'quantidade' => $quantidade,
            'subTotal' => $subTotal,
            'dt_registro' => $data
        ]);
    }

    private function removerFormatoDinheiro($valor): float
    {
        $valor = preg_replace('/[^0-9,]/', '', $valor); // Remove caracteres indesejados
        $valor = str_replace('.', '', $valor); // Remove pontos separadores de milhar
        $valor = str_replace(',', '.', $valor); // Converte ',' para '.'
        return (float) $valor;
    }
}
