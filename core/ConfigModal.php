<?php
declare(strict_types=1);

namespace Core;

class ConfigModal
{
    private string $nomeView;
    private array $dados;

    public function __construct(string $nomeView, array $dados = [])
    {
        $this->nomeView = $nomeView;
        $this->dados = $dados;
    }

    public function renderizar(): void
    {
        if (file_exists(__DIR__ . "/../View/{$this->nomeView}.php")) {
            extract($this->dados);
            require __DIR__ . "/../View/{$this->nomeView}.php";
        } else {
            echo "<div class='alert alert-danger'>Erro: Modal {$this->nomeView} não encontrado!</div>";
        }
    }
}
