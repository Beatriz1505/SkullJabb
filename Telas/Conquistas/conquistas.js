document.addEventListener("DOMContentLoaded", () => {
    const btnFiltro = document.getElementById("btnFiltro");
    const menuFiltro = document.getElementById("menuFiltro");

    if (!btnFiltro || !menuFiltro) {
        console.error("Elementos do filtro não encontrados");
        return;
    }

    // Função para alternar o menu
    function toggleMenu() {
        menuFiltro.classList.toggle("mostrar");
    }

    // Fechar menu ao clicar fora
    function fecharMenu(event) {
        if (!btnFiltro.contains(event.target) && !menuFiltro.contains(event.target)) {
            menuFiltro.classList.remove("mostrar");
        }
    }

    // Event listeners
    btnFiltro.addEventListener("click", (e) => {
        e.stopPropagation();
        toggleMenu();
    });

    document.addEventListener("click", fecharMenu);

    // Prevenir que cliques nos botões do menu fechem imediatamente
    menuFiltro.addEventListener("click", (e) => {
        if (e.target.tagName === 'BUTTON') {
            // O menu será fechado quando a página recarregar
            // devido ao redirecionamento do link
        }
    });

    // Fechar menu ao pressionar ESC
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            menuFiltro.classList.remove("mostrar");
        }
    });
});