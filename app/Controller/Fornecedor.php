<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigView;
use App\Model\FornecedorModel;

class Fornecedor extends Controller
{
    private FornecedorModel $fornecedor;

    public function __construct()
    {
        parent::__construct();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        if (!in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'], true)) {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores ou funcionários podem acessar esta área.";
            $this->redirecionar("home");
            exit();
        }

        $this->fornecedor = new FornecedorModel();
    }

    public function index(): void
    {
        try {
            $fornecedores = $this->fornecedor->listar() ?? [];

            if (empty($fornecedores)) {
                $_SESSION['msg_info'] = "Nenhum fornecedor encontrado.";
            }

            // Gera token para exclusão
            if (empty($_SESSION['token'])) {
                $_SESSION['token'] = bin2hex(random_bytes(32));
            }

            $view = new ConfigView('fornecedor/index', ['fornecedores' => $fornecedores]);
            $view->renderizar();
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao listar fornecedores.");
        }
    }

    public function cadastrar(): void
    {
        try {
            $this->gerarCsrfToken();
            $view = new ConfigView('fornecedor/novo');
            $view->renderizar();
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao carregar o formulário de cadastro.");
        }
    }

    public function adicionar(): void
    {
        if (!$this->validarCsrfToken()) {
            $this->redirecionarComMensagem("fornecedor", "Requisição inválida.", "erro");
            return;
        }

        $dados = $this->filtrarDadosFornecedor();

        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem("fornecedor/cadastrar", "Dados inválidos.", "erro");
            return;
        }

        try {
            $resultado = $this->fornecedor->adicionar($dados);
            $this->redirecionarComMensagem(
                "fornecedor",
                $resultado ? "Fornecedor adicionado com sucesso!" : "Erro ao adicionar fornecedor.",
                $resultado ? "sucesso" : "erro"
            );
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao adicionar fornecedor.");
        }
    }

    public function editar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirecionarComMensagem("fornecedor", "ID do fornecedor inválido.", "erro");
            return;
        }

        try {
            $fornecedor = $this->fornecedor->buscarPorId($id);
            if (!$fornecedor) {
                $this->redirecionarComMensagem("fornecedor", "Fornecedor não encontrado.", "erro");
                return;
            }
            $view = new ConfigView('fornecedor/editar', ['fornecedor' => $fornecedor]);
            $view->renderizar();
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao carregar fornecedor.");
        }
    }

    public function atualizar(): void
    {
        if (!$this->validarCsrfToken()) {
            $this->redirecionarComMensagem("fornecedor", "Requisição inválida.", "erro");
            return;
        }

        $dados = $this->filtrarDadosFornecedor();
        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem(
                "fornecedor/editar?id=" . ($dados['id'] ?? ''),
                "Dados inválidos.",
                "erro"
            );
            return;
        }

        try {
            $resultado = $this->fornecedor->atualizar($dados);
            $this->redirecionarComMensagem(
                "fornecedor",
                $resultado ? "Fornecedor atualizado com sucesso." : "Erro ao atualizar fornecedor.",
                $resultado ? "sucesso" : "erro"
            );
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao atualizar fornecedor.");
        }
    }

    // Exclui um fornecedor.
    
    public function excluir(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionarComMensagem("fornecedor", "Método inválido.", "erro");
            return;
        }

        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (empty($_SESSION['token']) || !$token || $token !== $_SESSION['token']) {
            $this->redirecionarComMensagem("fornecedor", "Token inválido.", "erro");
            return;
        }
        unset($_SESSION['token']);

        // Lê ID
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirecionarComMensagem("fornecedor", "ID inválido.", "erro");
            return;
        }

        try {
            $sucesso = $this->fornecedor->excluir($id);
            $this->redirecionarComMensagem(
                "fornecedor",
                $sucesso ? "Fornecedor excluído com sucesso." : "Erro ao excluir fornecedor.",
                $sucesso ? "sucesso" : "erro"
            );
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao excluir fornecedor.");
        }
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];
        if (empty(trim($dados['nome'])) || strlen(trim($dados['nome'])) < 3) {
            $erros[] = "O campo 'Nome' é obrigatório e deve ter ao menos 3 caracteres.";
        }
        if (empty(trim($dados['nipc'])) || !preg_match('/^\d{9,15}$/', $dados['nipc'])) {
            $erros[] = "O campo 'NIPC' é obrigatório e deve conter apenas números (9 a 15 dígitos).";
        }
        if (!empty($erros)) {
            $_SESSION['msg_erro'] = implode('<br>', $erros);
            return false;
        }
        return true;
    }

    private function filtrarDadosFornecedor(): array
    {
        return filter_input_array(INPUT_POST, [
            'id'    => FILTER_VALIDATE_INT,
            'nome'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'nipc'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ]) ?? [];
    }

    private function tratarErro(\Exception $e, string $mensagemUsuario): void
    {
        error_log("[FornecedorController] $mensagemUsuario - " . $e->getMessage());
        $_SESSION['msg_erro'] = $mensagemUsuario;
        $this->redirecionar('erro');
    }

    protected function redirecionarComMensagem(string $rota, string $mensagem, string $tipo = 'erro'): void
    {
        $_SESSION['msg_' . $tipo] = $mensagem;
        $this->redirecionar($rota);
    }

    protected function redirecionar(string $rota): void
    {
        $rota = trim($rota, '/');
        $url = BASE_URL . $rota;
        header("Location: " . $url);
        exit();
    }

    private function validarCsrfToken(): bool
    {
        $csrfToken = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $valido = isset($_SESSION['csrf_token'])
               && $csrfToken
               && $csrfToken === $_SESSION['csrf_token'];
        unset($_SESSION['csrf_token']);
        return $valido;
    }

    private function gerarCsrfToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
