<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioNome = $_SESSION['usuario']['nome'] ?? 'Administrador';
$ultimoAcesso = $_SESSION['usuario']['ultimo_acesso'] ?? date('d/m/Y H:i');
?>

<section class="container-fluid py-4">
    <!-- 1. Saudação -->
    <div class="mb-4">
        <h2 class="mb-1"><i class="fas fa-file-alt me-2"></i><?= $this->dados['titulo'] ?? 'Páginas Institucionais' ?></h2>
        <p class="text-muted">Bem-vindo(a), <?= htmlspecialchars($usuarioNome) ?> | Último acesso: <?= $ultimoAcesso ?></p>
    </div>

    <!-- 2. KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card text-bg-primary shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-dollar-sign me-1"></i>Vendas Hoje</h6>
                    <h3>€ 1.530</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-bg-success shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-user-plus me-1"></i>Novos Clientes</h6>
                    <h3>5</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-bg-warning shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-box me-1"></i>Pedidos Pendentes</h6>
                    <h3>8</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-bg-danger shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-exclamation-triangle me-1"></i>Estoque Baixo</h6>
                    <h3>3 itens</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Atalhos -->
    <div class="row g-3 mb-4">
        <?php
        $atalhos = [
            ['icon' => 'fa-boxes', 'label' => 'Produtos', 'rota' => 'adminproduto'],
            ['icon' => 'fa-shopping-cart', 'label' => 'Vendas', 'rota' => 'adminvenda'],
            ['icon' => 'fa-warehouse', 'label' => 'Estoque', 'rota' => 'estoque'],
            ['icon' => 'fa-users', 'label' => 'Usuários', 'rota' => 'adminusuario'],
            ['icon' => 'fa-truck', 'label' => 'Fornecedores', 'rota' => 'fornecedor'],
            ['icon' => 'fa-chart-line', 'label' => 'Relatórios', 'rota' => 'relatorios'],
            ['icon' => 'fa-tags', 'label' => 'Promoções', 'rota' => 'promocao'],
            ['icon' => 'fa-file-alt', 'label' => 'Páginas', 'rota' => 'paginas'], <!-- ✅ Corrigido aqui -->
            ['icon' => 'fa-sliders-h', 'label' => 'Configurações', 'rota' => 'configuracoes'],
        ];
        foreach ($atalhos as $atalho):
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <a href="<?= BASE_URL . '/' . $atalho['rota'] ?>" class="text-decoration-none">
                <div class="card text-center shadow-sm h-100">
                    <div class="card-body">
                        <i class="fas <?= $atalho['icon'] ?> fa-2x mb-2 text-secondary"></i>
                        <h6 class="mb-0"><?= $atalho['label'] ?></h6>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- 4. Gráficos (em breve) -->
    <div class="mb-4">
        <h5 class="text-muted"><i class="fas fa-chart-bar me-2"></i>Gráficos de Desempenho (em breve)</h5>
        <div class="alert alert-info">A integração com gráficos interativos será adicionada em breve.</div>
    </div>

    <!-- 5. Pedidos Recentes (simulado) -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Últimos Pedidos</h5>
            <table class="table table-hover align-middle mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#1023</td>
                        <td>João Silva</td>
                        <td>€ 89,90</td>
                        <td><span class="badge bg-warning">Pendente</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>#1022</td>
                        <td>Maria Souza</td>
                        <td>€ 153,75</td>
                        <td><span class="badge bg-success">Entregue</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 6. Estoque e Alertas -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-box-open me-2"></i>Avisos de Estoque</h5>
            <ul class="mb-0">
                <li>Tomate cereja - estoque crítico</li>
                <li>Leite integral - vencimento em 3 dias</li>
                <li>Água com gás - fornecedor atrasado</li>
            </ul>
        </div>
    </div>

    <!-- 7. Relatórios em destaque -->
    <div class="mb-4">
        <h5><i class="fas fa-star me-2"></i>Relatórios em Destaque</h5>
        <ul>
            <li><a href="<?= BASE_URL ?>/relatorios" class="text-decoration-none">Relatório Financeiro</a></li>
            <li><a href="<?= BASE_URL ?>/relatorios" class="text-decoration-none">Produtos Mais Vendidos</a></li>
            <li><a href="<?= BASE_URL ?>/relatorios" class="text-decoration-none">Clientes Ativos</a></li>
        </ul>
    </div>

    <!-- 8. Avisos -->
    <div class="alert alert-warning">
        <strong>Aviso:</strong> Atualização do sistema agendada para amanhã às 03:00.
    </div>

    <!-- 9. Logins recentes -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-user-clock me-2"></i>Últimos acessos</h5>
            <ul class="mb-0">
                <li>Alex Rodrigues - 25/04/2025 09:12</li>
                <li>Admin Master - 24/04/2025 18:27</li>
                <li>Suporte TI - 24/04/2025 12:44</li>
            </ul>
        </div>
    </div>

    <!-- 10. Ferramentas extras -->
    <div class="mb-4">
        <h5><i class="fas fa-tools me-2"></i>Ferramentas do Sistema</h5>
        <a href="#" class="btn btn-outline-secondary btn-sm me-2"><i class="fas fa-database"></i> Backup</a>
        <a href="#" class="btn btn-outline-secondary btn-sm me-2"><i class="fas fa-file-import"></i> Importar CSV</a>
        <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fas fa-plug"></i> Integrações</a>
    </div>
</section>
