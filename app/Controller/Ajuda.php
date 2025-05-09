<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigView;

class Ajuda extends Controller
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $dados = [
            'titulo' => 'Central de Ajuda',
            'descricao' => 'Encontre respostas para suas dúvidas sobre o sistema.'
        ];

        $view = new ConfigView("ajuda/index", $dados);
        $view->renderizar();
    }

    public function enviar(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception("Requisição inválida.");
            }

            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $mensagem = $_POST['mensagem'] ?? '';

            if (empty($nome) || empty($email) || empty($mensagem)) {
                throw new \Exception("Todos os campos são obrigatórios.");
            }

            // Simula envio 
            error_log("Solicitação de ajuda recebida: Nome: {$nome}, Email: {$email}, Mensagem: {$mensagem}");

            $_SESSION['msg'] = "Sua solicitação foi enviada com sucesso! Em breve responderemos.";
        } catch (\Exception $e) {
            error_log("Erro ao enviar solicitação de ajuda: " . $e->getMessage());
            $_SESSION['msg'] = "Erro ao enviar sua solicitação. Tente novamente.";
        }

        $this->redirecionar("ajuda");
    }
}
