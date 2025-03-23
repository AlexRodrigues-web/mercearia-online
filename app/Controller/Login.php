<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigController;
use Core\ConfigView;
use App\Model\LoginModel; // ✅ Importa a classe correta do Model

class Login extends Controller
{
    private LoginModel $loginModel;
    private array $dados = [];

    public function __construct(LoginModel $loginModel = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        parent::__construct();
        $this->loginModel = $loginModel ?? new LoginModel(); // ✅ Garante que sempre há uma instância do Model
    }

    public function index()
    {
        if (!empty($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
            $redirecionamento = ($_SESSION['usuario_nivel'] === 'funcionario' || $_SESSION['usuario_nivel'] === 'admin') 
                ? "admin" 
                : "home";

            header("Location: " . BASE_URL . $redirecionamento);
            exit();
        }

        try {
            include_once __DIR__ . "/../View/login/index.php";
        } catch (\Exception $e) {
            error_log("Erro ao carregar a página de login: " . $e->getMessage());
            $this->redirecionarComMensagem("login/index", "Erro ao carregar a página de login.", "erro");
        }
    }

    public function autenticar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->validarCsrfToken()) {
            error_log("Erro de CSRF: Token inválido ou ausente.");
            $this->redirecionarComMensagem("login/index", "Requisição inválida.", "erro");
            exit();
        }

        $this->dados = filter_input_array(INPUT_POST, [
            'credencial' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'senha' => FILTER_DEFAULT
        ]) ?? [];

        if (empty($this->dados['credencial']) || empty($this->dados['senha'])) {
            $this->redirecionarComMensagem("login/index", "Preencha os campos corretamente.", "erro");
            exit();
        }

        try {
            $usuario = $this->loginModel->buscarUsuario($this->dados['credencial']);

            if ($usuario && is_array($usuario) && isset($usuario['id'], $usuario['senha'])) {
                if (password_verify($this->dados['senha'], $usuario['senha'])) {
                    unset($usuario['senha']);
                    $this->iniciarSessaoUsuario($usuario);

                    $redirecionamento = ($usuario['nivel'] === 'admin' || $usuario['nivel'] === 'funcionario') 
                        ? "admin" 
                        : "home";

                    header("Location: " . BASE_URL . $redirecionamento);
                    exit();
                }
            }
        } catch (\Exception $e) {
            error_log("Erro na autenticação: " . $e->getMessage());
        }

        $_SESSION['msg'] = "<div class='alert alert-danger'>Erro ao autenticar. Tente novamente.</div>";
        header("Location: " . BASE_URL . "login");
        exit();
    }

    private function iniciarSessaoUsuario(array $usuario)
    {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];

        // ✅ Correção: Obtendo o nível correto do usuário
        if ($usuario['tipo'] === 'funcionario') {
            $_SESSION['usuario_nivel'] = $this->obterNomeNivelFuncionario($usuario['id']);
        } else {
            $_SESSION['usuario_nivel'] = 'cliente';
        }

        $_SESSION['usuario_paginas'] = $this->buscarPermissoes($usuario['id'], $_SESSION['usuario_nivel']);
        $_SESSION['usuario_logado'] = true;
        session_regenerate_id(true);
    }

    private function obterNomeNivelFuncionario(int $id): string
    {
        $nivel = $this->loginModel->buscarNivelFuncionario($id);
        return $nivel ?: 'funcionario';
    }

    private function buscarPermissoes(int $usuarioId, string $nivel): array
    {
        try {
            if ($nivel === 'admin') {
                return ["pagina_privada", "pagina_publica", "funcionario", "fornecedor", "nivel", "configuracoes"];
            }

            $permissoes = $this->loginModel->projetarLista(
                "SELECT p.nome FROM funcionario_pg_privada fp
                 JOIN pg_privada p ON fp.pg_privada_id = p.id
                 WHERE fp.funcionario_id = :usuario_id",
                ['usuario_id' => $usuarioId]
            );

            return !empty($permissoes) ? array_column($permissoes, 'nome') : ["pagina_publica", "perfil"];
        } catch (\Exception $e) {
            error_log("Erro ao buscar permissões do usuário: " . $e->getMessage());
            return ["pagina_publica", "perfil"];
        }
    }

    private function gerarCsrfToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    private function validarCsrfToken(): bool
    {
        $csrf_token = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!isset($_SESSION['csrf_token']) || !$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
            return false;
        }
        unset($_SESSION['csrf_token']);
        return true;
    }

    protected function redirecionarComMensagem(string $rota, string $mensagem, string $tipo = 'info'): void
    {
        $_SESSION['msg'] = "<div class='alert alert-$tipo'>$mensagem</div>";
        header("Location: " . BASE_URL . $rota);
        exit();
    }
}
