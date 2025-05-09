document.addEventListener("DOMContentLoaded", function () {

    // Captura elementos interativos
    let btnSalvar = document.getElementById("btnSalvarFuncionario");
    let btnExcluirFuncionarios = document.querySelectorAll(".btnExcluirFuncionario");
    let formulario = document.getElementById("formFuncionario");

    // Função para validar o formulário antes de salvar
    function validarFormulario() {
        let nome = document.getElementById("nomeFuncionario")?.value.trim();
        let email = document.getElementById("emailFuncionario")?.value.trim();
        let cargo = document.getElementById("cargoFuncionario")?.value.trim();

        if (!nome || !email || !cargo) {
            Swal.fire("Erro", "Preencha todos os campos obrigatórios.", "error");
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            Swal.fire("Erro", "Insira um e-mail válido.", "error");
            return false;
        }

        return true;
    }

    // Evento para salvar ou editar funcionário
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
                        Swal.fire("Sucesso!", "Funcionário salvo com sucesso!", "success");
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire("Erro", "Erro ao salvar funcionário. Tente novamente.", "error");
                    }
                })
                .catch(error => console.error("Erro na requisição:", error));
            }
        });
    }

    // Evento para excluir funcionários
    if (btnExcluirFuncionarios) {
        btnExcluirFuncionarios.forEach((botao) => {
            botao.addEventListener("click", function () {
                let idFuncionario = botao.dataset.id;
                let url = botao.dataset.url;

                Swal.fire({
                    title: "Tem certeza?",
                    text: "Esta ação não poderá ser desfeita!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sim, excluir!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(url, { 
                            method: "POST", 
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ id: idFuncionario }) 
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "sucesso") {
                                Swal.fire("Excluído!", "Funcionário removido com sucesso.", "success");
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                Swal.fire("Erro", "Erro ao excluir funcionário.", "error");
                            }
                        })
                        .catch(error => console.error("Erro na requisição:", error));
                    }
                });
            });
        });
    }
});
