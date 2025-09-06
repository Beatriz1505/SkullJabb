document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('loginForm');

  loginForm.addEventListener('submit', function (e) {
    e.preventDefault(); // Impede o envio do form

    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('password').value.trim();

    const usuarioValido = "nikolas@gmail.com";
    const senhaValida = "1234";

    if (email === usuarioValido && senha === senhaValida) {
      alert("Login bem-sucedido!");
      window.location.href = "Perfil.html"; // Redireciona para o perfil
    } else {
      alert("Usuário ou senha incorretos!");
    }
  });
});