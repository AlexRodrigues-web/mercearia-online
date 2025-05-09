<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigView;

class Contato extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            $viewPath = "contato/index";

            if (!file_exists(__DIR__ . "/../View/{$viewPath}.php")) {
                throw new \Exception("A view {$viewPath} não foi encontrada.");
            }

            $view = new ConfigView($viewPath);
            $view->renderizar();
        } catch (\Exception $e) {
            error_log("Erro ao carregar a página Contato: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar a página Contato.";
            $this->redirecionar("erro");
        }
    }

    public function enviar(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception("Método inválido. Use POST.");
            }

            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $mensagem = trim($_POST['mensagem'] ?? '');

            if (empty($nome) || empty($email) || empty($mensagem)) {
                $_SESSION['msg_erro'] = "Preencha todos os campos obrigatórios.";
                $this->redirecionar("contato");
                return;
            }

            error_log("Novo contato recebido: Nome: {$nome}, Email: {$email}, Mensagem: {$mensagem}");

            $_SESSION['msg'] = "Mensagem enviada com sucesso! Em breve entraremos em contato.";
            $this->redirecionar("contato");
        } catch (\Exception $e) {
            error_log("Erro ao enviar mensagem de contato: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Ocorreu um erro ao enviar sua mensagem. Tente novamente.";
            $this->redirecionar("contato");
        }
    }
}
