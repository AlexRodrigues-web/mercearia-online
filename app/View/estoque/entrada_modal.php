<?php
if (!isset($estoque)) {
    error_log("❌ entrada_modal.php: Estoque não definido.");
    exit('Produto não encontrado.');
}

error_log("📦 entrada_modal.php carregado para produto ID {$estoque['id']}");
?>

<form id="formEntradaEstoque" method="post">
    <input type="hidden" name="id" value="<?= (int)$estoque['id'] ?>">
    <input type="hidden" name="_csrf" value="<?= $_SESSION['_csrf'] ?? '' ?>">

    <div class="mb-3">
        <label class="form-label fw-bold">Produto</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($estoque['produto_nome']) ?>" disabled>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold" for="quantidade">Quantidade a adicionar</label>
        <input type="number" name="quantidade" id="quantidade" class="form-control" min="1" required>
    </div>

    <div class="text-end">
    <button type="button" class="btn btn-primary" id="btnSalvarEntradaEstoque">Confirmar</button>
    </div>
</form>

<script>
document.getElementById('formEntradaEstoque')?.addEventListener('submit', async function (e) {
    e.preventDefault();
    const form = e.target;
    const dados = new FormData(form);

    console.log("📤 Enviando dados do formulário de entrada:");
    for (const [chave, valor] of dados.entries()) {
        console.log(`  - ${chave}: ${valor}`);
    }

    try {
        const resposta = await fetch('<?= BASE_URL ?>estoque/entrada', {
            method: 'POST',
            body: dados,
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const resultado = await resposta.json();
        console.log("📬 Resposta recebida da entrada:", resultado);

        alert(resultado.message ?? 'Entrada registrada.');

        if (resultado.success || resultado.status) {
            location.reload();
        }
    } catch (err) {
        console.error('❌ Erro ao registrar entrada:', err);
        alert('Erro ao enviar os dados. Tente novamente.');
    }
});
</script>
