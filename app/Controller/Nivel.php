<?php 
namespace App\Controller;

use App\Model\NivelModel;  // Importação do modelo NivelModel
use App\View\Pagina;       // Importação da view Pagina
use App\Model\Atualizar;   // Importação do modelo Atualizar

class Nivel extends Controller
{
    private $pagina;
    private $nivelModel;
    private $atualizar;
    private $dados;
    private $id;

    public function __construct()
    {
        $this->pagina = new Pagina();
        $this->nivelModel = new NivelModel();
        $this->atualizar = new Atualizar();
    }

    public function index()
    {
        $this->listar();
        $this->pagina->renderizar();
    }

    private function listar()
    {
        $this->dados = $this->nivelModel->listar();
    }

    public function editar()
    {
        $this->id['id'] = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$this->id['id']) {
            $_SESSION['msg'] = parent::alertaFalha('Nível não encontrado.');
            return;
        }

        $this->dados = $this->nivelModel->editar($this->id);
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
