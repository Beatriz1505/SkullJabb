document.addEventListener("DOMContentLoaded", function () {
    // --- Pesquisa ---
    const searchBar = document.querySelector(".search-bar");
    const searchIcon = document.querySelector(".search-icon");

    function performSearch() {
        const query = searchBar.value.trim();
        if (query) {
            window.location.href = `Pesquisa.html?q=${encodeURIComponent(query)}`;
        }
    }

    if (searchIcon) {
        searchIcon.addEventListener("click", performSearch);
    }

    if (searchBar) {
        searchBar.addEventListener("keypress", function (event) {
            if (event.key === "Enter") {
                performSearch();
            }
        });
    }

    // --- Idioma ---
    const languageButton = document.getElementById('language-button');
    const languageOptions = document.getElementById('language-options');

    function toggleLanguageOptions() {
        if (languageOptions) {
            languageOptions.style.display = 
                (languageOptions.style.display === 'block') ? 'none' : 'block';
        }
    }

    function closeDropdown(event) {
        if (
            languageOptions &&
            !languageOptions.contains(event.target) &&
            !languageButton.contains(event.target)
        ) {
            languageOptions.style.display = 'none';
        }
    }

    if (languageButton) {
        languageButton.addEventListener('click', toggleLanguageOptions);
    }
    document.addEventListener('click', closeDropdown);

    // --- CPF ---
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, ""); // só números

            // limita a 11 dígitos
            if (value.length > 11) value = value.slice(0, 11);

            // aplica máscara XXX.XXX.XXX-XX
            if (value.length > 9) {
                value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*/, "$1.$2.$3-$4");
            } else if (value.length > 6) {
                value = value.replace(/^(\d{3})(\d{3})(\d{0,3}).*/, "$1.$2.$3");
            } else if (value.length > 3) {
                value = value.replace(/^(\d{3})(\d{0,3}).*/, "$1.$2");
            }

            e.target.value = value;
        });
    }
});

// função para mudar idioma
function changeLanguage(language) {
    console.log('Idioma selecionado:', language);
    const languageOptions = document.getElementById('language-options');
    if (languageOptions) {
        languageOptions.style.display = 'none';
    }
}
