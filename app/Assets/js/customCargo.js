document.addEventListener("DOMContentLoaded", function () {
    console.log("customCargo.js carregado com sucesso!");

    // Captura elementos interativos
    let btnSalvar = document.getElementById("btnSalvarCargo");
    let btnExcluirCargos = document.querySelectorAll(".btnExcluirCargo");
    let formulario = document.getElementById("formCargo");

    // Função para validar o formulário antes de salvar
    function validarFormulario() {
        let nome = document.getElementById("nomeCargo")?.value.trim();
        let descricao = document.getElementById("descricaoCargo")?.value.trim();

        if (!nome || !descricao) {
            alert("Por favor, preencha todos os campos obrigatórios.");
            return false;
        }
        return true;
    }

    // Evento para salvar ou editar cargo
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
                        alert("Cargo salvo com sucesso!");
                        location.reload(); 
                    } else {
                        alert("Erro ao salvar cargo. Tente novamente.");
                    }
                })
                .catch(error => console.error("Erro na requisição:", error));
            }
        });
    }

    // Evento para excluir cargos
    if (btnExcluirCargos) {
        btnExcluirCargos.forEach((botao) => {
            botao.addEventListener("click", function () {
                let idCargo = botao.dataset.id;
                let url = botao.dataset.url;

                if (confirm("Tem certeza que deseja excluir este cargo?")) {
                    fetch(url, { 
                        method: "POST", 
                        body: JSON.stringify({ id: idCargo }),
                        headers: { "Content-Type": "application/json" }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "sucesso") {
                            alert("Cargo excluído com sucesso!");
                            location.reload();
                        } else {
                            alert("Erro ao excluir cargo.");
                        }
                    })
                    .catch(error => console.error("Erro na requisição:", error));
                }
            });
        });
    }
});
