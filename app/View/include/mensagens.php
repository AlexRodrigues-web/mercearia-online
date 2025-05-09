<?php
// ============================
// 📢 Função para exibir mensagens formatadas
// ============================
function exibirMensagem(string $tipo, string $mensagem): void {
    $tiposValidos = [
        'sucesso' => 'alert-success',
        'info' => 'alert-info',
        'aviso' => 'alert-warning',
    ];

    if (!empty($mensagem) && isset($tiposValidos[$tipo])) {
        echo '<div class="alert ' . $tiposValidos[$tipo] . ' text-center" role="alert">';
        echo htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8');
        echo '</div>';
    }
}

// ============================
// 🚀 Exibição das Mensagens (Apenas úteis, sem erro)
// ============================
$tiposMensagens = [
    'msg_sucesso' => 'sucesso',
    'msg_info' => 'info',
    'msg_aviso' => 'aviso',
];

$temMensagem = false; // Variável para verificar se há mensagens reais

foreach ($tiposMensagens as $chave => $tipo) {
    if (!empty($_SESSION[$chave])) {
        exibirMensagem($tipo, $_SESSION[$chave]);
        unset($_SESSION[$chave]); // Remove a mensagem da sessão após exibição
        $temMensagem = true; // Se exibir uma mensagem, marca como verdadeiro
    }
}

// ✅ Se não houver nenhuma mensagem válida, o script finaliza aqui
if (!$temMensagem) {
    return;
}
?>
