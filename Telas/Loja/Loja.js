// faz os botões deslizarem o carrossel
document.querySelectorAll(".carousel").forEach(carousel => {
  const track = carousel.querySelector(".carousel-track");
  const leftBtn = carousel.querySelector(".carousel-btn.left");
  const rightBtn = carousel.querySelector(".carousel-btn.right");

  leftBtn.addEventListener("click", () => {
    track.scrollBy({ left: -200, behavior: "smooth" });
  });

  rightBtn.addEventListener("click", () => {
    track.scrollBy({ left: 200, behavior: "smooth" });
  });
});
