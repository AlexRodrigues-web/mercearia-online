<?php
// Verificação de sessão e inclusão de arquivos essenciais
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login");
    exit;
}

// Caminhos corretos para os arquivos necessários
$cssBootstrapPath = '/app/Assets/css/bootstrap.min.css';
$jsBootstrapPath = '/app/Assets/js/bootstrap.min.js';
$customJsPath = '/app/Assets/js/customCargo.js';

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
    <title>Editar Cargo</title>
    <link rel="stylesheet" href="<?= $cssBootstrapPath ?>">
    <script src="<?= $jsBootstrapPath ?>" defer></script>
    <script src="<?= $customJsPath ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Editar Cargo</h1>
        <form action="/cargo/atualizar" method="POST">
            <input type="hidden" name="id" value="<?= $cargo['id'] ?>">
            <div class="form-group">
                <label for="nome">Nome do Cargo</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    class="form-control" 
                    value="<?= htmlspecialchars($cargo['nome'], ENT_QUOTES, 'UTF-8') ?>" 
                    required>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="/cargo" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
