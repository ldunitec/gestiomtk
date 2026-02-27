

@extends('adminlte::page')

@section('content_header')
    <title>Editor de Señalización Vial</title>
 
@stop

@section('content')
  
<input type="file" id="cargarImagen" accept="image/*">

<button onclick="crearSenal('flecha')">➤ Flecha</button>
<button onclick="crearSenal('recta')">— Recta</button>
<button onclick="crearSenal('peatonal')">🚶 Peatonal</button>

<button onclick="exportarImagen('png')">📷 PNG</button>
<button onclick="exportarImagen('jpg')">🖼️ JPG</button>

<div id="lienzo"></div>


@stop

@section('css')
<style>
  body {
  font-family: Arial, sans-serif;
}

#lienzo {
  position: relative;
  width: 900px;
  height: 600px;
  border: 1px solid #999;
  margin-top: 10px;
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
}

.senal {
  position: absolute;
  width: 70px;
  user-select: none;
  touch-action: none;
}

.senal svg {
  width: 100%;
  height: auto;
  transform-origin: center center;
  display: block;
}

.etiqueta {
  position: absolute;
  top: -30px;
  left: 0;
  padding: 3px 8px;
  font-size: 12px;
  font-weight: bold;
  border-radius: 10px;
  white-space: nowrap;
  cursor: move;
  outline: none;
  touch-action: none;
}

.etiqueta::after {
  content: "";
  position: absolute;
  bottom: -6px;
  left: 50%;
  transform: translateX(-50%);
  border-left: 6px solid transparent;
  border-right: 6px solid transparent;
  border-top: 6px solid;
}

/* COLORES */
.fd svg { fill: rgba(28,230,31,0.85); }
.fd .etiqueta { background: rgba(28,230,31,0.85); }
.fd .etiqueta::after { border-top-color: rgba(28,230,31,0.85); }

.ff svg { fill: rgba(28,99,230,0.85); }
.ff .etiqueta { background: rgba(28,99,230,0.85); color: #fff; }
.ff .etiqueta::after { border-top-color: rgba(28,99,230,0.85); }

.fp svg { fill: rgba(244,146,0,0.85); }
.fp .etiqueta { background: rgba(244,146,0,0.85); }
.fp .etiqueta::after { border-top-color: rgba(244,146,0,0.85); }

</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
  const lienzo = document.getElementById("lienzo");
const inputImagen = document.getElementById("cargarImagen");

/* === SVGs === */
const svgMap = {
//   flecha: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M169.4 502.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9S332.9 320 320 320l-64 0 0-160C256 71.6 184.4 0 96 0L32 0C14.3 0 0 14.3 0 32L0 96c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32l0 160-64 0c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>`,
  flecha: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path d="M297.4 73.4C309.9 60.9 330.2 60.9 342.7 73.4L470.7 201.4C479.9 210.6 482.6 224.3 477.6 236.3C472.6 248.3 460.9 256 448 256L384 256L384 416C384 504.4 312.4 576 224 576L160 576C142.3 576 128 561.7 128 544L128 480C128 462.3 142.3 448 160 448L224 448C241.7 448 256 433.7 256 416L256 256L192 256C179.1 256 167.4 248.2 162.4 236.2C157.4 224.2 160.2 210.5 169.3 201.3L297.3 73.3z"/></svg>`,
  recta: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M41.4 342.6C28.9 330.1 28.9 309.8 41.4 297.3L169.4 169.3C178.6 160.1 192.3 157.4 204.3 162.4C216.3 167.4 224 179.1 224 192L224 256L560 256C586.5 256 608 277.5 608 304L608 336C608 362.5 586.5 384 560 384L224 384L224 448C224 460.9 216.2 472.6 204.2 477.6C192.2 482.6 178.5 479.8 169.3 470.7L41.3 342.7z"/></svg>`,
  peatonal: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M136.5 88C136.5 57.1 161.6 32 192.5 32C223.4 32 248.5 57.1 248.5 88C248.5 118.9 223.4 144 192.5 144C161.6 144 136.5 118.9 136.5 88zM128.5 269.3L105.9 291.9C99.9 297.9 96.5 306 96.5 314.5L96.5 352C96.5 369.7 82.2 384 64.5 384C46.8 384 32.5 369.7 32.5 352L32.5 314.5C32.5 289 42.6 264.6 60.6 246.6L95.7 211.5C118.5 188.7 149.3 175.9 181.5 175.9C218.4 175.9 253.3 192.7 276.3 221.5L294.3 244C300.4 251.6 309.6 256 319.3 256L352.5 256C370.2 256 384.5 270.3 384.5 288C384.5 305.7 370.2 320 352.5 320L319.3 320C290.1 320 262.6 306.7 244.3 284L240.5 279.3L240.5 394.5L275 424.1C292.7 439.3 304.3 460.3 307.6 483.4L320.2 571.5C322.7 589 310.5 605.2 293 607.7C275.5 610.2 259.3 598 256.8 580.5L244.2 492.4C243.1 484.7 239.2 477.7 233.3 472.6L162 411.5C140.7 393.3 128.5 366.6 128.5 338.6L128.5 269.3zM128.6 435C131 437.3 133.4 439.6 136 441.8L182 481.2L179.8 488.8C175.3 504.5 166.9 518.8 155.4 530.3L87.1 598.6C74.6 611.1 54.3 611.1 41.8 598.6C29.3 586.1 29.3 565.8 41.8 553.3L110.1 485C113.9 481.2 116.7 476.4 118.2 471.2L128.6 435zM537.5 409C528.1 418.4 512.9 418.4 503.6 409C494.3 399.6 494.2 384.4 503.6 375.1L534.6 344.1L432.5 344.1C419.2 344.1 408.5 333.4 408.5 320.1C408.5 306.8 419.2 296.1 432.5 296.1L534.6 296.1L503.6 265.1C494.2 255.7 494.2 240.5 503.6 231.2C513 221.9 528.2 221.8 537.5 231.2L609.5 303.2C618.9 312.6 618.9 327.8 609.5 337.1L537.5 409.1z"/></svg>`
};

/* === CARGAR IMAGEN DE FONDO === */
inputImagen.addEventListener("change", e => {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = () => lienzo.style.backgroundImage = `url(${reader.result})`;
  reader.readAsDataURL(file);
});

/* === CREAR SEÑAL === */
function crearSenal(tipo) {
  const s = document.createElement("div");
  s.className = "senal";
  s.style.top = "200px";
  s.style.left = "200px";
  s.dataset.rot = 0;
  s.dataset.scale = 1;

  const etiqueta = document.createElement("div");
  etiqueta.className = "etiqueta";
  etiqueta.contentEditable = true;
//  Asignar texto inicial según el tipo
switch(tipo) {
  case "flecha":
    etiqueta.innerText = "FF1";
    break;
  case "recta":
    etiqueta.innerText = "FD1";
    break;
  case "peatonal":
    etiqueta.innerText = "FP1";
    break;
  default:
    etiqueta.innerText = "FD1";
}

  s.innerHTML = svgMap[tipo];
  s.appendChild(etiqueta);

  hacerArrastrable(s);
  habilitarRotacion(s);
  habilitarEscala(s);
  detectarTipo(etiqueta, s);

  etiqueta.addEventListener("input", () => detectarTipo(etiqueta, s));

  lienzo.appendChild(s);

/////
// 
// ]/ function crearSenal(tipo) {
//   const s = document.createElement("div");
//   s.className = "senal";
//   s.style.top = "200px";
//   s.style.left = "200px";
//   s.dataset.rot = 0;
//   s.dataset.scale = 1;

//  const etiqueta = document.createElement("div");
// etiqueta.className = "etiqueta";
// etiqueta.contentEditable = true;

// // Asignar texto inicial según el tipo
// switch(tipo) {
//   case "flecha":
//     etiqueta.innerText = "FF1";
//     break;
//   case "recta":
//     etiqueta.innerText = "FD1";
//     break;
//   case "peatonal":
//     etiqueta.innerText = "FP1";
//     break;
//   default:
//     etiqueta.innerText = "FD1";
// }

///////





}

/* === ROTAR SOLO SVG === */
function habilitarRotacion(el) {
  const svg = el.querySelector("svg");
  el.addEventListener("wheel", e => {
    e.preventDefault();
    let r = Number(el.dataset.rot);
    r += e.deltaY > 0 ? 5 : -5;
    el.dataset.rot = r;
    svg.style.transform = `rotate(${r}deg) scale(${el.dataset.scale})`;
  });
}

/* === ESCALAR SOLO SVG === */
/* === ESCALAR SOLO SVG === */
function habilitarEscala(el) {
  const svg = el.querySelector("svg");
  el.addEventListener("dblclick", e => {
    e.preventDefault();
    let s = Number(el.dataset.scale);

    // Si se mantiene Shift presionado, reducimos
    if (e.shiftKey) {
      s -= 0.1;
      if (s < 0.3) s = 0.3; // tamaño mínimo
    } else {
      s += 0.1;
      if (s > 3) s = 3; // tamaño máximo
    }

    el.dataset.scale = s;
    svg.style.transform = `rotate(${el.dataset.rot}deg) scale(${s})`;
  });
}


/* === DETECTAR TIPO FD / FF / FP === */
function detectarTipo(et, cont) {
  const t = et.innerText.toUpperCase().trim();
  cont.classList.remove("fd", "ff", "fp");
  if (/^FD(1\d|20|[1-9])/.test(t)) cont.classList.add("fd");
  else if (/^FF(1\d|20|[1-9])/.test(t)) cont.classList.add("ff");
  else if (/^FP(1\d|20|[1-9])/.test(t)) cont.classList.add("fp");
}

/* === ARRASTRAR ELEMENTO === */
/* === ARRASTRAR ELEMENTO CON ETIQUETA PEGADA === */
function hacerArrastrable(el) {
  const etiqueta = el.querySelector(".etiqueta");

  // Guardamos posición relativa de la etiqueta
  if (!el.dataset.etOffsetX) el.dataset.etOffsetX = 0;
  if (!el.dataset.etOffsetY) el.dataset.etOffsetY = 0;

  // Mover toda la señal
  el.addEventListener("mousedown", e => {
    if (e.target === etiqueta) return; // si clic en etiqueta, no mueve la señal
    const offsetX = e.clientX - el.offsetLeft;
    const offsetY = e.clientY - el.offsetTop;

    function mover(ev) {
      el.style.left = ev.clientX - offsetX + "px";
      el.style.top = ev.clientY - offsetY + "px";

      // etiqueta se mueve junto con el contenedor (manteniendo offset)
      const offX = parseFloat(el.dataset.etOffsetX);
      const offY = parseFloat(el.dataset.etOffsetY);
      etiqueta.style.left = offX + "px";
      etiqueta.style.top = offY + "px";
    }

    document.addEventListener("mousemove", mover);
    document.addEventListener("mouseup", () => {
      document.removeEventListener("mousemove", mover);
    }, { once: true });
  });

  // Mover solo la etiqueta dentro del contenedor
  etiqueta.addEventListener("mousedown", e => {
    e.stopPropagation(); // evita mover la señal
    const startX = e.clientX;
    const startY = e.clientY;
    const startLeft = parseFloat(etiqueta.style.left) || 0;
    const startTop = parseFloat(etiqueta.style.top) || 0;

    function mover(ev) {
      let x = startLeft + (ev.clientX - startX);
      let y = startTop + (ev.clientY - startY);

      // Limitar dentro del contenedor
      const maxX = el.clientWidth - etiqueta.offsetWidth;
      const maxY = el.clientHeight - etiqueta.offsetHeight;
      x = Math.max(0, Math.min(x, maxX));
      y = Math.max(0, Math.min(y, maxY));

      etiqueta.style.left = x + "px";
      etiqueta.style.top = y + "px";

      // Guardar offset relativo
      el.dataset.etOffsetX = x;
      el.dataset.etOffsetY = y;
    }

    document.addEventListener("mousemove", mover);
    document.addEventListener("mouseup", () => {
      document.removeEventListener("mousemove", mover);
    }, { once: true });
  });

  function exportarImagen(tipo) {
  const lienzo = document.getElementById("lienzo");

  html2canvas(lienzo, {
    backgroundColor: null, // respeta fondo si es imagen
    scale: 2 // mejor calidad
  }).then(canvas => {
    let enlace = document.createElement("a");

    if (tipo === "jpg") {
      enlace.href = canvas.toDataURL("image/jpeg", 0.95);
      enlace.download = "senalizacion.jpg";
    } else {
      enlace.href = canvas.toDataURL("image/png");
      enlace.download = "senalizacion.png";
    }

    enlace.click();
  });
}

}

function exportarImagen(tipo) {
  const lienzo = document.getElementById("lienzo");

  html2canvas(lienzo, {
    backgroundColor: null, // respeta fondo si es imagen
    scale: 2 // mejor calidad
  }).then(canvas => {
    let enlace = document.createElement("a");

    if (tipo === "jpg") {
      enlace.href = canvas.toDataURL("image/jpeg", 0.95);
      enlace.download = "senalizacion.jpg";
    } else {
      enlace.href = canvas.toDataURL("image/png");
      enlace.download = "senalizacion.png";
    }

    enlace.click();
  });
}



</script>
@stop
