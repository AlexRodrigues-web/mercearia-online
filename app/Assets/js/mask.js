// Máscara para NIF (Portugal)
let maskNif = $('#form-nif');
maskNif.mask('000 000 000'); 

// Máscara para preço 
let maskPreco = $('#form-preco');
maskPreco.mask('#.##0,00', { reverse: true });

// Máscara para quantidade 
let maskKilograma = $('#form-kilograma');
maskKilograma.mask('#.##0,000', { reverse: true });

// Máscara para quantidade 
let maskLitro = $('#form-litro');
maskLitro.mask('#.##0,000', { reverse: true });

// Função padrão 
function placeholderFunction() {
    console.info('Placeholder function executed.');
}

// Função para validar NIF 
function validarNif(nif) {
    const nifRegex = /^[1-9]\d{8}$/; 
    return nifRegex.test(nif);
}

// Função para exibir erros com SweetAlert2
function mostrarErro(titulo, mensagem) {
    Swal.fire({
        icon: 'error',
        title: titulo,
        text: mensagem,
        confirmButtonColor: '#d33'
    });
}

// Validações ao aplicar máscaras
$(document).ready(function () {

    // Validação do NIF
    maskNif.on('blur', function () {
        let nif = $(this).val().replace(/\s/g, ''); // Remove espaços
        if (!validarNif(nif)) {
            mostrarErro('NIF inválido!', 'O NIF deve conter exatamente 9 dígitos e ser válido.');
        }
    });

    // Validação de Preço
    maskPreco.on('blur', function () {
        let preco = parseFloat($(this).val().replace('.', '').replace(',', '.') || 0);
        if (preco <= 0) {
            mostrarErro('Preço inválido!', 'Insira um preço maior que zero.');
        }
    });

    // Validação de Quilogramas
    maskKilograma.on('blur', function () {
        let kilo = parseFloat($(this).val().replace('.', '').replace(',', '.') || 0);
        if (kilo <= 0) {
            mostrarErro('Valor inválido!', 'Insira um valor em quilogramas maior que zero.');
        }
    });

    // Validação de Litros
    maskLitro.on('blur', function () {
        let litro = parseFloat($(this).val().replace('.', '').replace(',', '.') || 0);
        if (litro <= 0) {
            mostrarErro('Valor inválido!', 'Insira um valor em litros maior que zero.');
        }
    });
});
