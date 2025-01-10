<?php 
namespace App\Controller;

use App\View\Pagina; // Importação da view Pagina

class PaginaInvalida extends Controller
{
    private $pagina;

    public function __construct()
    {
        $this->pagina = new Pagina();
    }

    public function index()
    {
        $_SESSION['msg'] = parent::alertaFalha('A página que você tentou acessar é inválida ou não existe.');
        $this->pagina->renderizar();
    }
}
