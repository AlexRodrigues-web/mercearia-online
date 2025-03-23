<?php
namespace App\Controller;

class Erro extends Controller
{
    private const CODIGOS_PERMITIDOS = [400, 401, 403, 404, 500, 502, 503];

    public function __construct()
    {
        parent::__construct(); // Mantém compatibilidade com a classe base Controller
    }

    /**
     * Exibe a página de erro apropriada com um código e mensagem.
     *
     * @param int $codigoErro Código do erro (ex: 404, 500).
     * @param string $mensagem Mensagem personalizada do erro.
     */
    public function index(int $codigoErro = 404, string $mensagem = "Página não encontrada!")
    {
        $codigoErro = $this->validarCodigoErro($codigoErro);
        http_response_code($codigoErro);

        $mensagem = htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8');

        $this->renderizarErro($codigoErro, $mensagem);
    }

    /**
     * Renderiza a página de erro apropriada ou exibe uma mensagem genérica.
     *
     * @param int $codigoErro Código do erro HTTP.
     * @param string $mensagem Mensagem a ser exibida.
     */
    private function renderizarErro(int $codigoErro, string $mensagem): void
    {
        $caminhoView = __DIR__ . "/../../View/erro/{$codigoErro}.php";

        if (file_exists($caminhoView)) {
            $view = new ConfigView($caminhoView); $view->renderizar();
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
                    <a href='{$this->baseUrl}'>Voltar à página inicial</a>
                  </body>
                  </html>";
            exit;
        }
    }

    /**
     * Valida se o código de erro informado é válido.
     *
     * @param int $codigoErro Código HTTP informado.
     * @return int Código HTTP válido.
     */
    private function validarCodigoErro(int $codigoErro): int
    {
        return in_array($codigoErro, self::CODIGOS_PERMITIDOS, true) ? $codigoErro : 500;
    }
}
