<?php
// Verificação de sessão e inclusão de arquivos essenciais
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login");
    exit;
}

// Caminhos corretos para os arquivos necessários
$cssBootstrapPath = '/app/Assets/css/bootstrap.min.css';
$jsBootstrapPath = '/app/Assets/js/bootstrap.min.js';
$customJsPath = '/app/Assets/js/customPaginaPrivada.js';

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
    <title>Gestão de Páginas Privadas</title>
    <link rel="stylesheet" href="<?= $cssBootstrapPath ?>">
    <script src="<?= $jsBootstrapPath ?>" defer></script>
    <script src="<?= $customJsPath ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Gestão de Páginas Privadas</h1>
        <div class="mb-3">
            <a href="/pagina_privada/cadastrar" class="btn btn-success">Cadastrar Nova Página</a>
        </div>
        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($paginas)): ?>
                    <?php foreach ($paginas as $pagina): ?>
                        <tr>
                            <td><?= $pagina['id'] ?></td>
                            <td><?= htmlspecialchars($pagina['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="/pagina_privada/editar?id=<?= $pagina['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="/pagina_privada/excluir?id=<?= $pagina['id'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Deseja realmente excluir esta página privada?');">
                                    Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Nenhuma página privada cadastrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
