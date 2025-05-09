document.addEventListener("DOMContentLoaded", function () {
    console.log("Script customProduto.js carregado!");

    let carrossel = document.querySelector("#carouselProdutos");
    if (carrossel) {
        let carouselInstance = new bootstrap.Carousel(carrossel, {
            interval: 3000,  // Tempo de troca das imagens
            ride: "carousel" 
        });
        console.log("Carrossel inicializado corretamente.");
    } else {
        console.error("Elemento #carouselProdutos não encontrado.");
    }

    let btnAdicionar = document.getElementById("btnAdicionarProduto");
    let inputQuantidade = document.getElementById("quantidadeProduto");
    let precoUnitarioElement = document.getElementById("precoUnitario");
    let precoTotal = document.getElementById("precoTotal");
    let tabelaProdutos = document.getElementById("tabelaProdutos");

    function extrairNumero(str) {
        return parseFloat(str.replace(/[^\d,.]/g, "").replace(",", ".")) || 0;
    }

    function formatarMoeda(valor) {
        return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function atualizarPrecoTotal() {
        if (inputQuantidade && precoUnitarioElement && precoTotal) {
            let precoUnitario = extrairNumero(precoUnitarioElement.innerText);
            let quantidade = parseFloat(inputQuantidade.value) || 0;
            let total = precoUnitario * quantidade;
            precoTotal.innerText = formatarMoeda(total);
        }
    }

    if (inputQuantidade) {
        inputQuantidade.addEventListener("input", atualizarPrecoTotal);
    }
    function escaparHTML(texto) {
        let div = document.createElement("div");
        div.innerText = texto;
        return div.innerHTML;
    }

    if (btnAdicionar) {
        btnAdicionar.addEventListener("click", function (event) {
            event.preventDefault(); 

            let nomeProdutoElement = document.getElementById("nomeProduto");
            if (!nomeProdutoElement) {
                console.error("Elemento 'nomeProduto' não encontrado.");
                return;
            }

            let nomeProduto = nomeProdutoElement.value.trim();
            let quantidade = parseFloat(inputQuantidade?.value) || 1;
            let precoUnitario = extrairNumero(precoUnitarioElement?.innerText) || 0;

            if (!nomeProduto) {
                Swal.fire("Erro", "O nome do produto é obrigatório.", "error");
                return;
            }
            if (quantidade <= 0 || precoUnitario <= 0) {
                Swal.fire("Erro", "Quantidade e preço devem ser positivos.", "error");
                return;
            }

            let novaLinha = `
                <tr>
                    <td>${escaparHTML(nomeProduto)}</td>
                    <td>${quantidade}</td>
                    <td>${formatarMoeda(precoUnitario)}</td>
                    <td>${formatarMoeda(precoUnitario * quantidade)}</td>
                    <td><button class="btnRemoverProduto">Remover</button></td>
                </tr>
            `;

            tabelaProdutos.insertAdjacentHTML("beforeend", novaLinha);

            nomeProdutoElement.value = "";
            if (inputQuantidade) inputQuantidade.value = 1;
            atualizarPrecoTotal();
        });
    }

    if (tabelaProdutos) {
        tabelaProdutos.addEventListener("click", function (event) {
            if (event.target.classList.contains("btnRemoverProduto")) {
                Swal.fire({
                    title: "Tem certeza?",
                    text: "Você deseja remover este produto?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sim, remover",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        event.target.closest("tr").remove();
                        Swal.fire("Removido!", "O produto foi removido com sucesso.", "success");
                        atualizarPrecoTotal();
                    }
                });
            }
        });
    }
});
