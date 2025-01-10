<?php
// Verificação de sessão e inclusão de arquivos essenciais
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login");
    exit;
}

// Caminhos corretos para os arquivos necessários
$cssBootstrapPath = '/app/Assets/css/bootstrap.min.css';
$jsBootstrapPath = '/app/Assets/js/bootstrap.min.js';
$customJsPath = '/app/Assets/js/customCaixa.js';

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
    <title>Caixa</title>
    <link rel="stylesheet" href="<?= $cssBootstrapPath ?>">
    <script src="<?= $jsBootstrapPath ?>" defer></script>
    <script src="<?= $customJsPath ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Caixa</h1>
        <form action="/caixa/processarVenda" method="POST">
            <div class="form-group">
                <label for="produto_id">Produto</label>
                <select name="produto_id" id="produto_id" class="form-control" required>
                    <option value="">Selecione um produto</option>
                    <!-- Preenchimento dinâmico de opções -->
                    <?php foreach ($produtos as $produto): ?>
                        <option value="<?= $produto['id'] ?>"><?= $produto['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantidade">Quantidade</label>
                <input type="number" name="quantidade" id="quantidade" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="valor">Valor</label>
                <input type="text" name="valor" id="valor" class="form-control" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Finalizar Venda</button>
        </form>
    </div>
</body>
</html>
