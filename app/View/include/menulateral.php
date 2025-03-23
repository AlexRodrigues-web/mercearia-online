<?php  
// ============================
// 🔒 Segurança e Definições
// ============================
if (!defined("MERCEARIA2025")) {
    error_log("Acesso não autorizado ao menu lateral.");
    die("Erro: Acesso negado. Contate o administrador.");
}

// ============================
// 🌍 Garantir que BASE_URL está Definida
// ============================
if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}

// ============================
// 🔗 Função para Construção de Links com Validação
// ============================
function construirLinkMenu($pagina)
{
    $rotasValidas = [
        'home' => 'home',
        'produtos' => 'produtos',
        'sobre' => 'sobre',
        'contato' => 'contato',
        'funcionarios' => 'funcionarios',
        'fornecedores' => 'fornecedores',
        'admin' => 'admin',
        'estoque' => 'estoque',
        'vendas' => 'vendas',
        'caixa' => 'caixa',
        'ajuda' => 'ajuda', // ✅ Adicionado link para Ajuda
    ];

    return isset($rotasValidas[$pagina]) ? BASE_URL . $rotasValidas[$pagina] : false;
}

// ============================
// 🔑 Capturar Dados do Usuário
// ============================
$usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';
$usuarioNivel = $_SESSION['usuario_nivel'] ?? 'comum';
$paginasUsuario = $_SESSION['usuario_paginas'] ?? [];
$menuRestrito = true; // 🔹 Defina como `false` se quiser exibir para todos

// ✅ Se o usuário não está autenticado e o menu for restrito, oculta o menu lateral
if (!isset($_SESSION['usuario_id']) && $menuRestrito === true) {
    return;
}
?>

<nav id="menuLateral" class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-light border-end shadow-sm">
    <div class="sidebar-sticky p-3">
        <ul class="nav flex-column">
            <!-- ✅ Exibir informações do usuário -->
            <li class="nav-item text-center mb-3">
                <a class="nav-link" href="<?= BASE_URL ?>perfil">
                    <i class="fas fa-user-circle fa-5x text-primary"></i>
                    <p class="my-2"><?= htmlspecialchars($usuarioNome, ENT_QUOTES, 'UTF-8') ?></p>
                </a>
            </li>

            <!-- ✅ Link de Sair -->
            <li class="nav-item mb-2">
                <a class="nav-link text-danger text-center" href="<?= BASE_URL ?>sair" id="link-sair">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </li>

            <!-- ✅ Exibir menu apenas se houver permissões -->
            <?php if (!empty($paginasUsuario)): ?>
                <?php foreach ($paginasUsuario as $pagina): ?>
                    <?php 
                        $link = construirLinkMenu(strtolower($pagina));
                        if ($link): 
                    ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $link ?>">
                                <i class="fas fa-folder"></i>
                                <?= htmlspecialchars(ucfirst($pagina), ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="nav-item text-center text-muted">
                    Nenhuma página disponível. Verifique suas permissões com o administrador.
                </li>
            <?php endif; ?>

            <!-- ✅ Adicionado Link para a Ajuda -->
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>ajuda">
                    <i class="fas fa-question-circle"></i> Ajuda
                </a>
            </li>

            <!-- ✅ Adicionado Link para Configurações (Somente para Administradores) -->
            <?php if ($usuarioNivel === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>configuracoes">
                        <i class="fas fa-cogs"></i> Configurações
                    </a>
                </li>
            <?php endif; ?>
            
        </ul>
    </div>
</nav>
