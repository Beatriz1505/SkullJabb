const colorThief = new ColorThief();
const colorCache = {}; 

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('.profile img, .friend img').forEach(img => {
    if (img.complete) {
      applyBorder(img);
    } else {
      img.addEventListener('load', () => applyBorder(img));
    }
  });
});

function applyBorder(img) {
  if (colorCache[img.src]) {
    
    img.style.borderColor = colorCache[img.src];
  } else {
    try {
      const color = colorThief.getColor(img, 10); 
      const rgb = `rgb(${color[0]}, ${color[1]}, ${color[2]})`;
      colorCache[img.src] = rgb;
      img.style.borderColor = rgb;
    } catch (e) {
      console.log("Erro ao pegar cor:", e);
    }
  }
}
