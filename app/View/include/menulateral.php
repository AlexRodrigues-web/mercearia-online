<?php
// Verificar se a constante criada no index está definida
if (!defined("MERCEARIA2021")) {
    header("Location: /paginaInvalida/index");
    die("Erro: Página não encontrada!");
}
?>
<nav id="menuLateral" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <?php if (isset($_SESSION['usuario_paginas'])): ?>
                <li class="nav-item active">
                    <a class="nav-link text-center" href="<?= URL ?>perfil">
                        <i class="fas fa-user-circle fa-7x"></i>
                        <p class="my-1"><?= htmlspecialchars($_SESSION['usuario_nome'], ENT_QUOTES, 'UTF-8') ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-center" href="<?= URL ?>sair" id="link-sair">
                        Sair <i class="fas fa-sign-out-alt"></i>
                    </a>
                </li>
                <?php foreach ($_SESSION['usuario_paginas'] as $pagina): ?>
                    <?php if ($pagina !== "Perfil"): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php
                                switch ($pagina) {
                                    case 'Pagina_privada':
                                        echo URL . 'pagina_privada';
                                        break;
                                    case 'Pagina_publica':
                                        echo URL . 'pagina_publica';
                                        break;
                                    case 'Pagina_funcionario':
                                        echo URL . 'pagina_funcionario';
                                        break;
                                    case 'Nivel':
                                        echo URL . 'nivel';
                                        break;
                                    default:
                                        echo URL . strtolower($pagina);
                                        break;
                                }
                            ?>">
                                <?= htmlspecialchars($pagina === 'Pagina_privada' ? 'Páginas Privadas' : 
                                                     ($pagina === 'Pagina_publica' ? 'Páginas Públicas' : 
                                                     ($pagina === 'Pagina_funcionario' ? 'Páginas de Acesso' : 
                                                     ($pagina === 'Nivel' ? 'Nível de Acesso' : $pagina))),
                                                     ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</nav>
