<?php 
namespace App\Controller;

use App\View\Pagina; // Importação da view Pagina
use App\Model\HomeModel; // Importação do modelo HomeModel

class Home extends Controller
{
    private $pagina;
    private $homeModel;
    private $dados;

    public function __construct()
    {
        $this->pagina = new Pagina();
        $this->homeModel = new HomeModel();
    }

    public function index()
    {
        $this->dados = $this->homeModel->obterDadosIniciais();
        $this->pagina->renderizar($this->dados);
    }
}
