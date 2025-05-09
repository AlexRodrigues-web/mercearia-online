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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatórios | Mercearia Online</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/app/Assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/app/Assets/css/mercearia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
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
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin"><i class="fas fa-tachometer-alt"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminproduto"><i class="fas fa-boxes"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminvenda"><i class="fas fa-shopping-cart"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminusuario"><i class="fas fa-users"></i></a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="gestaoDropdown" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cogs"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
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
                <a class="nav-link btn btn-danger text-white px-3" href="<?= BASE_URL ?>/sair"><i class="fas fa-sign-out-alt"></i></a>
            </li>
        </ul>
    </div>
</nav>

<section class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2><i class="fas fa-chart-line me-2"></i>Relatórios e Análises</h2>
            <p class="text-muted">Painel de indicadores, desempenho e exportações</p>
        </div>
        <div class="text-end text-muted"><?= date('d/m/Y H:i') ?> - Atualizado</div>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin">Painel ADM</a></li>
            <li class="breadcrumb-item active" aria-current="page">Relatórios</li>
        </ol>
    </nav>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card border-start border-primary border-4 shadow-sm"><div class="card-body"><h6>Total Vendas (Hoje)</h6><h4>€ 1.250</h4></div></div></div>
        <div class="col-md-3"><div class="card border-start border-success border-4 shadow-sm"><div class="card-body"><h6>Clientes Ativos</h6><h4>72</h4></div></div></div>
        <div class="col-md-3"><div class="card border-start border-warning border-4 shadow-sm"><div class="card-body"><h6>Pedidos Finalizados</h6><h4>386</h4></div></div></div>
        <div class="col-md-3"><div class="card border-start border-danger border-4 shadow-sm"><div class="card-body"><h6>Faturamento Líquido</h6><h4>€ 18.420</h4></div></div></div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm"><div class="card-body"><h5 class="mb-3">📊 Vendas por Período</h5><canvas id="graficoBarras" height="200"></canvas></div></div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm"><div class="card-body"><h5 class="mb-3">📈 Participação por Categoria</h5><canvas id="graficoPizza" height="200"></canvas></div></div>
        </div>
    </div>

    <!-- Filtros Avançados com comportamento simulado -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filtros Avançados</h5>
            <form class="row g-3" id="filtroRelatorioForm">
                <div class="col-md-3"><label class="form-label">Período</label><input type="date" class="form-control" name="data_de"></div>
                <div class="col-md-3"><label class="form-label">Até</label><input type="date" class="form-control" name="data_ate"></div>
                <div class="col-md-3"><label class="form-label">Categoria</label><select class="form-select" name="categoria"><option value="todas">Todas</option><option>Hortifruti</option><option>Padaria</option></select></div>
                <div class="col-md-3"><label class="form-label">Produto</label><input type="text" class="form-control" name="produto" placeholder="Nome ou código"></div>
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5><i class="fas fa-receipt me-2"></i>Relatório de Vendas</h5>
                    <p class="text-muted small">Por período, produto ou cliente</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Vendas confirmadas: <strong>328</strong></li>
                        <li class="list-group-item">Cancelamentos: <strong>12</strong></li>
                        <li class="list-group-item">Cupons usados: <strong>27</strong></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5><i class="fas fa-warehouse me-2"></i>Relatório de Estoque</h5>
                    <p class="text-muted small">Histórico e status atual</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Produtos com estoque crítico: <strong>9</strong></li>
                        <li class="list-group-item">Entradas no mês: <strong>63</strong></li>
                        <li class="list-group-item">Saídas no mês: <strong>59</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-5">
        <div class="card-body">
            <h5 class="mb-3"><i class="fas fa-tools me-2"></i>Ferramentas e Exportações</h5>
            <div class="d-flex flex-wrap gap-2">
                <button onclick="exportarCSV()" class="btn btn-outline-secondary"><i class="fas fa-file-csv"></i> CSV</button>
                <button onclick="window.print()" class="btn btn-outline-secondary"><i class="fas fa-file-pdf"></i> PDF</button>
                <button onclick="alert('Exportação para Excel será implementada.');" class="btn btn-outline-secondary"><i class="fas fa-file-excel"></i> Excel</button>
                <button onclick="alert('Funcionalidade de envio por e-mail em desenvolvimento.');" class="btn btn-outline-primary"><i class="fas fa-envelope"></i> Enviar por E-mail</button>
                <button onclick="alert('Agendamento de relatório será implementado.');" class="btn btn-outline-dark"><i class="fas fa-clock"></i> Agendar Relatório</button>
                <button onclick="alert('Sem alertas críticos no momento.');" class="btn btn-outline-danger"><i class="fas fa-bell"></i> Alertas Críticos</button>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?= BASE_URL ?>/admin" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
    </div>
</section>

<script>
    const ctxBarras = document.getElementById('graficoBarras').getContext('2d');
    const ctxPizza = document.getElementById('graficoPizza').getContext('2d');

    new Chart(ctxBarras, {
        type: 'bar',
        data: {
            labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Vendas (€)',
                data: [320, 450, 390, 610, 280, 800, 760],
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
        },
        options: { responsive: true }
    });

    new Chart(ctxPizza, {
        type: 'pie',
        data: {
            labels: ['Hortifruti', 'Padaria', 'Bebidas', 'Importados'],
            datasets: [{
                data: [35, 25, 20, 20],
                backgroundColor: ['#28a745', '#ffc107', '#007bff', '#dc3545']
            }]
        },
        options: { responsive: true }
    });

    function exportarCSV() {
        const data = `ID,Cliente,Data,Valor,Status\n1021,João Silva,2025-04-24,39.90,Entregue\n1022,Maria Santos,2025-04-25,21.50,Pendente`;
        const blob = new Blob([data], { type: 'text/csv;charset=utf-8;' });
        saveAs(blob, 'relatorio.csv');
    }

    // 🎯 Filtro Simulado
    document.getElementById('filtroRelatorioForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const dataDe = this.data_de.value;
        const dataAte = this.data_ate.value;
        const categoria = this.categoria.value;
        const produto = this.produto.value;

        Swal.fire({
            title: 'Filtros aplicados!',
            html: `
                <div class="text-start small">
                    <b>De:</b> ${dataDe || '...'}<br>
                    <b>Até:</b> ${dataAte || '...'}<br>
                    <b>Categoria:</b> ${categoria}<br>
                    <b>Produto:</b> ${produto || 'Todos'}
                </div>
            `,
            icon: 'success',
            confirmButtonText: 'Ok',
        });

        console.log("🔎 Filtros simulados:", { dataDe, dataAte, categoria, produto });
    });
</script>

<script src="<?= BASE_URL ?>/app/Assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
