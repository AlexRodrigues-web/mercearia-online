<?php
if (!defined("MERCEARIA2025")) {
    die("Erro: Acesso não autorizado.");
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

$titulo = $dados['titulo'] ?? 'Ajuda e Suporte';
$descricao = $dados['descricao'] ?? 'Encontre aqui respostas rápidas, tutoriais, formas de contato, políticas e dicas para uma melhor experiência de compra.';
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> | Mercearia</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/app/Assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/app/Assets/fontawesome/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
        }
        .section-title {
            margin-top: 2rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #007bff;
            padding-bottom: .5rem;
            font-size: 1.4rem;
        }
        ul.faq-list li {
            margin-bottom: 0.8rem;
        }
        .icon-section {
            margin-right: 8px;
            color: #007bff;
        }
        .accordion-button:focus {
            box-shadow: none;
        }
    </style>
</head>
<body>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="container py-5">

    <?php if (!empty($_SESSION['msg'])): ?>
        <div class="alert alert-info alert-dismissible fade show text-center shadow-sm fw-semibold" role="alert">
            <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <h1 class="text-center text-primary"><?= htmlspecialchars($titulo) ?></h1>
    <p class="text-center text-muted mb-4"><?= htmlspecialchars($descricao) ?></p>

    <!-- 🧩 Perguntas Frequentes -->
    <h2 class="section-title"><i class="fas fa-question-circle icon-section"></i> Perguntas Frequentes (FAQ)</h2>
    <div class="row">
        <div class="col-md-6">
            <h5>📦 Pedidos</h5>
            <ul class="faq-list">
                <li>Como faço um pedido?</li>
                <li>Posso alterar ou cancelar um pedido depois de finalizado?</li>
                <li>Como acompanho o status do meu pedido?</li>
            </ul>

            <h5>💳 Pagamentos (Portugal)</h5>
            <ul class="faq-list">
                <li>Quais formas de pagamento são aceitas?<br><small>MB Way, Multibanco, Cartão de Crédito e Débito, Transferência Bancária.</small></li>
                <li>O pagamento é seguro?</li>
                <li>O que fazer se o pagamento for recusado?</li>
            </ul>

            <h5>🚚 Entregas</h5>
            <ul class="faq-list">
                <li>Quais são as áreas de entrega?</li>
                <li>Qual o prazo de entrega?</li>
                <li>O que acontece se eu não estiver em casa no momento da entrega?</li>
            </ul>
        </div>

        <div class="col-md-6">
            <h5>💰 Trocas e Devoluções</h5>
            <ul class="faq-list">
                <li>Como faço para devolver um produto?</li>
                <li>Posso trocar produtos com defeito?</li>
                <li>Em quanto tempo recebo o reembolso?</li>
            </ul>

            <h5>🎁 Promoções e Cupons</h5>
            <ul class="faq-list">
                <li>Como aplicar um cupom de desconto?</li>
                <li>Posso usar mais de um cupom?</li>
                <li>Por que meu cupom não está funcionando?</li>
            </ul>

            <h5>📱 Conta e Cadastro</h5>
            <ul class="faq-list">
                <li>Como crio uma conta?</li>
                <li>Esqueci minha senha, o que fazer?</li>
                <li>Como atualizo meus dados?</li>
            </ul>
        </div>
    </div>

    <!-- 📘 Guias e Dicas -->
<h2 class="section-title"><i class="fas fa-lightbulb icon-section"></i> Guias Rápidos e Dicas</h2>
<div class="accordion accordion-flush" id="guiasAccordion">
    <?php
    $guias = [
        "Como comprar passo a passo" => "Navegue pela seção de produtos, clique no item desejado, escolha a quantidade e unidade (quando aplicável), adicione ao carrinho e finalize a compra no caixa.",
        "Como criar uma conta" => "Clique em 'Registrar' no topo da página, preencha seus dados (nome, email e senha) e confirme o cadastro. Seu acesso será criado automaticamente.",
        "Como acompanhar seu pedido" => "Vá até 'Meus Pedidos' no menu lateral para visualizar o status, data e detalhes de cada pedido já realizado.",
        "Como adicionar itens ao carrinho" => "Acesse a página de produtos, clique em 'Adicionar ao Carrinho', selecione a quantidade e unidade e confirme.",
        "Como usar cupons e aproveitar promoções" => "No carrinho ou no caixa, insira seu código promocional no campo indicado e clique em aplicar para ver o desconto.",
        "Cuidados com produtos perecíveis" => "Refrigere imediatamente após o recebimento. Verifique a data de validade e evite deixar fora da embalagem original por muito tempo.",
        "Dicas para armazenar alimentos frescos" => "Frutas e legumes devem ser armazenados em locais arejados ou na gaveta inferior do frigorífico. Use recipientes herméticos para carnes e laticínios.",
        "Como montar uma lista de compras eficiente" => "Liste os produtos que faltam em casa, planeje as refeições da semana e priorize itens essenciais e frescos.",
        "Produtos recomendados para cada estação do ano" => "Escolha frutas, legumes e vegetais da época para garantir frescor, melhor preço e sustentabilidade."
    ];
    $index = 0;
    foreach ($guias as $tituloGuia => $descricaoGuia):
        $index++;
        $id = "guia" . $index;
    ?>
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading<?= $id ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $id ?>">
                <?= $tituloGuia ?>
            </button>
        </h2>
        <div id="collapse<?= $id ?>" class="accordion-collapse collapse" data-bs-parent="#guiasAccordion">
            <div class="accordion-body"><?= $descricaoGuia ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>


    <!-- 💬 Central de Atendimento -->
    <h2 class="section-title"><i class="fas fa-headset icon-section"></i> Central de Atendimento</h2>
    <ul>
        <li><strong>Email:</strong> suporte@mercearia.pt</li>
        <li><strong>Telefone:</strong> +351 910 000 000</li>
        <li><strong>Horário de atendimento:</strong> Segunda a Sexta, das 9h às 18h</li>
    </ul>

    <form class="border rounded p-4 bg-white shadow-sm mt-4" method="POST" action="<?= BASE_URL ?>ajuda/enviar" enctype="multipart/form-data">
        <h5>Fale Conosco</h5>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" id="nome" name="nome" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
        </div>
        <label for="assunto" class="form-label">Assunto</label>
        <input type="text" id="assunto" name="assunto" class="form-control mb-3" required>

        <label for="mensagem" class="form-label">Mensagem</label>
        <textarea id="mensagem" name="mensagem" rows="4" class="form-control mb-3" required></textarea>

        <label for="anexo" class="form-label">Anexo (opcional)</label>
        <input type="file" id="anexo" name="anexo" class="form-control mb-3">

        <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
    </form>

    <!-- 📍 Políticas -->
    <h2 class="section-title"><i class="fas fa-file-alt icon-section"></i> Políticas Importantes</h2>
    <ul>
        <li><a href="<?= BASE_URL ?>institucional#privacidade">Política de Entregas</a></li>
        <li><a href="<?= BASE_URL ?>institucional#privacidade">Política de Trocas e Devoluções</a></li>
        <li><a href="<?= BASE_URL ?>institucional#privacidade">Política de Pagamento</a></li>
        <li><a href="<?= BASE_URL ?>institucional#privacidade">Política de Privacidade</a></li>
        <li><a href="<?= BASE_URL ?>institucional#privacidade">Termos e Condições de Uso</a></li>
    </ul>

    <!-- 👥 Acesso Rápido -->
    <?php if (!empty($_SESSION['usuario_logado'])): ?>
        <h2 class="section-title"><i class="fas fa-user icon-section"></i> Acesso Rápido</h2>
        <ul>
            <li><a href="<?= BASE_URL ?>pedidos">Ver meus pedidos</a></li>
            <li><a href="<?= BASE_URL ?>perfil/editar">Atualizar meus dados</a></li>
            <li><a href="<?= BASE_URL ?>entregas">Acompanhar entrega</a></li>
            <li><a href="<?= BASE_URL ?>ajuda/contato">Enviar uma solicitação</a></li>
        </ul>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../include/footer.php'; ?>

<script src="<?= BASE_URL ?>/app/Assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
