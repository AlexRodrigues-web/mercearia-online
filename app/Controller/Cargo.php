<?php 
namespace App\Controller;

class Cargo extends Controller
{
    private $tabela;
    private $pagina;
    private $cargo;
    private $atualizar;
    private $dados;
    private $id;

    public function __construct()
    {
        $this->tabela = new Tabela();
        $this->pagina = new Pagina();
        $this->cargo = new CargoModel();
        $this->atualizar = new Atualizar();
    }

    public function index()
    {
        $this->listar();
        $this->pagina->renderizar();
    }

    private function listar()
    {
        $this->dados = $this->tabela->listar();
    }

    public function editar()
    {
        $this->id['id'] = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$this->id['id']) {
            $_SESSION['msg'] = parent::alertaFalha('ID inválido.');
            return;
        }

        $this->dados = $this->cargo->editar($this->id);
        $this->pagina->renderizar();
    }

    public function atualizar()
    {
        $this->dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if ($this->dados) {
            $this->atualizar->atualizar($this->dados);
        } else {
            $_SESSION['msg'] = parent::alertaFalha('Dados inválidos.');
        }
    }
}
