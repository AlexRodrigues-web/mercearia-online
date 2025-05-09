document.addEventListener("DOMContentLoaded", function () {

    // Captura elementos interativos
    let btnAdicionarEstoque = document.querySelectorAll(".btnAdicionarEstoque");
    let btnRemoverEstoque = document.querySelectorAll(".btnRemoverEstoque");
    let estoqueBaixo = document.querySelectorAll(".estoqueBaixo");

    // Exibir alerta para produtos com estoque baixo
    if (estoqueBaixo) {
        estoqueBaixo.forEach((produto) => {
            alert(`Atenção! O produto "${produto.dataset.nome}" está com estoque baixo (${produto.dataset.quantidade} unidades).`);
        });
    }

    // Função para atualizar estoque via AJAX
    function atualizarEstoque(produtoId, quantidade, acao) {
        fetch("/estoque/atualizar", {
            method: "POST",
            body: JSON.stringify({ id: produtoId, quantidade: quantidade, acao: acao }),
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "sucesso") {
                alert(`Estoque atualizado: ${data.mensagem}`);
                location.reload();
            } else {
                alert("Erro ao atualizar o estoque. Tente novamente.");
            }
        })
        .catch(error => console.error("Erro na requisição:", error));
    }

    // Evento para adicionar estoque
    if (btnAdicionarEstoque) {
        btnAdicionarEstoque.forEach((botao) => {
            botao.addEventListener("click", function () {
                let produtoId = botao.dataset.id;
                let quantidade = parseFloat(prompt("Digite a quantidade a adicionar:"));
                
                if (!isNaN(quantidade) && quantidade > 0) {
                    atualizarEstoque(produtoId, quantidade, "adicionar");
                } else {
                    alert("Quantidade inválida.");
                }
            });
        });
    }

    // Evento para remover estoque
    if (btnRemoverEstoque) {
        btnRemoverEstoque.forEach((botao) => {
            botao.addEventListener("click", function () {
                let produtoId = botao.dataset.id;
                let quantidade = parseFloat(prompt("Digite a quantidade a remover:"));

                if (!isNaN(quantidade) && quantidade > 0) {
                    atualizarEstoque(produtoId, quantidade, "remover");
                } else {
                    alert("Quantidade inválida.");
                }
            });
        });
    }
});
