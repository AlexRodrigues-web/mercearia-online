<?php 
namespace App\Controller;

class Caixa extends Controller
{
    private $produto;
    private $pagina;
    private $cadastro;
    private $listar;

    public function __construct()
    {
        $this->produto = new Produto();
        $this->pagina = new Pagina();
        $this->cadastro = new Cadastro();
        $this->listar = new Listar();
    }

    public function index()
    {
        if (isset($_SESSION['caixa']) && !empty($_SESSION['caixa'])) {
            $id['id'] = array_key_first($_SESSION['caixa'] ?? []);
            $this->dados = $this->produto->produtoAjax($id);
        } else {
            $_SESSION['msg'] = parent::alertaFalha("Caixa vazio.");
        }
        $this->pagina->renderizar();
    }

    public function pedido()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if ($dados) {
            $this->cadastro->cadastrar($dados);
        }
    }

    public function listaProdutosAjax()
    {
        $pesquisa['pesquisa'] = '%' . filter_input(INPUT_POST, 'pesquisa', FILTER_SANITIZE_STRING) . '%';
        if ($pesquisa['pesquisa']) {
            $dados = $this->listar->listaProdutosAjax($pesquisa);
        }
    }

    public function produtoAjax()
    {
        $id['id'] = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id['id']) {
            if (!isset($_SESSION['caixa'][$id['id']])) {
                $_SESSION['caixa'][$id['id']] = 1;
            } else {
                $_SESSION['caixa'][$id['id']] += 1;
            }
            $dados = $this->produto->produtoAjax($id);
        }
    }

    public function remover()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id && isset($_SESSION['caixa'][$id])) {
            unset($_SESSION['caixa'][$id]);
        }
    }

    public function quantidade()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_VALIDATE_INT);
        if ($dados) {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
            if ($id && $quantidade) {
                $_SESSION['caixa'][$id] = intval($quantidade, 10);
                $array['id'] = $id;
                $dados = $this->produto->produtoAjax($array);
            }
        }
    }

    public function cancelarTudo()
    {
        if (isset($_SESSION['caixa'])) {
            unset($_SESSION['caixa']);
        }
    }
}
