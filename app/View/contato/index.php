<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Mercearia</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-primary">Entre em Contato</h1>
        <p class="lead">Se você tiver dúvidas, sugestões ou precisar de suporte, entre em contato conosco.</p>

        <form method="POST" action="<?= BASE_URL ?>contato/enviar">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="mensagem" class="form-label">Mensagem</label>
                <textarea class="form-control" id="mensagem" name="mensagem" rows="5" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
        </form>

        <a href="<?= BASE_URL ?>" class="btn btn-secondary mt-3">Voltar à Página Inicial</a>
    </div>
</body>
</html>
