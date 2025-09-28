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

 function toggleLanguageOptions() {
    const languageOptions = document.getElementById('language-options');
    languageOptions.style.display = (languageOptions.style.display === 'block') ? 'none' : 'block';
}

function changeLanguage(language) {
    console.log('Idioma selecionado:', language);
    toggleLanguageOptions();
}

function closeDropdown(event) {
    const languageOptions = document.getElementById('language-options');
    
    if (!languageOptions.contains(event.target) && !document.getElementById('language-button').contains(event.target)) {
        languageOptions.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const languageButton = document.getElementById('language-button');
    
    languageButton.addEventListener('click', toggleLanguageOptions);
    document.addEventListener('click', closeDropdown); 
});