<?php 
namespace App\Controller;

use App\Model\PerfilModel; // Importação do modelo PerfilModel
use App\View\Pagina;       // Importação da view Pagina

class Perfil extends Controller
{
    private $pagina;
    private $perfilModel;
    private $dados;
    private $id;

    public function __construct()
    {
        $this->pagina = new Pagina();
        $this->perfilModel = new PerfilModel();
    }

    public function index()
    {
        $this->dados = $this->perfilModel->obterDadosUsuario($_SESSION['usuario']['id']);
        $this->pagina->renderizar($this->dados);
    }

    public function editar()
    {
        $this->id['id'] = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$this->id['id']) {
            $_SESSION['msg'] = parent::alertaFalha('Perfil não encontrado.');
            return;
        }

        $this->dados = $this->perfilModel->editar($this->id);
        $this->pagina->renderizar($this->dados);
    }

    public function atualizar()
    {
        $this->dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if ($this->dados) {
            $this->perfilModel->atualizar($this->dados);
        } else {
            $_SESSION['msg'] = parent::alertaFalha('Dados inválidos.');
        }
    }
}
