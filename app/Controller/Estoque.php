<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\ProdutoModel;
use Core\ConfigView;

class Estoque extends Controller
{
    private ProdutoModel $produtoModel;

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
            $_SESSION['msg_erro'] = "Acesso negado! Apenas funcionários podem acessar o estoque.";
            $this->redirecionar("home");
            exit();
        }

        $this->produtoModel = new ProdutoModel();
    }

    public function index(): void
    {
        try {
            $produtos = $this->produtoModel->listar() ?: [];

            if (empty($produtos)) {
                $_SESSION['msg_erro'] = "Nenhum produto encontrado.";
            }

            $view = new ConfigView('estoque/index', ['produtos' => $produtos]);
            $view->renderizar();
        } catch (\Exception $e) {
            error_log("Erro ao listar produtos no estoque: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar o estoque.";
            $this->redirecionar("erro");
            exit();
        }
    }

    public function adicionar(): void
    {
        if (!$this->validarCsrf()) {
            $this->redirecionarComMensagem("estoque", "Requisição inválida.", "erro");
            exit();
        }

        $dados = $this->filtrarDadosProduto();

        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem("estoque", "Dados inválidos para adicionar produto.", "erro");
            exit();
        }

        try {
            $resultado = $this->produtoModel->adicionar($dados);

            $_SESSION[$resultado ? 'msg_sucesso' : 'msg_erro'] = $resultado
                ? "Produto adicionado com sucesso."
                : "Erro ao adicionar produto.";

            $this->redirecionar("estoque");
            exit();
        } catch (\Exception $e) {
            error_log("Erro ao adicionar produto ao estoque: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro inesperado ao adicionar o produto.";
            $this->redirecionar("estoque");
            exit();
        }
    }

    public function editar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $_SESSION['msg_erro'] = "ID inválido. Produto não encontrado.";
            $this->redirecionar("estoque");
            exit();
        }

        try {
            $produto = $this->produtoModel->buscarPorId($id);

            if (!$produto) {
                $_SESSION['msg_erro'] = "Produto não encontrado.";
                $this->redirecionar("estoque");
                exit();
            }

            $view = new ConfigView('estoque/editar', ['produto' => $produto]);
            $view->renderizar();
        } catch (\Exception $e) {
            error_log("Erro ao buscar produto no estoque: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar os dados do produto.";
            $this->redirecionar("estoque");
            exit();
        }
    }

    public function atualizar(): void
    {
        if (!$this->validarCsrf()) {
            $this->redirecionarComMensagem("estoque", "Requisição inválida.", "erro");
            exit();
        }

        $dados = $this->filtrarDadosProduto();

        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem("estoque/editar?id=" . ($dados['id'] ?? ''), "Dados inválidos para atualização.", "erro");
            exit();
        }

        try {
            $resultado = $this->produtoModel->atualizar($dados);

            $_SESSION[$resultado ? 'msg_sucesso' : 'msg_erro'] = $resultado
                ? "Produto atualizado com sucesso."
                : "Erro ao atualizar o produto.";

            $this->redirecionar("estoque");
            exit();
        } catch (\Exception $e) {
            error_log("Erro ao atualizar produto no estoque: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro inesperado ao atualizar o produto.";
            $this->redirecionar("estoque/editar?id=" . ($dados['id'] ?? ''));
            exit();
        }
    }

    private function filtrarDadosProduto(): array
    {
        return filter_input_array(INPUT_POST, [
            'id' => FILTER_VALIDATE_INT,
            'nome' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'quantidade' => FILTER_VALIDATE_INT,
            'preco' => FILTER_VALIDATE_FLOAT,
            'descricao' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ]) ?? [];
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];

        if (empty(trim($dados['nome'])) || strlen($dados['nome']) < 3) {
            $erros[] = "O campo 'Nome' é obrigatório e deve ter pelo menos 3 caracteres.";
        }

        if (!isset($dados['quantidade']) || !is_numeric($dados['quantidade']) || $dados['quantidade'] < 0) {
            $erros[] = "O campo 'Quantidade' deve ser um número válido maior ou igual a 0.";
        }

        if (!isset($dados['preco']) || !is_numeric($dados['preco']) || $dados['preco'] < 0) {
            $erros[] = "O campo 'Preço' deve ser um número válido maior ou igual a 0.";
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
        $urlDestino = BASE_URL . trim($rota, '/');

        if (!filter_var($urlDestino, FILTER_VALIDATE_URL)) {
            error_log("Tentativa de redirecionamento para URL inválida: " . $urlDestino);
            $_SESSION['msg_erro'] = "Erro ao redirecionar.";
            exit();
        }

        header("Location: " . $urlDestino);
        exit();
    }
}
