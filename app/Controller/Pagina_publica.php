<?php 
namespace App\Controller;

use App\Model\PaginaPublicaModel; // Importação do modelo PaginaPublicaModel
use App\View\Pagina;             // Importação da view Pagina

class Pagina_publica extends Controller
{
    private $pagina;
    private $paginaPublicaModel;
    private $dados;
    private $id;

    public function __construct()
    {
        $this->pagina = new Pagina();
        $this->paginaPublicaModel = new PaginaPublicaModel();
    }

    public function index()
    {
        $this->listar();
        $this->pagina->renderizar($this->dados);
    }

    private function listar()
    {
        $this->dados = $this->paginaPublicaModel->listar();
    }

    public function editar()
    {
        $this->id['id'] = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$this->id['id']) {
            $_SESSION['msg'] = parent::alertaFalha('Página pública não encontrada.');
            return;
        }

        $this->dados = $this->paginaPublicaModel->editar($this->id);
        $this->pagina->renderizar($this->dados);
    }

    public function atualizar()
    {
        $this->dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if ($this->dados) {
            $this->paginaPublicaModel->atualizar($this->dados);
        } else {
            $_SESSION['msg'] = parent::alertaFalha('Dados inválidos.');
        }
    }
}
