<?php
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'], 2)), '/');
    define('BASE_URL', "{$protocol}://{$host}{$basePath}");
}

// Incluir o cabeçalho
require_once __DIR__ . '/../include/header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página em Manutenção</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= BASE_URL ?>/app/Assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .manutencao-container {
            min-height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: #f7f7f7;
        }

        .card-manutencao {
            padding: 40px;
            border: none;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            max-width: 600px;
        }
    </style>
</head>
<body>

<div class="manutencao-container">
    <div class="card-manutencao">
        <h1 class="text-warning mb-3">🚧 Página em Manutenção</h1>
        <p class="lead mb-4">
            Essa funcionalidade ainda está sendo desenvolvida.<br>
            Em breve estará disponível para você!
        </p>
        <a href="<?= BASE_URL ?>/perfil" class="btn btn-primary">Voltar ao perfil</a>
    </div>
</div>

<!-- JS Bootstrap opcional -->
<script src="<?= BASE_URL ?>/app/Assets/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Incluir o rodapé
require_once __DIR__ . '/../include/footer.php';
?>
