<?php // Arquivo PHP para suporte a BASE_URL no HTML ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Mercearia</title>

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/css/mercearia.css">
    <link rel="shortcut icon" href="<?= BASE_URL ?>app/Assets/image/logo/favicon.ico">
</head>
<body>

    <!-- Cabeçalho -->
    <?php
    $headerPath = __DIR__ . "/../include/header.php";
    if (file_exists($headerPath)) {
        include_once $headerPath;
    }
    ?>

    <div class="container-fluid">
        <div class="row">

            <!-- Menu lateral (exibe apenas se estiver logado) -->
            <?php if (!empty($_SESSION['usuario']['logado']) && $_SESSION['usuario']['logado'] === true): ?>
                <div class="col-md-3 col-lg-2 bg-light p-3">
                    <?php
                    $menuPath = __DIR__ . "/../include/menulateral.php";
                    if (file_exists($menuPath)) {
                        include_once $menuPath;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Conteúdo principal -->
            <div class="<?= (!empty($_SESSION['usuario']['logado']) && $_SESSION['usuario']['logado'] === true) ? 'col-md-9 col-lg-10' : 'col-12' ?> d-flex justify-content-center align-items-start p-4">

                <div class="card shadow w-100" style="max-width: 900px;">
                    <div class="card-body">
                        <h2 class="text-center text-primary mb-4"><i class="fas fa-envelope-open-text"></i> Fale Conosco</h2>

                        <!-- ✅ ALERTA DE MENSAGEM -->
                        <?php if (!empty($_SESSION['msg'])): ?>
                            <div class="alert alert-info alert-dismissible fade show text-center fw-semibold" role="alert">
                                <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Mensagem de acolhimento -->
                        <div class="bg-light p-4 mb-5 rounded border">
                            <h5 class="text-primary"><i class="fas fa-hand-holding-heart"></i> Sobre o nosso atendimento</h5>
                            <p class="mb-0">
                                Na Mercearia Exemplo, valorizamos cada cliente como parte da nossa família. Estamos sempre prontos para ajudar, seja com dúvidas sobre produtos, sugestões ou pedidos especiais. Fale conosco e teremos o maior prazer em atendê-lo!
                            </p>
                        </div>

                        <!-- Formulário -->
                        <form method="POST" action="<?= BASE_URL ?>contato/enviar" class="mt-2 needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="nome" name="nome" required>
                                </div>
                                <div class="invalid-feedback">Por favor, insira seu nome.</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="invalid-feedback">Insira um e-mail válido.</div>
                            </div>

                            <div class="mb-3">
                                <label for="mensagem" class="form-label">Mensagem</label>
                                <textarea class="form-control" id="mensagem" name="mensagem" rows="5" placeholder="Digite sua mensagem aqui..." required></textarea>
                                <div class="invalid-feedback">Por favor, escreva sua mensagem.</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Enviar
                                </button>
                                <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary">Voltar</a>
                            </div>
                        </form>

                        <!-- Separador -->
                        <hr class="my-5" style="border-top: 4px double #ccc;">

                        <!-- FAQ / Dúvidas frequentes -->
                        <section class="mb-5">
                            <h4 class="text-secondary mb-3 text-center"><i class="fas fa-question-circle"></i> Dúvidas frequentes</h4>
                            <div class="accordion" id="faqAccordion">
                                <?= '... (copie daqui suas perguntas conforme já está)' ?>
                            </div>
                        </section>

                        <!-- Pedido e parcerias -->
                        <section class="mb-5">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="p-4 bg-light border rounded shadow-sm h-100">
                                        <h5 class="text-primary"><i class="fas fa-shopping-basket"></i> Quer fazer um pedido?</h5>
                                        <p>Entre em contato pelo WhatsApp ou envie seu pedido pelo nosso formulário. Em breve também teremos nosso catálogo online!</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="p-4 bg-light border rounded shadow-sm h-100">
                                        <h5 class="text-success"><i class="fas fa-handshake"></i> Parcerias e fornecedores</h5>
                                        <p>Se você é fornecedor ou deseja propor uma parceria, entre em contato por e-mail com o assunto <strong>"Parceria Comercial"</strong>.</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Localização -->
                        <section class="mt-5">
                            <h4 class="text-center text-secondary mb-3"><i class="fas fa-map-marked-alt"></i> Nossa Localização</h4>
                            <div class="ratio ratio-16x9 rounded shadow">
                                <iframe 
                                    src="https://www.google.com/maps?q=Rua+da+Bélgica,+2450,+Canidelo,+Vila+Nova+de+Gaia&output=embed"
                                    allowfullscreen
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    class="rounded">
                                </iframe>
                            </div>
                        </section>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <?php
    $footerPath = __DIR__ . "/../include/footer.php";
    if (file_exists($footerPath)) {
        include_once $footerPath;
    } else {
        echo "<p class='text-center text-muted mt-4'>© " . date('Y') . " Mercearia - Todos os direitos reservados.</p>";
    }
    ?>

    <!-- Scripts -->
    <script src="<?= BASE_URL ?>app/Assets/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
