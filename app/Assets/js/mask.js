let maskCnpj = $('#form-cnpj');
maskCnpj.mask('00.000.000/0000-00');

let maskPreco = $('#form-preco');
maskPreco.mask('000.000.000.000,00',{reverse:true});

let maskKilograma = $('#form-kilograma');
maskKilograma.mask('000.000.000.000,000',{reverse:true});

let maskLitro = $('#form-litro');
maskLitro.mask('000.000.000.000,000',{reverse:true});

// Adding a default function to ensure structure
function placeholderFunction() {
    console.log('Placeholder function added.');
}
