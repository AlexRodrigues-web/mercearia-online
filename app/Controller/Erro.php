<?php
namespace App\Controller;

class Erro extends Controller
{
    private const CODIGOS_PERMITIDOS = [400, 401, 403, 404, 500, 502, 503];

    public function __construct()
    {
        parent::__construct();
    }

   
    public function index(): void
    {
        $codigoErro = (int) ($_GET['codigo'] ?? 404);
        $mensagem = $_GET['msg'] ?? "Página não encontrada!";

        $codigoErro = $this->validarCodigoErro($codigoErro);
        http_response_code($codigoErro);

        $mensagem = htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8');

        $this->renderizarErro($codigoErro, $mensagem);
    }

    private function renderizarErro(int $codigoErro, string $mensagem): void
    {
        $caminhoView = __DIR__ . "/../../View/erro/{$codigoErro}.php";

        if (file_exists($caminhoView)) {
            $view = new \Core\ConfigView("erro/{$codigoErro}", ['mensagem' => $mensagem]);
            $view->renderizar();
        } else {
            header("Content-Type: text/html; charset=UTF-8");
            echo "<!DOCTYPE html>
                  <html lang='pt-BR'>
                  <head>
                    <meta charset='UTF-8'>
                    <title>Erro {$codigoErro}</title>
                    <style>
                        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                        h1 { color: red; }
                        p { font-size: 18px; }
                        a { text-decoration: none; color: blue; }
                        a:hover { text-decoration: underline; }
                    </style>
                  </head>
                  <body>
                    <h1>Erro {$codigoErro}</h1>
                    <p>{$mensagem}</p>
                    <a href='" . BASE_URL . "'>Voltar à página inicial</a>
                  </body>
                  </html>";
            exit;
        }
    }

    private function validarCodigoErro(int $codigoErro): int
    {
        return in_array($codigoErro, self::CODIGOS_PERMITIDOS, true) ? $codigoErro : 500;
    }
}
