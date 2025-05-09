(function () {
    "use strict";

    var test = document.createElement("div");
    test.className = "hidden d-none";
    document.body.appendChild(test);

    var cssLoaded = false;
    try {
        cssLoaded = window.getComputedStyle && window.getComputedStyle(test).display === "none";
    } catch (error) {
        console.error("Erro ao verificar CSS:", error);
    } finally {
        document.body.removeChild(test);
    }

    // Carregar fallback se CSS não estiver disponível
    if (!cssLoaded) {
        console.warn("CSS do Bootstrap não carregado via CDN. Aplicando fallback local.");
        carregarCssLocal();
    }

    function carregarCssLocal() {
        const link = document.createElement("link");
        link.type = "text/css";
        link.rel = "stylesheet";
        link.async = true; 

        const baseURL = window.BASE_URL || window.location.origin + "/mercearia";
        link.href = `${baseURL}/app/Assets/bootstrap/css/bootstrap.css`;

        document.head.appendChild(link);

        link.onload = () => {
            console.log("CSS local carregado com sucesso.");
        };
        link.onerror = () => {
            console.error("Erro ao carregar o CSS local.");
        };
    }
})();
