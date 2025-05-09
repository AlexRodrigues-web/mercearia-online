<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// DEBUG: Checar se sessão existe e logar
error_log("[DEBUG] Sessão ativa: " . (session_status() === PHP_SESSION_ACTIVE ? "SIM" : "NÃO"));
error_log("[DEBUG] Conteúdo da sessão: " . print_r($_SESSION, true));

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'], 2)), '/');
    define('BASE_URL', "{$protocol}://{$host}{$basePath}");
}

error_log("[DEBUG] \$itens: " . print_r($itens ?? 'NÃO DEFINIDO', true));
error_log("[DEBUG] \$desconto: " . var_export($desconto ?? 'NÃO DEFINIDO', true));
error_log("[DEBUG] \$cupom_msg: " . var_export($cupom_msg ?? 'NÃO DEFINIDO', true));

$carrinho = $itens ?? [];
$desconto = $desconto ?? 0;
$cupom_msg = $cupom_msg ?? null;
$total = 0;

require_once __DIR__ . '/../include/header.php';
?>

<div class="container my-5">
    <h2 class="text-center text-primary mb-4"><i class="fas fa-shopping-cart"></i> Meu Carrinho</h2>

    <?php if ($cupom_msg): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($cupom_msg) ?></div>
    <?php endif; ?>

    <?php if (empty($carrinho)): ?>
        <div class="alert alert-info text-center">
            Seu carrinho está vazio. <a href="<?= BASE_URL ?>produtos">Ver produtos</a>.
        </div>
    <?php else: ?>

        <!-- Botão Continuar Comprando -->
        <div class="mb-4">
            <a href="<?= BASE_URL ?>produtos" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Continuar Comprando
            </a>
        </div>

        <form action="<?= BASE_URL ?>carrinho/atualizar" method="POST">
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th>Imagem</th>
                            <th>Preço Unitário</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrinho as $id => $item): 
                            $subtotal = $item['preco'] * $item['quantidade'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nome']) ?></td>
                                <td><img src="<?= BASE_URL ?>app/Assets/image/produtos/<?= htmlspecialchars($item['imagem']) ?>" width="60" class="img-thumbnail"></td>
                                <td>€ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                                <td>
                                    <input type="number" name="quantidades[<?= $id ?>]" value="<?= $item['quantidade'] ?>" min="1" class="form-control text-center" style="width: 80px;">
                                </td>
                                <td>€ <?= number_format($subtotal, 2, ',', '.') ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>carrinho/remover?id=<?= $id ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Cupom -->
            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <label for="cupom" class="form-label">Cupom de Desconto</label>
                    <input type="text" name="cupom" id="cupom" class="form-control" placeholder="Digite o código do cupom">
                    <small class="text-muted">Insira seu cupom promocional se tiver um.</small>
                </div>
                <div class="col-md-6 d-flex align-items-end justify-content-end">
                    <button type="submit" class="btn btn-warning">Atualizar Carrinho</button>
                </div>
            </div>
        </form>

        <!-- Estimativa de frete -->
        <div class="row mt-5">
            <div class="col-md-6 mb-3">
                <label for="codigo_postal" class="form-label">Calcular Entrega (Código Postal)</label>
                <input type="text" id="codigo_postal" class="form-control" placeholder="Ex: 1000-001" maxlength="8">
                <small class="text-muted">Digite apenas os números. O sistema aplicará o formato correto.</small>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="button" class="btn btn-outline-primary" id="btnCalcularFrete" onclick="calcularFrete()">Calcular Frete</button>
            </div>
        </div>

        <!-- Resultado do frete -->
        <div id="resultado-frete" class="mt-2 ms-2"></div>

        <script>
        // Máscara automática para código postal (formato 0000-000)
        document.getElementById('codigo_postal').addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').slice(0, 7);
            if (v.length > 4) {
                this.value = v.slice(0, 4) + '-' + v.slice(4);
            } else {
                this.value = v;
            }
        });

        function calcularFrete() {
            const cep = document.getElementById('codigo_postal').value.trim();
            const resultado = document.getElementById('resultado-frete');

            if (!cep.match(/^\d{4}-\d{3}$/)) {
                resultado.innerHTML = "<span class='text-danger'>Formato inválido. Ex: 1000-001</span>";
                return;
            }

            // Simulação do frete
            resultado.innerHTML = `<span class="text-success">Frete estimado: €3.50 - Entrega em até 2 dias úteis</span>`;
        }
        </script>

        <!-- Resumo -->
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h4 class="mb-3 text-secondary">Resumo da Compra</h4>
                <p><strong>Subtotal:</strong> € <?= number_format($total, 2, ',', '.') ?></p>
                <p><strong>Desconto:</strong> € <?= number_format(($desconto / 100) * $total, 2, ',', '.') ?></p>
                <p><strong>Frete:</strong> A calcular</p>
                <h5 class="text-success">Total Final: € <?= number_format($total - (($desconto / 100) * $total), 2, ',', '.') ?></h5>

                <?php if ($total < 50): ?>
                    <p class="text-danger"><i class="fas fa-truck"></i> Adicione mais €<?= number_format(50 - $total, 2, ',', '.') ?> para ganhar frete grátis!</p>
                <?php else: ?>
                    <p class="text-success"><i class="fas fa-truck"></i> Parabéns! Você ganhou frete grátis!</p>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>caixa" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-check-circle"></i> Finalizar Compra
                </a>

            </div>
        </div>

        <!-- Segurança -->
        <div class="mt-4">
            <p class="text-muted"><i class="fas fa-shield-alt"></i> Compra segura com criptografia.</p>
            <p class="text-muted"><i class="fas fa-undo-alt"></i> Troca ou devolução em até 7 dias.</p>
            <p class="text-muted"><i class="fas fa-credit-card"></i> Pagamento via Multibanco, MB Way, Visa, Mastercard, e mais.</p>
        </div>

        <?php if (!isset($_SESSION['usuario_id'])): ?>
            <div class="alert alert-warning mt-5 text-center">
                Já tem uma conta? <a href="<?= BASE_URL ?>login">Faça login</a> para salvar seu carrinho.
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../include/footer.php'; ?>
