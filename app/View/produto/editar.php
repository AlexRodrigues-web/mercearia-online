<?php
// Verificação de sessão e inclusão de arquivos essenciais
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login");
    exit;
}

// Caminhos corretos para os arquivos necessários
$cssBootstrapPath = '/app/Assets/css/bootstrap.min.css';
$jsBootstrapPath = '/app/Assets/js/bootstrap.min.js';
$customJsPath = '/app/Assets/js/customProduto.js';

// Garantir que os arquivos existem antes de incluí-los
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $cssBootstrapPath) || 
    !file_exists($_SERVER['DOCUMENT_ROOT'] . $jsBootstrapPath) || 
    !file_exists($_SERVER['DOCUMENT_ROOT'] . $customJsPath)) {
    die("Erro: Recursos necessários não encontrados. Contate o administrador do sistema.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="<?= $cssBootstrapPath ?>">
    <script src="<?= $jsBootstrapPath ?>" defer></script>
    <script src="<?= $customJsPath ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Editar Produto</h1>
        <form action="/produto/atualizar" method="POST">
            <input type="hidden" name="id" value="<?= $produto['id'] ?>">

            <div class="form-group">
                <label for="nome">Nome do Produto</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    class="form-control" 
                    value="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>" 
                    required 
                    maxlength="30">
            </div>

            <div class="form-group">
                <label for="preco">Preço</label>
                <input 
                    type="text" 
                    name="preco" 
                    id="preco" 
                    class="form-control" 
                    value="<?= htmlspecialchars(number_format($produto['preco'], 2, ',', '.'), ENT_QUOTES, 'UTF-8') ?>" 
                    required>
            </div>

            <div class="form-group">
                <label for="fornecedor_id">Fornecedor</label>
                <select name="fornecedor_id" id="fornecedor_id" class="form-control" required>
                    <option value="">Selecione</option>
                    <?php foreach ($fornecedores as $fornecedor): ?>
                        <option value="<?= $fornecedor['id'] ?>" <?= $produto['fornecedor_id'] == $fornecedor['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($fornecedor['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="quantidade">Quantidade</label>
                <input 
                    type="number" 
                    name="quantidade" 
                    id="quantidade" 
                    class="form-control" 
                    value="<?= htmlspecialchars($produto['quantidade'], ENT_QUOTES, 'UTF-8') ?>" 
                    required>
            </div>

            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="/produto" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
