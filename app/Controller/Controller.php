<?php 

namespace App\Controller;

class Controller
{
    private ?object $alerta = null;
    private string $mensagem;

    public function __construct()
    {
        $this->alerta = new Alerta();
    }

    protected function alertaFalha($mensagem): string
    {
        if ($this->alerta === null) {
            throw new \Exception("Dependência 'alerta' não inicializada.");
        }
        $this->mensagem = $this->alerta->alertaFalha($mensagem);
        return $this->mensagem;
    }

    protected function alertaSucesso($mensagem): string
    {
        if ($this->alerta === null) {
            throw new \Exception("Dependência 'alerta' não inicializada.");
        }
        $this->mensagem = $this->alerta->alertaSucesso($mensagem);
        return $this->mensagem;
    }
}
