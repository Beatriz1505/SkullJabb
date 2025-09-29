document.addEventListener("DOMContentLoaded", function() {
    const searchBar = document.querySelector(".search-bar");
    const searchIcon = document.querySelector(".search-icon");

    function performSearch() {
        const query = searchBar.value.trim();
        if (query) {
            window.location.href = `Pesquisa.html`;
        }
    }

    searchIcon.addEventListener("click", performSearch);

    searchBar.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            performSearch();
        }
    });
});

 // Função para alternar a exibição das opções de idioma
 function toggleLanguageOptions() {
    const languageOptions = document.getElementById('language-options');
    languageOptions.style.display = (languageOptions.style.display === 'block') ? 'none' : 'block';
}

// Função para alterar o idioma
function changeLanguage(language) {
    console.log('Idioma selecionado:', language);
    toggleLanguageOptions(); // Oculta as opções após a seleção
}

// Função para fechar o dropdown ao clicar fora dele
function closeDropdown(event) {
    const languageOptions = document.getElementById('language-options');
    
    // Verifica se o clique foi fora do dropdown e do botão
    if (!languageOptions.contains(event.target) && !document.getElementById('language-button').contains(event.target)) {
        languageOptions.style.display = 'none';
    }
}

// Adiciona listeners de evento para idioma
document.addEventListener('DOMContentLoaded', () => {
    const languageButton = document.getElementById('language-button');
    
    languageButton.addEventListener('click', toggleLanguageOptions);
    document.addEventListener('click', closeDropdown); // Escuta cliques fora do dropdown
});