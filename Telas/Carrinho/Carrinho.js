// Funções auxiliares
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
    const languageButton = document.getElementById('language-button');

    if (!languageOptions.contains(event.target) && !languageButton.contains(event.target)) {
        languageOptions.style.display = 'none';
    }
}

document.addEventListener("DOMContentLoaded", function() {
    // 🔎 Barra de pesquisa
    const searchBar = document.querySelector(".search-bar");
    const searchIcon = document.querySelector(".search-icon");

    function performSearch() {
        const query = searchBar.value.trim();
        if (query) {
            window.location.href = `Pesquisa.html`;
        }
    }

    if (searchIcon && searchBar) {
        searchIcon.addEventListener("click", performSearch);
        searchBar.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                performSearch();
            }
        });
    }

    // 🔢 Inputs do código
    const inputs = document.querySelectorAll(".code-input");
    const submitButton = document.querySelector(".submit-button");

    if (inputs.length > 0 && submitButton) {
        inputs.forEach((input, index) => {
            input.addEventListener("input", () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace" && index > 0 && input.value === "") {
                    inputs[index - 1].focus();
                }
            });
        });

        submitButton.addEventListener("click", () => {
            const code = Array.from(inputs).map(input => input.value).join("");
            if (code.length === inputs.length) {
                alert(`Código digitado: ${code}`);
            } else {
                alert("Por favor, preencha todos os campos.");
            }
        });
    }

    // 🌐 Dropdown de idioma
    const languageButton = document.getElementById('language-button');
    if (languageButton) {
        languageButton.addEventListener('click', toggleLanguageOptions);
        document.addEventListener('click', closeDropdown);
    }
});
