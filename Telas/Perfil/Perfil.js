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

document.getElementById("abrir-popup").addEventListener("click", (e) => {
  e.preventDefault();
  document.getElementById("popup-editar").style.display = "flex";
});

document.getElementById("fechar-popup").addEventListener("click", () => {
  document.getElementById("popup-editar").style.display = "none";
});

// Preview da imagem
document.getElementById("foto-upload").addEventListener("change", function() {
  const file = this.files[0];
  if (file) {
    document.getElementById("preview").src = URL.createObjectURL(file);
  }
});

// Seleção de moldura
document.querySelectorAll(".moldura").forEach((m, index) => {
  m.addEventListener("click", () => {
    document.querySelectorAll(".moldura").forEach(el => el.classList.remove("ativa"));
    m.classList.add("ativa");
    document.getElementById("moldura-escolhida").value = index + 1;
  });
});

