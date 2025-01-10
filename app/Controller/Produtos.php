<?php 
namespace App\Controller;

use App\Model\ProdutoModel; // Importação do modelo ProdutoModel
use App\View\Pagina;        // Importação da view Pagina
use App\Model\Atualizar;    // Importação do modelo Atualizar

class Produtos extends Controller
{
    private $pagina;
    private $produtoModel;
    private $atualizar;
    private $dados;
    private $id;

    public function __construct()
    {
        $this->pagina = new Pagina();
        $this->produtoModel = new ProdutoModel();
        $this->atualizar = new Atualizar();
    }

    public function index()
    {
        $this->listar();
        $this->pagina->renderizar($this->dados);
    }

    private function listar()
    {
        $this->dados = $this->produtoModel->listar();
    }

    public function editar()
    {
        $this->id['id'] = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$this->id['id']) {
            $_SESSION['msg'] = parent::alertaFalha('Produto não encontrado.');
            return;
        }

        $this->dados = $this->produtoModel->editar($this->id);
        $this->pagina->renderizar($this->dados);
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
