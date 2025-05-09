<?php 
// Verificação de sessão e inclusão de arquivos essenciais
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login");
    exit;
}

// Caminhos corretos para os arquivos necessários
$cssBootstrapPath = '/assets/css/bootstrap.min.css';
$jsBootstrapPath = '/assets/js/bootstrap.min.js';
$customJsPath = '/assets/js/customProduto.js';

// Garantir que os arquivos existem antes de incluí-los
$requiredFiles = [$cssBootstrapPath, $jsBootstrapPath, $customJsPath];
foreach ($requiredFiles as $file) {
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
        error_log("Recurso não encontrado: $file");
        die("Erro: Recursos necessários não encontrados. Contate o administrador do sistema.");
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Produto</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($cssBootstrapPath, ENT_QUOTES, 'UTF-8') ?>">
    <script src="<?= htmlspecialchars($jsBootstrapPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars($customJsPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5 text-primary">Criar Novo Produto</h1>

        <!-- Mensagens de Feedback -->
        <?php if (!empty($_SESSION['msg_success'])): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($_SESSION['msg_success'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['msg_error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($_SESSION['msg_error'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg_error']); ?>
        <?php endif; ?>

        <!-- Formulário para Criação de Produto -->
        <form action="/produto/salvar" method="POST" class="mt-4 shadow p-4 bg-light rounded" id="formProduto">
            <!-- Campo Nome -->
            <div class="form-group">
                <label for="nome" class="font-weight-bold">Nome do Produto</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    class="form-control" 
                    required 
                    maxlength="30" 
                    placeholder="Digite o nome do produto"
                    aria-label="Nome do Produto">
            </div>

            <!-- Campo Preço -->
            <div class="form-group">
                <label for="preco" class="font-weight-bold">Preço</label>
                <input 
                    type="text" 
                    name="preco" 
                    id="preco" 
                    class="form-control preco-format" 
                    required 
                    placeholder="Digite o preço do produto"
                    aria-label="Preço do Produto">
                <small class="form-text text-muted">O preço deve ser inserido no formato correto (ex: 10,50).</small>
            </div>

            <!-- Campo Fornecedor -->
            <div class="form-group">
                <label for="fornecedor_id" class="font-weight-bold">Fornecedor</label>
                <select name="fornecedor_id" id="fornecedor_id" class="form-control" required aria-label="Fornecedor">
                    <option value="">Selecione</option>
                    <?php foreach ($fornecedores as $fornecedor): ?>
                        <option value="<?= htmlspecialchars($fornecedor['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($fornecedor['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Campo Quantidade -->
            <div class="form-group">
                <label for="quantidade" class="font-weight-bold">Quantidade</label>
                <input 
                    type="number" 
                    name="quantidade" 
                    id="quantidade" 
                    class="form-control" 
                    required 
                    min="0"
                    placeholder="Digite a quantidade do produto"
                    aria-label="Quantidade do Produto">
            </div>

            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary" aria-label="Salvar Produto">Salvar</button>
                <button type="reset" class="btn btn-warning" aria-label="Limpar Formulário">Limpar</button>
                <a href="/produto" class="btn btn-secondary" aria-label="Cancelar Cadastro">Cancelar</a>
            </div>
        </form>
    </div>

    <!-- Script para formatação do preço -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var precoInput = document.getElementById('preco');
            
            precoInput.addEventListener('input', function () {
                let valor = this.value.replace(/\D/g, ''); // Remove caracteres não numéricos
                let decimal = valor.slice(-2);
                let inteiro = valor.slice(0, -2);

                if (inteiro === '') {
                    this.value = '0,' + decimal;
                } else {
                    this.value = inteiro + ',' + decimal;
                }
            });
        });
    </script>
</body>
</html>
