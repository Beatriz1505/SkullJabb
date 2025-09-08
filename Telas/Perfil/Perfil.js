const colorThief = new ColorThief();

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('.friend img').forEach(img => {

    if (img.complete) {
      setBorderColor(img);
    } else {
      img.addEventListener('load', () => setBorderColor(img));
    }
  });
});

function setBorderColor(img) {
  try {
    const color = colorThief.getColor(img);
    img.style.borderColor = `rgb(${color[0]}, ${color[1]}, ${color[2]})`;
  } catch (e) {
    console.log("Erro ao pegar cor:", e);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('.avatar').forEach(img => {
    
    if (img.complete) {
      setBorderColor(img);
    } else {
      img.addEventListener('load', () => setBorderColor(img));
    }
  });
});

function setBorderColor(img) {
  try {
    const color = colorThief.getColor(img);
    img.style.borderColor = `rgb(${color[0]}, ${color[1]}, ${color[2]})`;
  } catch (e) {
    console.log("Erro ao pegar cor:", e);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('.profile img').forEach(img => {
    
    if (img.complete) {
      setBorderColor(img);
    } else {
      img.addEventListener('load', () => setBorderColor(img));
    }
  });
});

function setBorderColor(img) {
  try {
    const color = colorThief.getColor(img);
    img.style.borderColor = `rgb(${color[0]}, ${color[1]}, ${color[2]})`;
  } catch (e) {
    console.log("Erro ao pegar cor:", e);
  }
}