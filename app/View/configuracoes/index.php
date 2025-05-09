<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['logado'] !== true) {
    $_SESSION['msg_erro'] = "Acesso negado! Faça login para continuar.";
    header("Location: " . BASE_URL . "login");
    exit();
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

// Simula carregamento de configurações atuais (substitua por chamadas ao model)
$config = [
    'store_name'       => 'Minha Mercearia',
    'store_nif'        => '123456789',
    'store_address'    => 'Rua Exemplo, 123',
    'language'         => 'pt-BR',
    'timezone'         => 'Europe/Lisbon',
    'currency'         => 'EUR',
    'logo'             => '/app/Assets/images/logo.png',
    'favicon'          => '/app/Assets/images/favicon.ico',
    'footer_text'      => '© 2025 Minha Mercearia',
    'homepage_type'    => 'personalizada',
    'friendly_urls'    => true,
    'maintenance'      => false,
    'ga_id'            => '',
    'cookie_policy'    => true,
    // … complete todas conforme necessidade …
];

$usuarioNome = $_SESSION['usuario']['nome'] ?? 'Administrador';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Configurações | Mercearia Online</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= BASE_URL ?>/app/Assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/app/Assets/css/mercearia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/admin">
        <i class="fas fa-tools me-2"></i> ADM
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/admin" title="Dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/adminproduto" title="Produtos">
                    <i class="fas fa-boxes"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/adminvenda" title="Vendas">
                    <i class="fas fa-shopping-cart"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/adminusuario" title="Usuários">
                    <i class="fas fa-users"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="gestaoDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Gestão">
                    <i class="fas fa-cogs"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="gestaoDropdown">
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/funcionario"><i class="fas fa-id-badge me-2"></i>Funcionários</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/nivel"><i class="fas fa-layer-group me-2"></i>Níveis</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/estoque"><i class="fas fa-warehouse me-2"></i>Estoque</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/fornecedor"><i class="fas fa-truck me-2"></i>Fornecedores</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/promocao"><i class="fas fa-tags me-2"></i>Promoções</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/relatorios"><i class="fas fa-chart-line me-2"></i>Relatórios</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/configuracoes"><i class="fas fa-sliders-h me-2"></i>Configurações</a></li>
                </ul>
            </li>
            <li class="nav-item ms-2">
                <a class="nav-link btn btn-danger text-white px-3" href="<?= BASE_URL ?>/sair" title="Sair">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="text-center mt-4">
        <a href="<?= BASE_URL ?>/admin" class="btn btn-secondary">⬅ Voltar ao Painel Administrativo</a>
    </div>
</div>

<div class="container my-5">
    <h1 class="text-center mb-4"><i class="fas fa-sliders-h me-2"></i>Configurações do Sistema</h1>
    <p class="text-center text-muted mb-5">Olá, <strong><?= htmlspecialchars($usuarioNome) ?></strong>. Altere as configurações abaixo e clique em “Salvar Alterações”.</p>

    <?php if (!empty($_SESSION['msg_erro'])): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['msg_erro']) ?></div>
      <?php unset($_SESSION['msg_erro']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['msg_sucesso'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_SESSION['msg_sucesso']) ?></div>
      <?php unset($_SESSION['msg_sucesso']); ?>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/configuracoes/salvar" method="post" enctype="multipart/form-data">
        <div class="accordion" id="accordionConfig">

            <!-- 1. Configurações Gerais -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingGeral">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGeral">
                        <i class="fas fa-cog me-2"></i>Configurações Gerais
                    </button>
                </h2>
                <div id="collapseGeral" class="accordion-collapse collapse show" data-bs-parent="#accordionConfig">
                    <div class="accordion-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome da loja</label>
                            <input type="text" name="store_name" class="form-control" value="<?= htmlspecialchars($config['store_name']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CNPJ/NIF</label>
                            <input type="text" name="store_nif" class="form-control" value="<?= htmlspecialchars($config['store_nif']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Endereço fiscal</label>
                            <input type="text" name="store_address" class="form-control" value="<?= htmlspecialchars($config['store_address']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Idioma padrão</label>
                            <select name="language" class="form-select">
                                <option value="pt-BR" <?= $config['language']=='pt-BR'?'selected':'' ?>>Português (BR)</option>
                                <option value="en-US" <?= $config['language']=='en-US'?'selected':'' ?>>Inglês (US)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fuso horário</label>
                            <select name="timezone" class="form-select">
                                <option value="Europe/Lisbon" <?= $config['timezone']=='Europe/Lisbon'?'selected':'' ?>>Europe/Lisbon</option>
                                <option value="UTC" <?= $config['timezone']=='UTC'?'selected':'' ?>>UTC</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Moeda</label>
                            <input type="text" name="currency" class="form-control" value="<?= htmlspecialchars($config['currency']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Logo (PNG/JPG)</label>
                            <input type="file" name="logo" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Favicon (ICO)</label>
                            <input type="file" name="favicon" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Texto do rodapé</label>
                            <textarea name="footer_text" class="form-control" rows="2"><?= htmlspecialchars($config['footer_text']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Configurações do Site -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingSite">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSite">
                        <i class="fas fa-globe me-2"></i>Configurações do Site
                    </button>
                </h2>
                <div id="collapseSite" class="accordion-collapse collapse" data-bs-parent="#accordionConfig">
                    <div class="accordion-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Página inicial</label>
                            <select name="homepage_type" class="form-select">
                                <option value="padrão" <?= $config['homepage_type']=='padrão'?'selected':'' ?>>Padrão</option>
                                <option value="personalizada" <?= $config['homepage_type']=='personalizada'?'selected':'' ?>>Personalizada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="friendly_urls" id="friendly_urls" <?= $config['friendly_urls']?'checked':'' ?>>
                                <label class="form-check-label" for="friendly_urls">URLs Amigáveis / SEO</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="maintenance" id="maintenance" <?= $config['maintenance']?'checked':'' ?>>
                                <label class="form-check-label" for="maintenance">Modo de Manutenção</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Google Analytics / Pixel ID</label>
                            <input type="text" name="ga_id" class="form-control" value="<?= htmlspecialchars($config['ga_id']) ?>">
                        </div>
                        <div class="col-12">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="cookie_policy" id="cookie_policy" <?= $config['cookie_policy']?'checked':'' ?>>
                                <label class="form-check-label" for="cookie_policy">Política de Cookies & LGPD</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Pagamentos -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingPagamentos">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePagamentos">
                        <i class="fas fa-credit-card me-2"></i>Métodos de Pagamento
                    </button>
                </h2>
                <div id="collapsePagamentos" class="accordion-collapse collapse" data-bs-parent="#accordionConfig">
                    <div class="accordion-body row g-3">
                        <?php
                        $metodos = ['multibanco','mbway','paypal','cartao','referencia','transferencia'];
                        foreach ($metodos as $metodo): ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="payment_methods[]" id="<?= $metodo ?>" value="<?= $metodo ?>">
                                    <label class="form-check-label" for="<?= $metodo ?>"><?= ucfirst($metodo) ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="col-12">
                            <label class="form-label">Chaves/API (JSON)</label>
                            <textarea name="payment_keys" class="form-control" rows="3"><?= htmlspecialchars($config['payment_keys'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Entregas e Fretes -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFrete">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFrete">
                        <i class="fas fa-truck me-2"></i>Entregas e Fretes
                    </button>
                </h2>
                <div id="collapseFrete" class="accordion-collapse collapse" data-bs-parent="#accordionConfig">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipos de entrega (separados por vírgula)</label>
                                <input type="text" name="delivery_types" class="form-control" value="<?= htmlspecialchars($config['delivery_types'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Frete grátis acima de (€)</label>
                                <input type="number" name="free_shipping_threshold" class="form-control" value="<?= htmlspecialchars($config['free_shipping_threshold'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tabela de preços por CEP/distância (JSON)</label>
                                <textarea name="shipping_table" class="form-control" rows="3"><?= htmlspecialchars($config['shipping_table'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Carrinho & Checkout -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingCart">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCart">
                        <i class="fas fa-shopping-cart me-2"></i>Carrinho & Checkout
                    </button>
                </h2>
                <div id="collapseCart" class="accordion-collapse collapse" data-bs-parent="#accordionConfig">
                    <div class="accordion-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Expiração do carrinho (minutos)</label>
                            <input type="number" name="cart_expiry" class="form-control" value="<?= htmlspecialchars($config['cart_expiry'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="require_login_checkout" id="require_login" <?= !empty($config['require_login_checkout'])?'checked':'' ?>>
                                <label class="form-check-label" for="require_login">Exigir login no checkout</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Campos obrigatórios no checkout (JSON)</label>
                            <textarea name="checkout_fields" class="form-control" rows="3"><?= htmlspecialchars($config['checkout_fields'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. Estoque -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingStock">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStock">
                        <i class="fas fa-boxes me-2"></i>Estoque
                    </button>
                </h2>
                <div id="collapseStock" class="accordion-collapse collapse" data-bs-parent="#accordionConfig">
                    <div class="accordion-body row g-3">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="stock_control" id="stock_control" <?= !empty($config['stock_control'])?'checked':'' ?>>
                                <label class="form-check-label" for="stock_control">Controle automático de estoque</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notify_low_stock" id="notify_low_stock" <?= !empty($config['notify_low_stock'])?'checked':'' ?>>
                                <label class="form-check-label" for="notify_low_stock">Notificar estoque mínimo</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">  
                                <input class="form-check-input" type="checkbox" name="hide_out_of_stock" id="hide_out_of_stock" <?= !empty($config['hide_out_of_stock'])?'checked':'' ?>>
                                <label class="form-check-label" for="hide_out_of_stock">Ocultar produtos esgotados</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. E-mails & Notificações -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingEmail2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEmail2">
                        <i class="fas fa-envelope me-2"></i>E-mails & Notificações
                    </button>
                </h2>
                <div id="collapseEmail2" class="accordion-collapse collapse" data-bs-parent="#accordionConfig">
                    <div class="accordion-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Remetente de e-mails</label>
                            <input type="email" name="mail_from" class="form-control" value="<?= htmlspecialchars($config['mail_from'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Servidor SMTP</label>
                            <input type="text" name="smtp_server" class="form-control" value="<?= htmlspecialchars($config['smtp_server'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Porta SMTP</label>
                            <input type="number" name="smtp_port" class="form-control" value="<?= htmlspecialchars($config['smtp_port'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notificações de alerta</label>
                            <textarea name="alert_notifications" class="form-control" rows="3"><?= htmlspecialchars($config['alert_notifications'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 8. Segurança & Acesso -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingSec">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSec">
                        <i class="fas fa-shield-alt me-2"></i>Segurança & Acesso
                    </button>
                </h2>
                <div id="collapseSec" class="accordion-collapse collapse" data-bs-parent="#accordionConfig">
                    <div class="accordion-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Senha mínima (chars)</label>
                            <input type="number" name="min_password_length" class="form-control" value="<?= htmlspecialchars($config['min_password_length'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="two_factor" id="two_factor" <?= !empty($config['two_factor'])?'checked':'' ?>>
                                <label class="form-check-label" for="two_factor">Autenticação em 2 etapas</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="auto_logout" id="auto_logout" <?= !empty($config['auto_logout'])?'checked':'' ?>>
                                <label class="form-check-label" for="auto_logout">Logout automático</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 9. Aparência & Tema -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTheme">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTheme">
                        <i class="fas fa-paint-brush me-2"></i>Aparência & Tema
                    </button>
                </h2>
                <div id="collapseTheme" class="accordion-collapse collapse" data-bs-parent="#accordionConfig">
                    <div class="accordion-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tema padrão</label>
                            <select name="default_theme" class="form-select">
                                <option value="light">Claro</option>
                                <option value="dark">Escuro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cor primária (hex)</label>
                            <input type="text" name="primary_color" class="form-control" value="<?= htmlspecialchars($config['primary_color'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Fonte do sistema</label>
                            <input type="text" name="font_family" class="form-control" value="<?= htmlspecialchars($config['font_family'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 10. Integrações -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingInt">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInt">
                        <i class="fas fa-plug me-2"></i>Integrações
                    </button>
                </h2>
                <div id="collapseInt" class="accordion-collapse collapse" data-bs-parent="#accordionConfig">
                    <div class="accordion-body row g-3">
                        <div class="col-12">
                            <label class="form-label">APIs ativas (JSON)</label>
                            <textarea name="active_apis" class="form-control" rows="3"><?= htmlspecialchars($config['active_apis'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 11. Backup & Restauração -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingBackup">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBackup">
                        <i class="fas fa-database me-2"></i>Backup & Restauração
                    </button>
                </h2>
                <div id="collapseBackup" class="accordion-collapse collapse" data-bs-parent="#accordionConfig">
                    <div class="accordion-body row g-3">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-primary w-100" onclick="location.href='<?= BASE_URL ?>/configuracoes/backup'">
                                <i class="fas fa-download me-2"></i> Criar/Download Backup
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-warning w-100" onclick="location.href='<?= BASE_URL ?>/configuracoes/restore'">
                                <i class="fas fa-upload me-2"></i> Restaurar Configurações
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 12. Conta & Preferências -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingAccount">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAccount">
                        <i class="fas fa-user-cog me-2"></i>Conta & Preferências
                    </button>
                </h2>
                <div id="collapseAccount" class="accordion-collapse collapse" data-bs-parent="#accordionConfig">
                    <div class="accordion-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Alterar senha</label>
                            <input type="password" name="new_password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Foto do administrador</label>
                            <input type="file" name="admin_photo" class="form-control">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="personal_notifications" id="personal_notifications">
                                <label class="form-check-label" for="personal_notifications">Notificações pessoais</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-2"></i>Salvar Alterações</button>
        </div>
    </form>
</div>

<script src="<?= BASE_URL ?>/app/Assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
