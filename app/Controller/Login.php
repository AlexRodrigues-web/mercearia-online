<?php 
namespace App\Controller;

use App\Model\LoginModel; // Importação do modelo LoginModel
use App\View\Pagina;      // Importação da view Pagina

class Login extends Controller
{
    private $pagina;
    private $loginModel;
    private $dados;

    public function __construct()
    {
        $this->pagina = new Pagina();
        $this->loginModel = new LoginModel();
    }

    public function index()
    {
        $this->pagina->renderizar();
    }

    public function autenticar()
    {
        $this->dados = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if ($this->dados) {
            $resultado = $this->loginModel->validarCredenciais($this->dados);
            if ($resultado) {
                $_SESSION['usuario'] = $resultado;
                header("Location: /home");
            } else {
                $_SESSION['msg'] = parent::alertaFalha("Usuário ou senha inválidos.");
                header("Location: /login");
            }
        } else {
            $_SESSION['msg'] = parent::alertaFalha("Preencha os campos corretamente.");
            header("Location: /login");
        }
    }

    public function sair()
    {
        session_destroy();
        header("Location: /login");
    }
}
