<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigController;
use Core\ConfigView;
use App\Model\LoginModel;

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
        $this->loginModel = $loginModel ?? new LoginModel();
    }

    public function index()
    {
        if (!empty($_SESSION['usuario']['logado']) && $_SESSION['usuario']['logado'] === true) {
            $nivel = $_SESSION['usuario']['nivel_nome'] ?? 'visitante';

            error_log("[Login/index] Usuário já logado com nível: $nivel");

            $redirecionamento = in_array($nivel, ['funcionario', 'admin']) 
                ? "admin" 
                : "home";        

            error_log("[Login/index] Redirecionando para: $redirecionamento");

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

            error_log("Dados retornados do LoginModel: " . print_r($usuario, true));

            if ($usuario && is_array($usuario) && isset($usuario['id'], $usuario['senha'])) {
                if (password_verify($this->dados['senha'], $usuario['senha'])) {
                    unset($usuario['senha']);

                    error_log("Senha verificada. Iniciando sessão...");
                    $this->iniciarSessaoUsuario($usuario);

                    $nivel = $usuario['nivel'] ?? 'desconhecido';
                    $redirecionamento = ($nivel === 'admin' || $nivel === 'funcionario') 
                        ? "admin" 
                        : "home";

                    error_log("Redirecionamento após login: $redirecionamento");

                    header("Location: " . BASE_URL . $redirecionamento);
                    exit();
                } else {
                    error_log("Senha inválida.");
                }
            } else {
                error_log("Usuário não encontrado ou dados incompletos.");
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

    error_log("Iniciando sessão com usuário: " . print_r($usuario, true));

    if (isset($usuario['nivel_id']) && $usuario['nivel_id'] === 1) {
        $nivel_nome = 'admin';
    } elseif (isset($usuario['tipo']) && $usuario['tipo'] === 'funcionario') {
        $nivel_nome = $this->obterNomeNivelFuncionario($usuario['id']);
    } else {
        $nivel_nome = 'cliente';
    }

    error_log("Nivel nome identificado: $nivel_nome");

    $paginas = $this->buscarPermissoes($usuario['id'], $nivel_nome);

    $_SESSION['usuario'] = [
        'id' => $usuario['id'],
        'logado' => true,
        'nivel_id' => $usuario['nivel_id'] ?? null,
        'nivel_nome' => $nivel_nome,
        'paginas' => $paginas,
        'foto' => $usuario['foto'] ?? 'default.png', 
    ];

    error_log("Sessão final: " . print_r($_SESSION['usuario'], true));

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
