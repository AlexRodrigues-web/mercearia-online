<?php
// Caminhos corretos para os arquivos necessários
$fontAwesomeJsPath = '/app/Assets/fontawesome/js/all.min.js';
$customJsPath = '/app/Assets/js/eventos.js';

// Garantir que os arquivos existem antes de incluí-los
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $fontAwesomeJsPath) || 
    !file_exists($_SERVER['DOCUMENT_ROOT'] . $customJsPath)) {
    die("Erro: Recursos necessários não encontrados. Contate o administrador do sistema.");
}
?>
<footer class="footer mt-auto py-3 bg-light">
    <div class="container text-center">
        <span class="text-muted">&copy; <?= date('Y') ?> Mercearia. Todos os direitos reservados.</span>
    </div>
</footer>

<!-- Scripts -->
<script src="<?= $fontAwesomeJsPath ?>" defer></script>
<script src="<?= $customJsPath ?>" defer></script>
</body>
</html>
