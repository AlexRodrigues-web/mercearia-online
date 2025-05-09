<?php
namespace App\Controller;

use Core\ConfigView;

class Configuracoes extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        $nivel = $_SESSION['usuario']['nivel_nome'] ?? '';
        if (!in_array($nivel, ['admin', 'funcionario'])) {
            $_SESSION['msg_erro'] = "Acesso negado! Permissão insuficiente.";
            $this->redirecionar("admin");
            exit();
        }
    }

    public function index(): void
    {
        $dados = [
            'titulo'  => 'Configurações do Sistema',
            'usuario' => $_SESSION['usuario']['nome'] ?? 'Administrador'
        ];
        (new ConfigView("configuracoes/index", $dados))->renderizar();
    }

    public function salvar(): void
    {
        $_SESSION['msg_sucesso'] = "Dados salvos com sucesso!";
        $this->redirecionar("admin");
    }
}
