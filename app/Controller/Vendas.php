<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\VendaModel;
use Core\ConfigView;

class Vendas extends Controller
{
    private VendaModel $vendaModel;

    public function __construct()
    {
        parent::__construct();
        $this->vendaModel = new VendaModel();

        if (empty($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        if (!in_array($_SESSION['usuario_nivel'], ['admin', 'funcionario'], true)) {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas funcionários podem gerenciar vendas.";
            $this->redirecionar("home");
            exit();
        }
    }

    public function index(): void
    {
        try {
            $vendas = $this->vendaModel->buscarTodas() ?? [];

            if (empty($vendas)) {
                $_SESSION['msg_info'] = "Nenhuma venda encontrada.";
            }

            $view = new ConfigView("venda/index", ['vendas' => $vendas]);
            $view->renderizar();
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao carregar a lista de vendas.");
        }
    }

    public function finalizar(): void
    {
        if (!$this->validarCsrfToken()) {
            $this->redirecionarComMensagem("vendas", "Requisição inválida!", "erro");
            return;
        }

        $dados = filter_input_array(INPUT_POST, [
            'cliente_id' => FILTER_VALIDATE_INT,
            'valor_total' => FILTER_VALIDATE_FLOAT,
            'metodo_pagamento' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]);

        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem("vendas", "Dados inválidos!", "erro");
            return;
        }

        try {
            if (!$this->vendaModel->verificarEstoqueSuficiente($dados['cliente_id'])) {
                $this->redirecionarComMensagem("vendas", "Estoque insuficiente para concluir a venda!", "erro");
                return;
            }

            $resultado = $this->vendaModel->finalizarVenda($dados);

            $mensagem = $resultado ? "Venda finalizada com sucesso!" : "Erro ao finalizar a venda.";
            $this->redirecionarComMensagem("vendas", $mensagem, $resultado ? "sucesso" : "erro");
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao finalizar a venda.");
        }
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];

        if (empty($dados['cliente_id']) || $dados['cliente_id'] <= 0) {
            $erros[] = "O campo 'Cliente' é obrigatório.";
        }

        if (empty($dados['valor_total']) || $dados['valor_total'] <= 0) {
            $erros[] = "O campo 'Valor Total' deve ser um número positivo.";
        }

        if (empty($dados['metodo_pagamento']) || !in_array($dados['metodo_pagamento'], ['dinheiro', 'cartao', 'MBA'], true)) {
            $erros[] = "O método de pagamento selecionado não é válido.";
        }

        if (!empty($erros)) {
            $_SESSION['msg_erro'] = implode('<br>', $erros);
            return false;
        }

        return true;
    }

    private function tratarErro(\Exception $e, string $mensagemUsuario): void
    {
        error_log("Erro na VendasController: " . $e->getMessage());
        $_SESSION['msg_erro'] = $mensagemUsuario;
        $this->redirecionar('erro');
    }

    protected function redirecionarComMensagem(string $rota, string $mensagem, string $tipo = 'info'): void
    {
        $_SESSION["msg_{$tipo}"] = $mensagem;
        $this->redirecionar($rota);
    }

    protected function redirecionar(string $rota): void
    {
        $rota = trim($rota, '/');
        $urlDestino = BASE_URL . $rota;

        if (!filter_var($urlDestino, FILTER_VALIDATE_URL)) {
            error_log("Tentativa de redirecionamento para URL inválida: " . $urlDestino);
            $_SESSION['msg_erro'] = "Erro ao redirecionar.";
            exit();
        }

        header("Location: " . $urlDestino);
        exit();
    }

    private function validarCsrfToken(): bool
    {
        $csrfToken = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return isset($_SESSION['csrf_token']) && $csrfToken && $csrfToken === $_SESSION['csrf_token'];
    }
}
