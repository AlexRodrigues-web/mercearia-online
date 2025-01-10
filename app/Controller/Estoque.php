<?php 
namespace App\Controller;

use App\Model\Produto; // Importação do modelo Produto
use App\Model\Tabela;  // Importação do modelo Tabela
use App\View\Pagina;   // Importação da view Pagina
use App\Model\Atualizar; // Importação do modelo Atualizar

class Estoque extends Controller
{
    private $tabela;
    private $pagina;
    private $produto;
    private $atualizar;
    private $dados;
    private $id;

    public function __construct()
    {
        $this->tabela = new Tabela();
        $this->pagina = new Pagina();
        $this->produto = new Produto();
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
            $_SESSION['msg'] = parent::alertaFalha('Produto não encontrado.');
            return;
        }

        $this->dados = $this->produto->editar($this->id);
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
