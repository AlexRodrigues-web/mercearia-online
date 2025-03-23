<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\ProdutoModel;
use App\Model\PedidoModel;
use Core\ConfigView;

class Caixa extends Controller
{
    private ProdutoModel $produtoModel;
    private PedidoModel $pedidoModel;

    public function __construct()
    {
        parent::__construct();

        // 🔒 Garante que a sessão está ativa antes de qualquer verificação
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // ✅ Verifica autenticação do usuário
        if (empty($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        // ✅ Apenas funcionários e administradores podem acessar
        if (!in_array($_SESSION['usuario_nivel'], ['admin', 'funcionario'], true)) {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas funcionários podem acessar o caixa.";
            $this->redirecionar("home");
            exit();
        }

        $this->produtoModel = new ProdutoModel();
        $this->pedidoModel = new PedidoModel();
    }

    public function index(): void
    {
        $dados = ['produtos' => []];

        if (!empty($_SESSION['caixa'])) {
            foreach ($_SESSION['caixa'] as $id => $quantidade) {
                try {
                    $produto = $this->produtoModel->buscarPorId((int)$id);
                    if ($produto) {
                        $produto['quantidade'] = (int)$quantidade;
                        $dados['produtos'][] = $produto;
                    }
                } catch (\Exception $e) {
                    error_log("Erro ao carregar produto ID {$id}: " . $e->getMessage());
                }
            }
        }

        if (empty($dados['produtos'])) {
            $_SESSION['msg_info'] = "Caixa vazio. Adicione produtos ao carrinho.";
        }

        $view = new ConfigView('caixa/index', $dados);
        $view->renderizar();
    }

    public function finalizarPedido(): void
    {
        if (!$this->validarCsrf()) {
            $this->redirecionarComMensagem("caixa", "Requisição inválida!", "erro");
            exit();
        }

        $dados = filter_input_array(INPUT_POST, [
            'cliente_id' => FILTER_VALIDATE_INT,
            'produtos' => ['filter' => FILTER_DEFAULT, 'flags' => FILTER_REQUIRE_ARRAY],
            'valor_total' => FILTER_VALIDATE_FLOAT,
            'metodo_pagamento' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]);

        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem("caixa", "Dados inválidos para finalizar o pedido.", "erro");
            exit();
        }

        try {
            $resultado = $this->pedidoModel->finalizarPedido($dados);

            if ($resultado) {
                unset($_SESSION['caixa']); // Limpa o carrinho após a finalização da compra
                $_SESSION['msg_sucesso'] = "Pedido finalizado com sucesso!";
            } else {
                $_SESSION['msg_erro'] = "Erro ao finalizar o pedido.";
            }
        } catch (\Exception $e) {
            error_log("Erro ao finalizar pedido: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro inesperado ao processar o pedido.";
        }

        $this->redirecionar("caixa");
        exit();
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];

        if (empty($dados['cliente_id']) || $dados['cliente_id'] <= 0) {
            $erros[] = "O campo 'Cliente' é obrigatório.";
        }

        if (empty($dados['produtos']) || !is_array($dados['produtos'])) {
            $erros[] = "Nenhum produto foi selecionado.";
        }

        if (empty($dados['valor_total']) || $dados['valor_total'] <= 0) {
            $erros[] = "O campo 'Valor Total' deve ser um número positivo.";
        }

        if (empty($dados['metodo_pagamento'])) {
            $erros[] = "O campo 'Método de Pagamento' é obrigatório.";
        }

        if (!empty($erros)) {
            $_SESSION['msg_erro'] = implode('<br>', $erros);
            return false;
        }

        return true;
    }

    private function validarCsrf(): bool
    {
        $csrfToken = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!isset($_SESSION['csrf_token']) || !$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
            $_SESSION['msg_erro'] = "Requisição inválida. O token CSRF não corresponde.";
            error_log("Tentativa de CSRF detectada.");
            $this->gerarCsrfToken();
            return false;
        }

        unset($_SESSION['csrf_token']);
        $this->gerarCsrfToken();
        return true;
    }

    private function gerarCsrfToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    private function redirecionar(string $rota): void
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

    private function redirecionarComMensagem(string $rota, string $mensagem, string $tipo = 'info'): void
    {
        $_SESSION["msg_{$tipo}"] = $mensagem;
        $this->redirecionar($rota);
    }
}
