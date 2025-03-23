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

        // 🔒 Verifica se o usuário está autenticado
        if (empty($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        // ✅ Apenas administradores e funcionários podem acessar
        if (!in_array($_SESSION['usuario_nivel'], ['admin', 'funcionario'], true)) {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores e funcionários podem gerenciar fornecedores.";
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

            $view = new ConfigView('fornecedor/index', ['fornecedores' => $fornecedores]);
            $view->renderizar();
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao listar fornecedores.");
        }
    }

    public function adicionar()
    {
        if (!$this->validarCsrfToken()) {
            $this->redirecionarComMensagem("fornecedor", "Requisição inválida.", "erro");
            return;
        }

        $dados = $this->filtrarDadosFornecedor();

        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem("fornecedor/adicionar", "Dados inválidos.", "erro");
            return;
        }

        try {
            $resultado = $this->fornecedor->adicionar($dados);

            $mensagem = $resultado ? "Fornecedor adicionado com sucesso!" : "Erro ao adicionar fornecedor.";
            $tipoMensagem = $resultado ? "sucesso" : "erro";

            $this->redirecionarComMensagem("fornecedor", $mensagem, $tipoMensagem);
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao adicionar fornecedor.");
        }
    }

    public function editar()
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

    public function atualizar()
    {
        if (!$this->validarCsrfToken()) {
            $this->redirecionarComMensagem("fornecedor", "Requisição inválida.", "erro");
            return;
        }

        $dados = $this->filtrarDadosFornecedor();

        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem("fornecedor/editar?id=" . ($dados['id'] ?? ''), "Dados inválidos.", "erro");
            return;
        }

        try {
            $atualizado = $this->fornecedor->atualizar($dados);

            $mensagem = $atualizado ? "Fornecedor atualizado com sucesso." : "Erro ao atualizar fornecedor.";
            $this->redirecionarComMensagem("fornecedor", $mensagem, $atualizado ? "sucesso" : "erro");
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao atualizar o fornecedor.");
        }
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];

        if (empty(trim($dados['nome'])) || strlen($dados['nome']) < 3) {
            $erros[] = "O campo 'Nome' é obrigatório e deve ter pelo menos 3 caracteres.";
        }

        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = "O campo 'E-mail' é inválido.";
        }

        if (empty(trim($dados['telefone'])) || strlen($dados['telefone']) < 8) {
            $erros[] = "O campo 'Telefone' é obrigatório e deve ter pelo menos 8 caracteres.";
        }

        if (empty(trim($dados['endereco'])) || strlen($dados['endereco']) < 5) {
            $erros[] = "O campo 'Endereço' é obrigatório e deve ter pelo menos 5 caracteres.";
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
            'id' => FILTER_VALIDATE_INT,
            'nome' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'email' => FILTER_VALIDATE_EMAIL,
            'telefone' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'endereco' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ]) ?? [];
    }

    private function tratarErro(\Exception $e, string $mensagemUsuario): void
    {
        error_log("Erro na FornecedorController: " . $e->getMessage());
        $_SESSION['msg_erro'] = $mensagemUsuario;
        $this->redirecionar('erro');
    }

    private function redirecionarComMensagem(string $rota, string $mensagem, string $tipo = 'info'): void
    {
        $_SESSION["msg_{$tipo}"] = $mensagem;
        $this->redirecionar($rota);
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

    private function validarCsrfToken(): bool
    {
        $csrfToken = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return isset($_SESSION['csrf_token']) && $csrfToken && $csrfToken === $_SESSION['csrf_token'];
    }
}
