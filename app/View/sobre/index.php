<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - Mercearia</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-primary">Sobre a Mercearia</h1>
        <p class="lead">Bem-vindo à nossa mercearia! Aqui você encontra os melhores produtos com os melhores preços.</p>
        <p>Fundada em 2024, nossa empresa tem como missão oferecer qualidade e confiança para nossos clientes.</p>

        <a href="<?= BASE_URL ?>" class="btn btn-primary mt-3">Voltar à Página Inicial</a>
    </div>
</body>
</html>
