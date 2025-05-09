<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if (!isset($_SESSION['usuario']['id'])) {
    $_SESSION['msg_error'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

$assetsPath = BASE_URL . "/app/Assets/";
$fotoPerfil = !empty($perfil['foto']) ? BASE_URL . "/app/Assets/image/perfil/" . $perfil['foto'] : BASE_URL . "/app/Assets/image/perfil/default.png";

$msgSuccess = $_SESSION['msg_success'] ?? '';
$msgError = $_SESSION['msg_error'] ?? '';
unset($_SESSION['msg_success'], $_SESSION['msg_error']);

include_once __DIR__ . '/../include/header.php';
include_once __DIR__ . '/../include/menulateral.php';
?>

<style>
    .perfil-container {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 2rem;
    }
    .perfil-avatar {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #198754;
    }
    .perfil-info h2 {
        font-size: 1.5rem;
        margin-top: 1rem;
        font-weight: 700;
    }
    .perfil-info p {
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    .painel-acoes .card {
        transition: 0.3s;
        border-radius: 12px;
        cursor: pointer;
    }
    .painel-acoes .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
    }
    .painel-acoes i {
        font-size: 1.5rem;
        color: #198754;
    }
</style>

<main class="conteudo-com-menu py-5 bg-light">
    <div class="container">
        <?php if (!empty($msgSuccess) || !empty($msgError)): ?>
            <div class="alert <?= $msgSuccess ? 'alert-success' : 'alert-danger' ?> text-center">
                <?= htmlspecialchars($msgSuccess ?: $msgError) ?>
            </div>
        <?php endif; ?>

        <div class="perfil-container">
            <div class="row g-4">
                <!-- Coluna da esquerda -->
                <div class="col-md-4 text-center border-end">
                    <img src="<?= $fotoPerfil ?>" class="perfil-avatar shadow-sm" alt="Foto de Perfil">
                    <div class="perfil-info mt-3">
                        <h2><?= htmlspecialchars($perfil['nome']) ?></h2>
                        <p><?= htmlspecialchars($perfil['email']) ?></p>
                        <span class="badge bg-success text-uppercase"><?= $_SESSION['usuario_nivel'] ?? 'cliente' ?></span>
                        <div class="mt-3">
                            <a href="<?= BASE_URL ?>/perfil/editar" class="btn btn-outline-primary btn-sm rounded-pill">
                                <i class="fas fa-user-edit me-1"></i> Editar Perfil
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Coluna da direita -->
                <div class="col-md-8">
                    <div class="row g-3 painel-acoes">
                        <div class="col-6 col-md-4">
                            <a href="<?= BASE_URL ?>/manutencao">
                                <div class="card text-center p-3 shadow-sm h-100">
                                    <i class="fas fa-clock mb-2"></i>
                                    <div>Atividades</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-4">
                            <a href="<?= BASE_URL ?>/manutencao">
                                <div class="card text-center p-3 shadow-sm h-100">
                                    <i class="fas fa-heart mb-2"></i>
                                    <div>Favorito</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-4">
                            <a href="<?= BASE_URL ?>/manutencao">
                                <div class="card text-center p-3 shadow-sm h-100">
                                    <i class="fas fa-star mb-2"></i>
                                    <div>Favoritos</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-4">
                            <a href="<?= BASE_URL ?>/carrinho">
                                <div class="card text-center p-3 shadow-sm h-100">
                                    <i class="fas fa-shopping-cart mb-2"></i>
                                    <div>Carrinho</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-4">
                            <a href="<?= BASE_URL ?>/meuspedidos">
                                <div class="card text-center p-3 shadow-sm h-100">
                                    <i class="fas fa-box mb-2"></i>
                                    <div>Meus Pedidos</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-4">
                            <a href="<?= BASE_URL ?>/sair">
                                <div class="card text-center p-3 shadow-sm h-100">
                                    <i class="fas fa-sign-out-alt mb-2"></i>
                                    <div>Sair</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Extras -->
                    <div class="mt-4">
                        <h5 class="text-muted">Dados da Conta</h5>
                        <ul class="list-group list-group-flush border rounded shadow-sm">
                            <li class="list-group-item"><strong>ID:</strong> <?= $_SESSION['usuario']['id'] ?></li>
                            <li class="list-group-item">
                                <strong>Registrado em:</strong>
                                <?php
                         if (!empty($perfil['dt_registro'])) {
                        echo date('d/m/Y', strtotime($perfil['dt_registro']));
                    } else {
            echo '<span class="text-muted">Data não disponível</span>';
        }
    ?>
</li>

                            <li class="list-group-item"><strong>Status:</strong> <span class="badge bg-success">Ativo</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../include/footer.php'; ?>
