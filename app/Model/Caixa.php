<?php 
namespace App\Model;

use PDOException;

class Caixa extends Model
{   
    private array $form_obrigatorio;
    private int $form_obrigatorio_quantidade;
    private string $novoCliente = 'sim';
    private array $ultimo_cliente = [];
    private array $form_valido = [];

    public function cadastrar($dados)
    {
        $this->form_obrigatorio = [
            'produto_id', 'quantidade', 'total', 'subTotal', 'pagamento',
            'valor', 'troco', 'btn_cadastrar'
        ];

        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $dados['total'] = $this->removerFormatoDinheiro($dados['total']);
            $dados['valor'] = $this->removerFormatoDinheiro($dados['valor']);
            $dados['troco'] = $this->removerFormatoDinheiro($dados['troco']);

            foreach ($dados['subTotal'] as $key => $value) {
                $dados['subTotal'][$key] = $this->removerFormatoDinheiro($value);
            }

            // Validação dos campos
            $this->arrayFormValido('int', $dados['produto_id'], 'produto_id', '*Id inválido', 1);
            $this->arrayFormValido('int', $dados['quantidade'], 'quantidade', '*Quantidade inválida', 1);
            $this->arrayFormValido('float', $dados['subTotal'], 'subTotal', '*SubTotal inválido', 1);

            array_push($this->form_valido,
                parent::valida_float($dados['total'], 'total', '*Total informado inválido', 0),
                parent::valida_float($dados['troco'], 'troco', '*Troco informado inválido', 0)
            );

            if (parent::formularioValido($this->form_valido)) {
                try {
                    $this->processarVenda($dados);
                } catch (PDOException $e) {
                    $this->conn->rollBack();
                    $_SESSION['msg'] = parent::alertaFalha($e->getMessage());
                }
            } else {
                $_SESSION['Erro_form']['total'] = '*Total divergente, tente novamente!';
                $_SESSION['msg'] = parent::alertaFalha('*Total divergente, tente novamente!');
            }
        } else {
            $_SESSION['msg'] = parent::alertaFalha('Verifique todos os campos e tente novamente.');
        }
    }

    private function processarVenda($dados)
    {
        $this->conn->beginTransaction();
        $data = date('Y-m-d H:i:s');

        // Processar produtos e atualizar estoque
        foreach ($dados['produto_id'] as $key => $idProduto) {
            $this->processarProduto($idProduto, $dados['quantidade'][$key], $dados['subTotal'][$key], $data);
        }

        // Fechar a venda no caixa
        parent::implementar("INSERT INTO caixa (id, cliente_id, total, pagamento, valor, troco, dt_registro) VALUES (:id, :cliente_id, :total, :pagamento, :valor, :troco, :dt_registro)", [
            'id' => null,
            'cliente_id' => $this->ultimo_cliente[0]['cliente'],
            'total' => $dados['total'],
            'pagamento' => $dados['pagamento'],
            'valor' => $dados['valor'],
            'troco' => $dados['troco'],
            'dt_registro' => $data
        ]);

        $this->conn->commit();
        $_SESSION['msg'] = parent::alertaSucesso('Compra concluída com sucesso!');
    }

    private function processarProduto($idProduto, $quantidade, $subTotal, $data)
    {
        $produto = parent::projetarExpecifico("SELECT quantidade FROM estoque WHERE produto_id = :produto_id", ['produto_id' => $idProduto], true);
        
        if ($produto['quantidade'] < $quantidade) {
            $_SESSION['form-quantidade'][] = '*Estoque insuficiente para o produto ID ' . $idProduto;
            throw new PDOException('Estoque insuficiente.');
        }

        $novoEstoque = $produto['quantidade'] - $quantidade;

        parent::implementar("UPDATE estoque SET quantidade = :quantidade WHERE produto_id = :produto_id", [
            'quantidade' => $novoEstoque,
            'produto_id' => $idProduto
        ]);

        parent::implementar("INSERT INTO produtos_caixa (id, cliente, produto_id, quantidade, subTotal, dt_registro) VALUES (:id, :cliente, :produto_id, :quantidade, :subTotal, :dt_registro)", [
            'id' => null,
            'cliente' => $this->ultimo_cliente[0]['cliente'],
            'produto_id' => $idProduto,
            'quantidade' => $quantidade,
            'subTotal' => $subTotal,
            'dt_registro' => $data
        ]);
    }

    private function removerFormatoDinheiro($valor): float
    {
        $valor = str_replace(['R$', '.', ','], ['', '', '.'], $valor);
        return (float) $valor;
    }
}
