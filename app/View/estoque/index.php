<?php
// Verificação de sessão e inclusão de arquivos essenciais
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login");
    exit;
}

// Caminhos corretos para os arquivos necessários
$cssBootstrapPath = '/app/Assets/css/bootstrap.min.css';
$jsBootstrapPath = '/app/Assets/js/bootstrap.min.js';
$customJsPath = '/app/Assets/js/customEstoque.js';

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
    <title>Gestão de Estoque</title>
    <link rel="stylesheet" href="<?= $cssBootstrapPath ?>">
    <script src="<?= $jsBootstrapPath ?>" defer></script>
    <script src="<?= $customJsPath ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Gestão de Estoque</h1>
        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($estoques)): ?>
                    <?php foreach ($estoques as $estoque): ?>
                        <tr>
                            <td><?= $estoque['id'] ?></td>
                            <td><?= htmlspecialchars($estoque['produto_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $estoque['quantidade'] ?></td>
                            <td>
                                <a href="/estoque/editar?id=<?= $estoque['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="/estoque/excluir?id=<?= $estoque['id'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Deseja realmente excluir este item do estoque?');">
                                    Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Nenhum produto cadastrado no estoque.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
