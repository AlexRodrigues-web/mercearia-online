<?php

namespace Core;

class ConfigController
{
    private $url;
    private array $urlDividida;
    private string $urlMetodo = "index"; // Valor padrão
    private string $urlController = "login"; // Valor padrão

    public function __construct()
    {
        $this->url = filter_input(INPUT_GET, "url", FILTER_SANITIZE_URL);

        if (!empty($this->url)) {
            $this->urlDividida = explode('/', $this->url);
            $this->urlController = !empty($this->urlDividida[0]) ? ucfirst($this->urlDividida[0]) : "Login";
            $this->urlMetodo = !empty($this->urlDividida[1]) ? $this->urlDividida[1] : "index";
        }
    }

    public function carregar()
    {
        $paramArquivo = "./app/Controller/" . ucfirst($this->urlController) . ".php";
        $paramClasse = ucfirst($this->urlController);
        $paramMetodo = $this->urlMetodo;

        if (!$this->paginaExiste($paramArquivo, $paramClasse, $paramMetodo)) {
            $this->urlController = "PaginaInvalida";
            $this->urlMetodo = "index";
        }

        $classe = "\\App\\Controller\\" . $this->urlController;
        $metodo = $this->urlMetodo;

        if (class_exists($classe)) {
            $pagina = new $classe();
            if (method_exists($pagina, $metodo)) {
                $pagina->$metodo();
            } else {
                header("Location: /paginaInvalida");
                exit;
            }
        } else {
            header("Location: /paginaInvalida");
            exit;
        }
    }

    private function paginaExiste($paramArquivo, $paramClasse, $paramMetodo): bool
    {
        if (!file_exists($paramArquivo)) {
            return false;
        }

        $classe = "\\App\\Controller\\" . $paramClasse;
        if (!class_exists($classe)) {
            return false;
        }

        if (!method_exists($classe, $paramMetodo)) {
            return false;
        }

        return true;
    }

    private function permissao()
    {
        if ($this->urlController === "Admin") {
            $this->urlController = "PaginaInvalida";
        } else {
            $this->urlController = ucfirst($this->urlController);
        }
    }
}

?>
