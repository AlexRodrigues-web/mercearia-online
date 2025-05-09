document.addEventListener("DOMContentLoaded", function () {

    // Captura elementos interativos da página
    let btnSalvar = document.getElementById("btnSalvarAlteracoes");
    let btnExcluir = document.getElementById("btnExcluirConta");
    let formulario = document.getElementById("formEditarPerfil");

    // Função para validar o formulário antes de salvar
    function validarFormulario() {
        let nome = document.getElementById("nomeUsuario")?.value.trim();
        let email = document.getElementById("emailUsuario")?.value.trim();

        if (!nome || !email) {
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

    // Evento para salvar alterações
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
                        Swal.fire({
                            icon: "success",
                            title: "Alterações salvas com sucesso!",
                            showConfirmButton: false,
                            timer: 1500
                        });
                        // Atualizar alguma seção sem reload
                        document.getElementById("perfilNome").textContent = nome;
                    } else {
                        Swal.fire("Erro", "Erro ao salvar alterações. Tente novamente.", "error");
                    }
                })
                .catch(error => {
                    console.error("Erro na requisição:", error);
                    Swal.fire("Erro", "Erro inesperado ao salvar alterações.", "error");
                });
            }
        });
    }

    // Evento para exclusão de conta (com confirmação)
    if (btnExcluir) {
        btnExcluir.addEventListener("click", function () {
            Swal.fire({
                title: "Tem certeza?",
                text: "Esta ação é irreversível!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sim, excluir!",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(btnExcluir.dataset.url, {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ id: btnExcluir.dataset.userId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "sucesso") {
                            Swal.fire({
                                icon: "success",
                                title: "Conta excluída com sucesso!",
                                showConfirmButton: false,
                                timer: 1500
                            });
                            setTimeout(() => {
                                window.location.href = "/logout";
                            }, 1500);
                        } else {
                            Swal.fire("Erro", "Erro ao excluir a conta. Tente novamente.", "error");
                        }
                    })
                    .catch(error => {
                        console.error("Erro na requisição:", error);
                        Swal.fire("Erro", "Erro inesperado ao excluir conta.", "error");
                    });
                }
            });
        });
    }
});
