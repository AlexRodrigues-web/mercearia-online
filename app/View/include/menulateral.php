<?php
if (!defined("MERCEARIA2025")) {
    error_log("Acesso não autorizado ao menu lateral.");
    die("Erro: Acesso negado.");
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}

$usuario = $_SESSION['usuario'] ?? [];
$usuarioNome = $usuario['nome'] ?? 'Usuário';
$usuarioNivel = strtolower($usuario['nivel_nome'] ?? 'comum'); // Corrigido para lowercase
$usuarioFoto = $usuario['foto'] ?? 'default.png';

error_log("🖼️ [MenuLateral] Foto da sessão carregada: " . $usuarioFoto);

if ($usuarioNivel === 'admin') {
    error_log("✅ [MenuLateral] Botão ADM visível para usuário: {$usuarioNome}");
} else {
    error_log("⛔ [MenuLateral] Usuário sem permissão de admin tentou acessar botão ADM: {$usuarioNivel}");
}
?>

<style>
   #menuLateral {
    height: 100vh;               /* altura fixa da tela */
    overflow-y: auto;            /* permite scroll vertical */
    overflow-x: hidden;          /* previne scroll horizontal */
    font-size: 0.95rem;
    background: linear-gradient(90deg,rgb(219, 219, 219),rgb(170, 170, 170),rgb(115, 114, 114));
    border-right: 1px solid #e0e0e0;
    position: fixed;
    top: 0;
    left: 0;
    width: 230px;
    z-index: 1030;
    transition: all 0.3s ease-in-out;
    box-shadow: 2px 0 6px rgba(0, 0, 0, 0.04);
}


    body.menu-ativo {
        padding-left: 230px;
    }

    #menuLateral .nav-link {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.65rem 1rem;
        color: #333;
        transition: background 0.2s, color 0.2s;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-radius: 4px;
        margin: 2px 8px;
    }

    #menuLateral .nav-link:hover,
    #menuLateral .nav-link.active {
        background-color:rgb(247, 247, 247);
        color: #0077cc;
        font-weight: 600;
    }

    #menuLateral .section-title {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #888;
        padding: 0.75rem 1rem 0.3rem;
        margin-top: 1rem;
        border-top: 1px solid #ddd;
    }

    #menuLateral .user-box {
        text-align: center;
        padding: 1.2rem 0 1rem;
        background-color:rgb(243, 237, 237);
        border-bottom: 1px solid rgb(251, 243, 243);
    }

    #menuLateral .user-box img {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #ccc;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    #menuLateral .user-box p {
        margin: 0.5rem 0 0;
        font-weight: 500;
        font-size: 1.05rem;
        color: #333;
    }

    @media (max-width: 768px) {
        #menuLateral {
            position: absolute;
            width: 80%;
            left: -100%;
        }

        body.menu-ativo #menuLateral {
            left: 0;
        }

        body.menu-ativo {
            padding-left: 0;
        }
    }

    .botao-adm-final {
        margin: 1.5rem 1rem 1.8rem;
    }

    .botao-adm-final a {
        display: block;
        width: 100%;
        font-weight: bold;
        background-color: #343a40;
        color: #fff !important;
        padding: 10px;
        text-align: center;
        border-radius: 4px;
        transition: background-color 0.3s;
        text-decoration: none;
    }

    .botao-adm-final a:hover {
        background-color: #0077cc;
    }
</style>

<nav id="menuLateral">
    <div class="sidebar-sticky">
        <div class="user-box">
            <img src="<?= BASE_URL ?>app/Assets/image/perfil/<?= htmlspecialchars($usuarioFoto) ?>?v=<?= time() ?>" alt="Foto de Perfil">
            <p><?= htmlspecialchars($usuarioNome) ?></p>
        </div>

        <ul class="nav flex-column px-2">
            <div class="section-title">Minha Conta</div>
            <li><a class="nav-link" href="<?= BASE_URL ?>perfil"><i class="fas fa-id-card"></i> Perfil</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>perfil/editar"><i class="fas fa-user-edit"></i> Editar Conta</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-history"></i> Atividade</a></li>

            <div class="section-title">Compras</div>
            <li><a class="nav-link" href="<?= BASE_URL ?>meuspedidos"><i class="fas fa-box"></i> Meus Pedidos</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-heart"></i> Favoritos</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>carrinho"><i class="fas fa-shopping-cart"></i> Carrinho</a></li>

            <div class="section-title">Navegação</div>
            <li><a class="nav-link" href="<?= BASE_URL ?>produtos"><i class="fas fa-tags"></i> Produtos</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>promocoes"><i class="fas fa-percent"></i> Promoções</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-list"></i> Categorias</a></li>

            <div class="section-title">Personalização</div>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-receipt"></i> Histórico de Compras</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-magic"></i> Recomendados</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-list-alt"></i> Minhas Listas</a></li>

            <div class="section-title">Suporte</div>
            <li><a class="nav-link" href="<?= BASE_URL ?>ajuda"><i class="fas fa-question-circle"></i> Ajuda</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-star"></i> Avaliações</a></li>

            <div class="section-title">Benefícios</div>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-gift"></i> Fidelidade</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-ticket-alt"></i> Meus Cupons</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-calendar-check"></i> Eventos</a></li>

            <div class="section-title">Preferências</div>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-adjust"></i> Tema/Exibição</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-bell"></i> Notificações</a></li>

            <div class="section-title">Mais</div>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-search"></i> Pesquisas</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>manutencao"><i class="fas fa-share-alt"></i> Compartilhar</a></li>

            <?php if (in_array($usuarioNivel, ['admin', 'funcionario'])): ?>
                <div class="section-title">Administração</div>
                <li><a class="nav-link" href="<?= BASE_URL ?>admin"><i class="fas fa-user-shield"></i> Painel Admin</a></li>
                <li><a class="nav-link" href="<?= BASE_URL ?>estoque"><i class="fas fa-warehouse"></i> Estoque</a></li>
                <li><a class="nav-link" href="<?= BASE_URL ?>fornecedor"><i class="fas fa-truck"></i> Fornecedores</a></li>
                <li><a class="nav-link" href="<?= BASE_URL ?>funcionario"><i class="fas fa-id-badge"></i> Funcionários</a></li>
                <?php if ($usuarioNivel === 'admin'): ?>
                    <li><a class="nav-link" href="<?= BASE_URL ?>nivel"><i class="fas fa-layer-group"></i> Níveis de Acesso</a></li>
                    <li><a class="nav-link" href="<?= BASE_URL ?>paginaPrivada"><i class="fas fa-lock"></i> Página Privada</a></li>
                    <li><a class="nav-link" href="<?= BASE_URL ?>configuracoes"><i class="fas fa-cogs"></i> Configurações</a></li>
                <?php endif; ?>
            <?php endif; ?>

            <div class="section-title">Sessão</div>
            <li><a class="nav-link text-danger" href="<?= BASE_URL ?>sair"><i class="fas fa-sign-out-alt"></i> Sair</a></li>

            <?php if ($usuarioNivel === 'admin'): ?>
                <li class="botao-adm-final">
                    <a href="<?= BASE_URL ?>admin" title="Acessar Painel Administrativo">
                        <i class="fas fa-tools me-2"></i> Painel ADM
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<script>
    window.addEventListener('DOMContentLoaded', function () {
        if (!document.body.classList.contains('menu-ativo')) {
            document.body.classList.add('menu-ativo');
        }
    });
</script>
