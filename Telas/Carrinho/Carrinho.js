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

    const inputs = document.querySelectorAll(".code-input");
    const submitButton = document.querySelector(".submit-button");

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
});

  function toggleLanguageOptions() {
    const languageOptions = document.getElementById('language-options');
    languageOptions.style.display = (languageOptions.style.display === 'block') ? 'none' : 'block';
}

function changeLanguage(language) {
    console.log('Idioma selecionado:', language);
    toggleLanguageOptions(); }

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