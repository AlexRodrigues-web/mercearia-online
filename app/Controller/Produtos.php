<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\ProdutoModel;
use Core\ConfigView;

class Produtos extends Controller
{
    private ProdutoModel $produto;

    public function __construct()
    {
        parent::__construct();
        $this->produto = new ProdutoModel();
        error_log("[Produtos] Controller instanciado.");
    }

    public function index()
    {
        error_log("ACESSO À PÁGINA DE PRODUTOS - Controller acionado"); 

        try {
            error_log("[Produtos] Chamando listarTodosProdutos()");
            $produtos = $this->produto->listarTodosProdutos() ?? [];

            if (empty($produtos)) {
                $_SESSION['msg_erro'] = "Nenhum produto encontrado.";
                error_log("[Produtos] Nenhum produto encontrado.");
            }

            error_log("[Produtos] Renderizando view com " . count($produtos) . " produtos.");
            $view = new ConfigView('produto/index', ['produtos' => $produtos]); 
            $view->renderizar();
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao listar produtos.");
        }
    }

    public function adicionar()
    {
        $this->verificarAutenticacao();

        if (!$this->validarCsrfToken()) {
            error_log("[Produtos] CSRF inválido ao adicionar produto.");
            $this->redirecionarComMensagem("produto", "Requisição inválida.", "erro");
            return;
        }

        $dados = $this->filtrarDadosProduto();
        error_log("[Produtos] Dados recebidos para adicionar: " . print_r($dados, true));

        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem("produto/adicionar", "Dados inválidos.", "erro");
            return;
        }

        try {
            $resultado = $this->produto->adicionarProduto($dados);
            error_log("[Produtos] Resultado do adicionarProduto: " . var_export($resultado, true));
            $mensagem = $resultado ? "Produto adicionado com sucesso!" : "Erro ao adicionar produto.";
            $this->redirecionarComMensagem("produto", $mensagem, $resultado ? "sucesso" : "erro");
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao adicionar produto.");
        }
    }

    public function editar()
    {
        $this->verificarAutenticacao();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            error_log("[Produtos] ID inválido para edição.");
            $this->redirecionarComMensagem("produto", "ID do produto inválido.", "erro");
            return;
        }

        try {
            error_log("[Produtos] 🔍 Buscando produto para editar (ID: $id)");
            $produto = $this->produto->buscarPorId($id);
            if (!$produto) {
                error_log("[Produtos] Produto não encontrado.");
                $this->redirecionarComMensagem("produto", "Produto não encontrado.", "erro");
                return;
            }

            $view = new ConfigView('produto/editar', ['produto' => $produto]);
            $view->renderizar();
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao carregar produto.");
        }
    }

    public function atualizar()
    {
        $this->verificarAutenticacao();

        if (!$this->validarCsrfToken()) {
            error_log("[Produtos] CSRF inválido ao atualizar produto.");
            $this->redirecionarComMensagem("produto", "Requisição inválida.", "erro");
            return;
        }

        $dados = $this->filtrarDadosProduto();
        error_log("[Produtos] Dados recebidos para atualizar: " . print_r($dados, true));

        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem("produto/editar?id=" . ($dados['id'] ?? ''), "Dados inválidos.", "erro");
            return;
        }

        try {
            $atualizado = $this->produto->editarProduto($dados);
            error_log("[Produtos] Resultado da atualização: " . var_export($atualizado, true));
            $mensagem = $atualizado ? "Produto atualizado com sucesso." : "Erro ao atualizar produto.";
            $this->redirecionarComMensagem("produto", $mensagem, $atualizado ? "sucesso" : "erro");
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao atualizar o produto.");
        }
    }

    private function verificarAutenticacao(): void
    {
        if (empty($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
            error_log("[Produtos] Acesso negado - usuário não logado.");
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar essa funcionalidade.";
            $this->redirecionar("login");
            exit();
        }
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];

        if (empty(trim($dados['nome']))) {
            $erros[] = 'O campo "Nome" é obrigatório.';
        }

        if (!isset($dados['preco']) || !is_numeric($dados['preco']) || $dados['preco'] <= 0) {
            $erros[] = 'O campo "Preço" deve ser um número positivo.';
        }

        if (!isset($dados['quantidade']) || !is_numeric($dados['quantidade']) || $dados['quantidade'] < 1) {
            $erros[] = 'O campo "Quantidade" deve ser maior ou igual a 1.';
        }

        if (!empty($erros)) {
            error_log("[Produtos] Erros na validação: " . implode(" | ", $erros));
            $_SESSION['msg_erro'] = implode('<br>', $erros);
            return false;
        }

        return true;
    }

    private function filtrarDadosProduto(): array
    {
        $dados = filter_input_array(INPUT_POST, [
            'id' => FILTER_VALIDATE_INT,
            'nome' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'preco' => FILTER_VALIDATE_FLOAT,
            'quantidade' => FILTER_VALIDATE_INT,
            'descricao' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'imagem' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]) ?? [];

        error_log("[Produtos] Dados filtrados: " . print_r($dados, true));
        return $dados;
    }

    private function tratarErro(\Exception $e, string $mensagemUsuario): void
    {
        error_log("Erro na ProdutosController: " . $e->getMessage());
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
        $urlDestino = BASE_URL . "/" . $rota;

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
        $valido = isset($_SESSION['csrf_token']) && $csrfToken && $csrfToken === $_SESSION['csrf_token'];
        error_log("[Produtos] Validação CSRF: " . ($valido ? "válida" : "inválida"));
        return $valido;
    }
}
