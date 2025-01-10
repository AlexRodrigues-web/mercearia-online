<?php
// Verificação de sessão e inclusão de arquivos essenciais
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login");
    exit;
}

// Caminhos corretos para os arquivos necessários
$cssBootstrapPath = '/app/Assets/css/bootstrap.min.css';
$jsBootstrapPath = '/app/Assets/js/bootstrap.min.js';
$customJsPath = '/app/Assets/js/eventos.js';

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
    <title>Home - Mercearia</title>
    <link rel="stylesheet" href="<?= $cssBootstrapPath ?>">
    <script src="<?= $jsBootstrapPath ?>" defer></script>
    <script src="<?= $customJsPath ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Bem-vindo à Mercearia</h1>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header bg-primary text-white">
                        Funcionários Ativos
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $dados['funcionarios_ativos'] ?? 0 ?></h5>
                        <p class="card-text">Número total de funcionários ativos no sistema.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header bg-success text-white">
                        Produtos em Estoque
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $dados['produtos_estoque'] ?? 0 ?></h5>
                        <p class="card-text">Quantidade total de produtos disponíveis.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header bg-danger text-white">
                        Vendas Totais
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">R$ <?= number_format($dados['vendas_totais'] ?? 0, 2, ',', '.') ?></h5>
                        <p class="card-text">Valor total das vendas realizadas.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
