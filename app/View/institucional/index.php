<?php
if (!defined("MERCEARIA2025")) {
    die("Acesso negado.");
}

require_once __DIR__ . '/../include/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">Informações Institucionais</h1>

    <div class="list-group mb-4">
        <a href="#privacidade" class="list-group-item list-group-item-action">Política de Privacidade</a>
        <a href="#termos" class="list-group-item list-group-item-action">Termos de Uso</a>
        <a href="#entrega" class="list-group-item list-group-item-action">Política de Entrega</a>
    </div>

    <section id="privacidade" class="mb-5">
        <h2>🔒 Política de Privacidade</h2>
        <p>Esta política descreve como coletamos, usamos e protegemos as informações dos nossos clientes.</p>
        <ul>
            <li><strong>Dados coletados:</strong> Nome, e-mail, telefone, endereço, CPF/NIF e dados de navegação.</li>
            <li><strong>Uso das informações:</strong> Para processar pedidos, fazer entregas, oferecer suporte, enviar comunicações e promoções (se autorizado).</li>
            <li><strong>Compartilhamento:</strong> Apenas com parceiros essenciais como transportadoras, gateways de pagamento e sistemas antifraude.</li>
            <li><strong>Direitos do cliente:</strong> Acesso, correção, exclusão ou solicitação de cópia dos dados mediante solicitação.</li>
            <li><strong>Segurança:</strong> Utilizamos criptografia, proteção por senha e práticas seguras de armazenamento.</li>
            <li><strong>Cookies:</strong> Usamos cookies para melhorar a experiência do usuário e personalizar o conteúdo.</li>
        </ul>
    </section>

    <section id="termos" class="mb-5">
        <h2>📜 Termos de Uso</h2>
        <p>Estes termos definem as regras de uso do nosso site e serviços.</p>
        <ul>
            <li><strong>Elegibilidade:</strong> Apenas pessoas com 18 anos ou mais, com cadastro válido, podem efetuar compras.</li>
            <li><strong>Responsabilidades do cliente:</strong> Fornecer dados corretos, manter sigilo de login e respeitar o uso adequado do site.</li>
            <li><strong>Responsabilidades da mercearia:</strong> Garantir a entrega dos produtos, suporte eficiente e segurança da plataforma.</li>
            <li><strong>Proibições:</strong> Uso indevido, fraudes, tentativas de acesso não autorizado ou disseminação de conteúdo malicioso.</li>
            <li><strong>Alterações:</strong> Podemos atualizar preços, produtos ou condições de serviço sem aviso prévio.</li>
            <li><strong>Limitação de responsabilidade:</strong> Não nos responsabilizamos por indisponibilidade temporária do site por motivos técnicos.</li>
        </ul>
    </section>

    <section id="entrega" class="mb-5">
        <h2>🚚 Política de Entrega</h2>
        <p>Informações sobre os nossos serviços de entrega:</p>
        <ul>
            <li><strong>Áreas de entrega:</strong> Atendemos toda a região continental e ilhas de Portugal. Consulte a disponibilidade para sua localidade.</li>
            <li><strong>Prazos:</strong> Variam entre 1 a 5 dias úteis, conforme a zona e tipo de produto.</li>
            <li><strong>Frete:</strong> Pode haver taxa de entrega conforme o valor do pedido. Oferecemos frete grátis em compras acima de valores promocionais.</li>
            <li><strong>Tentativas:</strong> Realizamos até 2 tentativas de entrega. Após isso, o produto retorna ao centro de distribuição.</li>
            <li><strong>Retirada em loja:</strong> Disponível em determinadas localidades. Consulte as opções no checkout.</li>
            <li><strong>Problemas na entrega:</strong> Em caso de atrasos, extravios ou devoluções, entre em contato com nosso suporte para resolução rápida.</li>
        </ul>
    </section>
</div>

<?php require_once __DIR__ . '/../include/footer.php'; ?>
