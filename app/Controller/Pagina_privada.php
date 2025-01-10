<?php 
namespace App\Controller;

use App\Model\PaginaPrivadaModel; // Importação do modelo PaginaPrivadaModel
use App\View\Pagina;             // Importação da view Pagina

class Pagina_privada extends Controller
{
    private $pagina;
    private $paginaPrivadaModel;
    private $dados;
    private $id;

    public function __construct()
    {
        $this->pagina = new Pagina();
        $this->paginaPrivadaModel = new PaginaPrivadaModel();
    }

    public function index()
    {
        $this->listar();
        $this->pagina->renderizar($this->dados);
    }

    private function listar()
    {
        $this->dados = $this->paginaPrivadaModel->listar();
    }

    public function editar()
    {
        $this->id['id'] = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$this->id['id']) {
            $_SESSION['msg'] = parent::alertaFalha('Página privada não encontrada.');
            return;
        }

        $this->dados = $this->paginaPrivadaModel->editar($this->id);
        $this->pagina->renderizar($this->dados);
    }

    public function atualizar()
    {
        $this->dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if ($this->dados) {
            $this->paginaPrivadaModel->atualizar($this->dados);
        } else {
            $_SESSION['msg'] = parent::alertaFalha('Dados inválidos.');
        }
    }
}
