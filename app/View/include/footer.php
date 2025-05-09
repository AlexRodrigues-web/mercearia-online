<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', rtrim("{$protocol}://{$host}{$basePath}", '/') . '/');
}

if (!defined('ASSETS_URL')) {
    define('ASSETS_URL', BASE_URL . 'app/Assets');
}
?>

<footer class="text-white pt-5" style="background: linear-gradient(90deg,rgb(0, 0, 0),rgb(69, 69, 69),rgb(115, 114, 114));">
    <div class="container">
        <div class="row">

            <!-- Loja -->
            <div class="col-md-3 mb-4">
                <h5 class="fw-bold">🏪 Mercearia </h5>
                <p>Rua da Bélgica, 2450<br>Canidelo, Vila Nova de Gaia</p>
                <p>CNPJ/NIF: 00.000.000/0001-00</p>
                <p>📞 (+351) 0000 00 000</p>
                <p>📧 suporte@mercearia.com</p>
            </div>

            <!-- Links -->
            <div class="col-md-3 mb-4">
                <h5 class="fw-bold">🔗 Links úteis</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= BASE_URL ?>" class="text-white text-decoration-none">Início</a></li>
                    <li><a href="<?= BASE_URL ?>produtos" class="text-white text-decoration-none">Produtos</a></li>
                    <li><a href="<?= BASE_URL ?>promocoes" class="text-white text-decoration-none">Promoções</a></li>
                    <li><a href="<?= BASE_URL ?>sobre" class="text-white text-decoration-none">Sobre nós</a></li>
                    <li><a href="<?= BASE_URL ?>contato" class="text-white text-decoration-none">Contato</a></li>
                    <li><a href="<?= BASE_URL ?>institucional#privacidade" class="text-white text-decoration-none">Política de Privacidade</a></li>
                    <li><a href="<?= BASE_URL ?>institucional#termos" class="text-white text-decoration-none">Termos de Uso</a></li>
                    <li><a href="<?= BASE_URL ?>institucional#entrega" class="text-white text-decoration-none">Política de Entrega</a></li>
                </ul>
            </div>

            <!-- Redes sociais -->
            <div class="col-md-3 mb-4">
                <h5 class="fw-bold">📱 Redes Sociais</h5>
                <a href="https://facebook.com" target="_blank" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                <a href="https://instagram.com" target="_blank" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                <a href="https://wa.me/351000000000" target="_blank" class="text-white me-3"><i class="fab fa-whatsapp fa-lg"></i></a>
                <a href="https://tiktok.com" target="_blank" class="text-white me-3"><i class="fab fa-tiktok fa-lg"></i></a>
            </div>

            <!-- Newsletter -->
            <div class="col-md-3 mb-4">
                <h5 class="fw-bold">🧾 Receba novidades</h5>
                <form action="#" method="POST">
                    <div class="input-group">
                        <input type="email" name="email_news" class="form-control" placeholder="Seu e-mail" required>
                        <button class="btn btn-warning" type="submit">OK</button>
                    </div>
                </form>

                <div class="mt-4">
                    <h6 class="fw-bold mb-2">💳 Pagamentos</h6>
                    <i class="fab fa-cc-visa fa-lg me-2"></i>
                    <i class="fab fa-cc-mastercard fa-lg me-2"></i>
                    <i class="fab fa-cc-paypal fa-lg me-2"></i>
                    <img src="<?= ASSETS_URL ?>/image/icone/mbway.png" alt="MB Way" height="22">
                </div>
            </div>
        </div>

        <hr class="border-light">

        <div class="text-center small">
            © <?= date('Y') ?> Mercearia Online – Todos os direitos reservados. <br>
            Desenvolvido por <strong>Alex Rodrigues</strong>
        </div>
    </div>
</footer>
