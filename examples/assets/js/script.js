/**
 * JavaScript de Exemplo - Asset Guard Standalone (V1.4)
 */

(function() {
    const run = () => {
        console.log("🛡️ Asset Guard: Script executado com sucesso!");
        const statusEl = document.getElementById("status");
        if (statusEl) {
            statusEl.innerText = "Proteção Ativa & Código Ofuscado";
            statusEl.classList.add("ready"); // Usa a classe do CSS Invisible
            console.log("🛡️ Asset Guard: Interface atualizada via Classe.");
        }
    };

    // Execução Segura
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", run);
    } else {
        run();
    }

    window.assetGuardTest = () => {
        alert("Você executou uma função que estava ofuscada no servidor!");
    }
})();
