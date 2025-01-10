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
    <title>Gestão de Produtos</title>
    <link rel="stylesheet" href="<?= $cssBootstrapPath ?>">
    <script src="<?= $jsBootstrapPath ?>" defer></script>
    <script src="<?= $customJsPath ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Gestão de Produtos</h1>
        <div class="mb-3">
            <a href="/produto/cadastrar" class="btn btn-success">Cadastrar Novo Produto</a>
        </div>
        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Fornecedor</th>
                    <th>Quantidade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($produtos)): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td><?= $produto['id'] ?></td>
                            <td><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>R$ <?= htmlspecialchars(number_format($produto['preco'], 2, ',', '.'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($produto['fornecedor'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($produto['quantidade'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="/produto/editar?id=<?= $produto['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="/produto/excluir?id=<?= $produto['id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Deseja realmente excluir este produto?');">
                                    Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Nenhum produto cadastrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
