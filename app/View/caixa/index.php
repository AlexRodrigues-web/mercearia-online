<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

define('APP_ROOT', dirname(__DIR__, 3));
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$baseUrl = rtrim($protocolo . "://" . $host . dirname($scriptName), '/') . '/';

if (!defined('BASE_URL')) {
    define('BASE_URL', $baseUrl);
}

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = '<div class="alert alert-danger text-center">Por favor, faça o login para acessar o sistema.</div>';
    header("Location: " . BASE_URL . "login", true, 302);
    exit();
}

if (empty($_SESSION['token']) || !isset($_SESSION['csrf_time']) || time() - $_SESSION['csrf_time'] > 1800) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_time'] = time();
}

$carrinho = $_SESSION['carrinho'] ?? [];
$desconto = $_SESSION['desconto'] ?? 0;
$total = 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Compra</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/css/bootstrap.min.css">
    <script src="<?= BASE_URL ?>app/Assets/js/bootstrap.bundle.min.js" defer></script>
    <style>
        .bloco-pagamento {
            display: none;
            padding: 15px;
            margin-top: 20px;
            border-radius: 8px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h2 class="text-center text-success mb-4">🧾 Finalizar Compra</h2>

    <?php if (!empty($_SESSION['msg_erro'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['msg_erro']; unset($_SESSION['msg_erro']); ?></div>
    <?php elseif (!empty($_SESSION['msg_sucesso'])): ?>
        <div class="alert alert-success"><?= $_SESSION['msg_sucesso']; unset($_SESSION['msg_sucesso']); ?></div>
    <?php endif; ?>

    <?php if (empty($carrinho)): ?>
        <div class="alert alert-warning text-center">Seu carrinho está vazio.</div>
    <?php else: ?>
        <form action="<?= BASE_URL ?>caixa/finalizarPedido" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <!-- 1. Identificação do Cliente -->
            <div class="mb-4">
                <h4 class="mb-3">👤 Dados do Cliente</h4>
                <p><strong>Nome:</strong> <?= $_SESSION['usuario_nome'] ?? '—' ?></p>
                <p><strong>Email:</strong> <?= $_SESSION['usuario_email'] ?? '—' ?></p>
                <input type="hidden" name="cliente_id" value="<?= $_SESSION['usuario_id'] ?>">
            </div>

            <!-- 2. Endereço de Entrega -->
            <div class="mb-4">
                <h4 class="mb-3">📦 Endereço de Entrega</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Nome Completo</label>
                        <input type="text" name="nome_entrega" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Telefone</label>
                        <input type="text" name="telefone" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label>Morada (Rua, nº, andar, complemento)</label>
                        <input type="text" name="morada" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Código Postal</label>
                        <input type="text" name="codigo_postal" class="form-control" required placeholder="Ex: 1000-123">
                    </div>
                    <div class="col-md-4">
                        <label>Cidade</label>
                        <input type="text" name="cidade" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Distrito</label>
                        <input type="text" name="distrito" class="form-control" required>
                    </div>
                </div>
            </div>

            <!-- 3. Método de Entrega -->
            <div class="mb-4">
                <h4 class="mb-3">🚚 Método de Entrega</h4>
                <select name="metodo_entrega" class="form-control" required>
                    <option value="">Selecione</option>
                    <option value="domicilio">Entrega ao domicílio - €3,50</option>
                    <option value="retirada">Retirada na loja</option>
                </select>
            </div>

            <!-- 4. Método de Pagamento -->
            <div class="mb-4">
                <h4 class="mb-3">💳 Método de Pagamento</h4>
                <select name="metodo_pagamento" id="metodo_pagamento" class="form-control" required>
                    <option value="">Selecione</option>
                    <option value="multibanco">Referência Multibanco</option>
                    <option value="mbway">MB Way</option>
                    <option value="cartao">Cartão de crédito/débito</option>
                    <option value="paypal">PayPal</option>
                    <option value="dinheiro">Contra Entrega</option>
                </select>

                <!-- Campos específicos de pagamento -->
                <div id="pagamento_cartao" class="bloco-pagamento">
                    <label>Número do Cartão</label>
                    <input type="text" name="numero_cartao" class="form-control mb-2" placeholder="XXXX XXXX XXXX XXXX">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Validade</label>
                            <input type="text" name="validade_cartao" class="form-control" placeholder="MM/AA">
                        </div>
                        <div class="col-md-6">
                            <label>CVV</label>
                            <input type="text" name="cvv_cartao" class="form-control" placeholder="***">
                        </div>
                    </div>
                </div>

                <div id="pagamento_mbway" class="bloco-pagamento">
                    <label>Número de Telemóvel (MB WAY)</label>
                    <input type="text" name="telefone_mbway" class="form-control" placeholder="Ex: 912345678">
                </div>

                <div id="pagamento_multibanco" class="bloco-pagamento">
                    <p>⚠️ A referência Multibanco será gerada após o envio do pedido.</p>
                </div>

                <div id="pagamento_paypal" class="bloco-pagamento">
                    <p>🔒 Você será redirecionado ao PayPal para concluir o pagamento com segurança.</p>
                </div>
            </div>

            <!-- 5. Resumo do Pedido -->
            <div class="mb-4">
                <h4 class="mb-3">📦 Resumo do Pedido</h4>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th>Qtd</th>
                            <th>Preço</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrinho as $item):
                            $subtotal = $item['preco'] * $item['quantidade'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nome']) ?></td>
                                <td><?= $item['quantidade'] ?></td>
                                <td>€ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                                <td>€ <?= number_format($subtotal, 2, ',', '.') ?></td>
                                <input type="hidden" name="produto[<?= $item['id'] ?>]" value="<?= $item['quantidade'] ?>">
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p><strong>Subtotal:</strong> € <?= number_format($total, 2, ',', '.') ?></p>
                <p><strong>Desconto:</strong> € <?= number_format(($desconto / 100) * $total, 2, ',', '.') ?></p>
                <p><strong>Total Final:</strong> € <?= number_format($total - (($desconto / 100) * $total), 2, ',', '.') ?></p>
            </div>

            <!-- 6. Termos e Comentário -->
            <div class="mb-4">
                <label for="comentario">Comentário ou instrução especial</label>
                <textarea name="comentario" id="comentario" rows="3" class="form-control" placeholder="Ex: Tocar campainha, entregar na portaria..."></textarea>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="aceite_termos" id="aceite_termos" required>
                    <label class="form-check-label" for="aceite_termos">
                        Li e aceito os <a href="<?= BASE_URL ?>politica" target="_blank">termos e condições</a>.
                    </label>
                </div>
            </div>

            <!-- 7. Botão Finalizar -->
            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-check-circle"></i> Finalizar Compra
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('metodo_pagamento');
    const blocos = {
        cartao: document.getElementById('pagamento_cartao'),
        mbway: document.getElementById('pagamento_mbway'),
        multibanco: document.getElementById('pagamento_multibanco'),
        paypal: document.getElementById('pagamento_paypal')
    };

    select.addEventListener('change', () => {
        Object.values(blocos).forEach(div => div.style.display = 'none');
        const valor = select.value;
        if (valor && blocos[valor]) {
            blocos[valor].style.display = 'block';
        }
    });
});
</script>
</body>
</html>
