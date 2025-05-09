document.addEventListener("DOMContentLoaded", function () {

    // Captura elementos interativos
    let btnSalvar = document.getElementById("btnSalvarFornecedor");
    let btnExcluirFornecedores = document.querySelectorAll(".btnExcluirFornecedor");
    let formulario = document.getElementById("formFornecedor");

    // Função para validar o formulário antes de salvar
    function validarFormulario() {
        let nome = document.getElementById("nomeFornecedor")?.value.trim();
        let email = document.getElementById("emailFornecedor")?.value.trim();
        let telefone = document.getElementById("telefoneFornecedor")?.value.trim();

        if (!nome || !email || !telefone) {
            alert("Por favor, preencha todos os campos obrigatórios.");
            return false;
        }
        return true;
    }

    // Evento para salvar ou editar fornecedor
    if (btnSalvar && formulario) {
        btnSalvar.addEventListener("click", function (event) {
            event.preventDefault();

            if (validarFormulario()) {
                let dadosFormulario = new FormData(formulario);

                fetch(formulario.action, {
                    method: "POST",
                    body: dadosFormulario
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "sucesso") {
                        alert("Fornecedor salvo com sucesso!");
                        location.reload(); 
                    } else {
                        alert("Erro ao salvar fornecedor. Tente novamente.");
                    }
                })
                .catch(error => console.error("Erro na requisição:", error));
            }
        });
    }

    // Evento para excluir fornecedores
    if (btnExcluirFornecedores) {
        btnExcluirFornecedores.forEach((botao) => {
            botao.addEventListener("click", function () {
                let idFornecedor = botao.dataset.id;
                let url = botao.dataset.url;

                if (confirm("Tem certeza que deseja excluir este fornecedor?")) {
                    fetch(url, { 
                        method: "POST", 
                        body: JSON.stringify({ id: idFornecedor }),
                        headers: { "Content-Type": "application/json" }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "sucesso") {
                            alert("Fornecedor excluído com sucesso!");
                            location.reload();
                        } else {
                            alert("Erro ao excluir fornecedor.");
                        }
                    })
                    .catch(error => console.error("Erro na requisição:", error));
                }
            });
        });
    }
});
