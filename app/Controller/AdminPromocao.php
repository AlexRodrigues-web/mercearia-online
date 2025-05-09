<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\AdmPromocaoModel;
use Core\ConfigView;

class AdminPromocao extends Controller
{
    private AdmPromocaoModel $promocao;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        parent::__construct();

        if (
            empty($_SESSION['usuario']['logado']) ||
            !in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'], true)
        ) {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores ou funcionários.";
            header("Location: " . BASE_URL . "login");
            exit();
        }

        $this->promocao = new AdmPromocaoModel();
    }

    public function index(): void
    {
        $promocoes = $this->promocao->listarPromocoes();
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);
        $this->renderizarView("admin/promocao/index", [
            'promocoes' => $promocoes,
            'msg'       => $msg
        ]);
    }

    public function novo(): void
    {
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);

        $produtos = (new \App\Model\ProdutoModel())->listarTodosProdutos();
        $this->renderizarView("admin/promocao/novo", [
            'msg' => $msg,
            'produtos' => $produtos
        ]);
    }

    public function salvar(): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        error_log("[AdminPromocao::salvar] Dados recebidos: " . print_r($dados, true));

        if (!$this->verificarTokenCSRF()) {
            $this->setMensagem("Token CSRF inválido!", "danger");
            $this->redirect("adminpromocao/novo");
            return;
        }

        $produto_id = $dados['produto_id'] ?? null;
        $tipo = $dados['tipo'] ?? 'percentual';
        $desconto = 0;

        switch ($tipo) {
            case 'percentual':
                $desconto = floatval($dados['desconto_percentual'] ?? 0);
                if ($desconto <= 0 || $desconto > 100) {
                    $this->setMensagem("Desconto percentual inválido!", "warning");
                    $this->redirect("adminpromocao/novo");
                    return;
                }
                break;

            case 'fixo':
                $desconto = floatval($dados['desconto_fixo'] ?? 0);
                if ($desconto <= 0) {
                    $this->setMensagem("Desconto fixo inválido!", "warning");
                    $this->redirect("adminpromocao/novo");
                    return;
                }
                break;

            case 'fretegratis':
            case 'ofertadodia':
            case 'compre2leve3':
                break;

            default:
                $this->setMensagem("Tipo de promoção inválido!", "danger");
                $this->redirect("adminpromocao/novo");
                return;
        }

        $dadosInsercao = [
            'produto_id' => $produto_id,
            'desconto'   => $desconto,
            'tipo'       => $tipo,
            'selo'       => $dados['selo'] ?? null,
            'inicio'     => $dados['dt_inicio'] ?? null,
            'fim'        => $dados['dt_fim'] ?? null,
            'ativo'      => 1,
            'vis_home'   => !empty($dados['vis_home'])   ? 1 : 0,
            'vis_banner' => !empty($dados['vis_banner']) ? 1 : 0,
            'vis_pagina' => !empty($dados['vis_pagina']) ? 1 : 0,
        ];
        error_log("[AdminPromocao::salvar] Inserindo: " . print_r($dadosInsercao, true));

        if ($this->promocao->salvarPromocao($dadosInsercao)) {
            $this->setMensagem("Promoção salva com sucesso!", "sucesso");
            $this->redirect("adminpromocao");
        } else {
            $this->setMensagem("Erro ao salvar promoção!", "erro");
            $this->redirect("adminpromocao/novo");
        }
    }

    public function editar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['msg_erro'] = "ID inválido.";
            $this->redirect("adminpromocao");
        }

        $promo = $this->promocao->buscarPorId($id);
        if (!$promo) {
            $_SESSION['msg_erro'] = "Promoção não encontrada.";
            $this->redirect("adminpromocao");
        }

        $produto = $this->promocao->buscarNomeProduto($promo['produto_id'] ?? 0);
        $promo['nome_produto'] = $produto['nome'] ?? 'Produto desconhecido';

        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);

        $this->renderizarView("admin/promocao/editar", [
            'promo' => $promo,
            'msg'   => $msg
        ]);
    }

    public function atualizar(): void
    {
        error_log("AdminPromocao::atualizar() chamado");
        $dados = $_POST;
        error_log("Dados recebidos para atualização: " . print_r($dados, true));

        $id = (int)($dados['id'] ?? 0);
        $produto_id = (int)($dados['produto_id'] ?? 0);
        if ($id <= 0 || $produto_id <= 0) {
            $this->setMensagem("ID ou produto inválido!", "danger");
            $this->redirect("adminpromocao");
        }

        $promo = [
            'id'         => $id,
            'produto_id' => $produto_id,
            'desconto'   => floatval($dados['desconto'] ?? 0),
            'inicio'     => $dados['inicio'] ?? '',
            'fim'        => $dados['fim'] ?? '',
            'ativo'      => isset($dados['ativo']) ? 1 : 0
        ];

        $sucesso = $this->promocao->atualizar($promo);
        $this->setMensagem(
            $sucesso ? "Promoção atualizada com sucesso!" : "Falha ao atualizar promoção!",
            $sucesso ? "success" : "danger"
        );
        $this->redirect("adminpromocao");
    }

    public function excluir(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->setMensagem("ID inválido.", "danger");
            $this->redirect("adminpromocao");
        }

        $resultado = $this->promocao->excluirPromocao($id);
        $this->setMensagem(
            $resultado ? "Promoção excluída com sucesso!" : "Erro ao excluir promoção.",
            $resultado ? "success" : "danger"
        );
        $this->redirect("adminpromocao");
    }

    protected function redirect(string $rota): void
    {
        parent::redirect($rota);
    }

    protected function setMensagem(string $mensagem, string $tipo = 'info'): void
    {
        parent::setMensagem($mensagem, $tipo);
    }
}
