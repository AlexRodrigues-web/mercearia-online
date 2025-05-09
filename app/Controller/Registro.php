<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\UsuarioModel;
use Core\ConfigView;

class Registro extends Controller
{
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        parent::__construct();
        $this->usuarioModel = new UsuarioModel();
    }

    public function index(): void
    {
        if (!empty($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
            $this->redirecionar("home");
            return;
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $view = new ConfigView('registro/index', []);
        $view->renderizar();
    }

    public function cadastrar(): void
    {
        if (!$this->validarCsrfToken()) {
            $_SESSION['msg_erro'] = "Requisição inválida!";
            $this->redirecionar("registro");
            return;
        }

        $dados = filter_input_array(INPUT_POST, [
            'nome'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'email' => FILTER_VALIDATE_EMAIL,
            'senha' => FILTER_DEFAULT
        ]);

        if (!$dados || empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
            $_SESSION['msg_erro'] = "Preencha todos os campos corretamente.";
            $this->redirecionar("registro");
            return;
        }

        $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
        $dados['usuario_nivel'] = 'cliente'; 


        try {
            $resultado = $this->usuarioModel->cadastrar($dados);
            
            if ($resultado) {
                $_SESSION['msg_sucesso'] = "Cadastro realizado com sucesso! Você já pode fazer login.";
                $this->redirecionar("login");
            } else {
                $_SESSION['msg_erro'] = "Erro ao cadastrar. Tente novamente.";
                $this->redirecionar("registro");
            }
        } catch (\Exception $e) {
            error_log("Erro ao cadastrar usuário: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro inesperado. Contate o suporte.";
            $this->redirecionar("registro");
        }
    }

    private function validarCsrfToken(): bool
    {
        $csrf_token = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return isset($_SESSION['csrf_token']) && $csrf_token && $csrf_token === $_SESSION['csrf_token'];
    }
}
