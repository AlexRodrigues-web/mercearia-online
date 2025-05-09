<?php // Arquivo PHP para suportar uso de BASE_URL no HTML ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - Mercearia</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/css/mercearia.css">
</head>
<body>

    <!-- Cabeçalho -->
    <?php
    $headerPath = __DIR__ . "/../include/header.php";
    if (file_exists($headerPath)) {
        include_once $headerPath;
    }
    ?>

    <div class="container my-5">

        <!-- Título -->
        <div class="text-center mb-5">
            <h1 class="text-primary fw-bold">Sobre a Mercearia</h1>
            <p class="lead text-muted">União de sabores, culturas e tradição entre Brasil e Portugal 🇧🇷🇵🇹</p>
        </div>

        <!-- Missão, Visão, Valores -->
        <div class="row text-center mb-5">
            <div class="col-md-4 mb-3">
                <div class="p-4 border rounded shadow-sm">
                    <i class="fas fa-bullseye fa-2x text-primary mb-2"></i>
                    <h5 class="fw-bold">Missão</h5>
                    <p>Levar produtos de qualidade à mesa das famílias, com respeito, preço justo e carinho no atendimento.</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-4 border rounded shadow-sm">
                    <i class="fas fa-eye fa-2x text-success mb-2"></i>
                    <h5 class="fw-bold">Visão</h5>
                    <p>Ser referência na integração Brasil-Portugal, oferecendo uma mercearia moderna com alma tradicional.</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-4 border rounded shadow-sm">
                    <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                    <h5 class="fw-bold">Valores</h5>
                    <p>Ética, confiança, proximidade com o cliente, respeito à cultura local e aos nossos fornecedores.</p>
                </div>
            </div>
        </div>

        <!-- História -->
        <div class="mb-5">
            <h3 class="text-center text-secondary mb-4"><i class="fas fa-history"></i> Nossa História</h3>
            <p class="text-justify">
                Fundada em 2024, a Mercearia nasceu da união de um casal luso-brasileiro apaixonado por gastronomia e pela vida em comunidade.
                Começamos com uma pequena loja em Vila Nova de Gaia, unindo sabores do Brasil e Portugal. A resposta da vizinhança foi tão calorosa
                que rapidamente expandimos a variedade de produtos e serviços. Hoje, continuamos crescendo sem perder a essência de proximidade e atendimento familiar.
            </p>
        </div>

        <!-- Equipe -->
        <div class="mb-5 text-center">
            <h3 class="text-secondary mb-4"><i class="fas fa-users"></i> Nossa Equipe</h3>
            <p>Somos uma equipa multicultural e apaixonada por servir. Nossos colaboradores são o coração da mercearia — sempre com um sorriso e dispostos a ajudar.</p>
        </div>

        <!-- Compromisso -->
        <div class="mb-5">
            <h3 class="text-center text-secondary mb-4"><i class="fas fa-handshake"></i> Compromisso com o Cliente</h3>
            <p class="text-justify">
                Prezamos por um atendimento humano e justo. Trabalhamos com fornecedores selecionados, respeitamos a sazonalidade dos produtos e buscamos minimizar desperdícios. 
                Também apoiamos causas sociais e o comércio justo, fortalecendo os laços com a comunidade onde estamos inseridos.
            </p>
        </div>

        <!-- Diferenciais -->
        <div class="mb-5">
            <h3 class="text-center text-secondary mb-4"><i class="fas fa-star"></i> Nossos Diferenciais</h3>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Produtos frescos e selecionados diariamente</li>
                <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Integração de sabores típicos brasileiros e portugueses</li>
                <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Atendimento acolhedor e personalizado</li>
                <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Promoções semanais imperdíveis</li>
                <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Compra online com retirada rápida em loja</li>
            </ul>
        </div>

        <!-- Chamada para Ação -->
        <div class="text-center mt-5">
            <h4 class="mb-3 text-primary"><i class="fas fa-shopping-cart"></i> Pronto para conhecer nossos produtos?</h4>
            <a href="<?= BASE_URL ?>produtos" class="btn btn-outline-primary btn-lg mx-2"><i class="fas fa-store"></i> Ver Produtos</a>
            <a href="<?= BASE_URL ?>contato" class="btn btn-primary btn-lg mx-2"><i class="fas fa-envelope"></i> Entrar em Contato</a>
        </div>
    </div>

    <!-- Rodapé -->
    <?php
    $footerPath = __DIR__ . "/../include/footer.php";
    if (file_exists($footerPath)) {
        include_once $footerPath;
    } else {
        echo "<footer class='text-center text-muted my-4'>© " . date('Y') . " Mercearia - Todos os direitos reservados.</footer>";
    }
    ?>

    <script src="<?= BASE_URL ?>app/Assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
